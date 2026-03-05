<?php

namespace App\Http\Controllers;

use App\Models\Resume;
use App\Models\Project;
use App\Models\Skill;
use App\Services\ResumeAnalyzer;
use App\Services\AiResumeWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class ResumeController extends Controller
{
    protected ResumeAnalyzer $analyzer;
    protected AiResumeWriter $aiWriter;

    public function __construct(ResumeAnalyzer $analyzer, AiResumeWriter $aiWriter)
    {
        $this->analyzer = $analyzer;
        $this->aiWriter = $aiWriter;
    }

    // =========================================================================
    // Index
    // =========================================================================

    public function index()
    {
        $resumes = auth()->user()->resumes()->latest()->paginate(10);

        return view('resume.index', compact('resumes'));
    }

    // =========================================================================
    // Create form
    // =========================================================================

    public function create()
    {
        $projects = auth()->user()->projects;
        $skills   = Skill::orderBy('category')->orderBy('name')->get();

        return view('resume.create', compact('projects', 'skills'));
    }

    // =========================================================================
    // Store — validates, saves, calls AI, generates PDF
    // =========================================================================

    public function store(Request $request)
    {
        $validated = $request->validate([
            // Core role fields
            'job_title'              => 'required|string|max:255',
            'job_description'        => 'required|string',
            'target_keywords'        => 'nullable|string|max:500',
            'template'               => 'nullable|string|in:modern,classic,minimal,creative,executive,tech',
            'tone'                   => 'nullable|string|in:professional,creative,executive,concise',

            // Contact / personal
            'phone'                  => 'nullable|string|max:50',
            'location'               => 'nullable|string|max:150',
            'linked_in'              => 'nullable|url|max:255',
            'github_url'             => 'nullable|url|max:255',

            // Rich input
            'work_experience'        => 'nullable|string',
            'education_details'      => 'nullable|string',
            'certifications'         => 'nullable|string',
            'spoken_languages'       => 'nullable|string|max:255',
            'bio_seed'               => 'nullable|string',

            // Projects & skills
            'selected_project_ids'   => 'nullable|array',
            'selected_project_ids.*' => 'exists:projects,id',
            'selected_skill_ids'     => 'nullable|array',
            'selected_skill_ids.*'   => 'exists:skills,id',
        ]);

        $user     = auth()->user();
        $keywords = $this->analyzer->extractKeywords($validated['job_description']);

        // Merge user-supplied target keywords with auto-extracted ones
        if (!empty($validated['target_keywords'])) {
            $manualKeywords = array_map('trim', explode(',', $validated['target_keywords']));
            $keywords       = array_values(array_unique(array_merge($keywords, $manualKeywords)));
        }

        $matchScore = $this->analyzer->calculateMatchScore($user, $keywords);

        $resume = $user->resumes()->create([
            // Core
            'job_title'            => $validated['job_title'],
            'job_description'      => $validated['job_description'],
            'target_keywords'      => implode(', ', $keywords),
            'match_score'          => $matchScore,
            'template'             => $validated['template']  ?? 'modern',
            'tone'                 => $validated['tone']      ?? 'professional',

            // Contact
            'phone'                => $validated['phone']     ?? null,
            'location'             => $validated['location']  ?? null,
            'linked_in'            => $validated['linked_in'] ?? null,
            'github_url'           => $validated['github_url'] ?? null,

            // Rich input
            'work_experience'      => $validated['work_experience']   ?? null,
            'education_details'    => $validated['education_details'] ?? null,
            'certifications'       => $validated['certifications']    ?? null,
            'spoken_languages'     => $validated['spoken_languages']  ?? null,
            'bio_seed'             => $validated['bio_seed']          ?? null,

            // Selections
            'selected_project_ids' => $validated['selected_project_ids'] ?? [],
            'selected_skill_ids'   => $validated['selected_skill_ids']   ?? [],
        ]);

        // Fetch the actual Eloquent models for the AI writer
        $projects   = Project::with('skills')
            ->whereIn('id', $validated['selected_project_ids'] ?? [])
            ->get();
        $skills     = Skill::whereIn('id', $validated['selected_skill_ids'] ?? [])->get();

        // Generate AI resume content (9 fields via Gemini responseSchema)
        $resumeData = $this->aiWriter->generate($user, $resume, $projects, $skills);
        $resume->update(['resume_data' => $resumeData]);

        // Render PDF
        $pdfPath = $this->renderPdf($resume, $resumeData, $user, $projects, $skills);
        $resume->update(['pdf_path' => $pdfPath]);

        return redirect()->route('resume.show', $resume)
            ->with('success', 'Resume generated successfully!');
    }

    // =========================================================================
    // Show
    // =========================================================================

    public function show(Resume $resume)
    {
        $this->authorizeResume($resume);

        $projects   = $resume->getSelectedProjects();
        $skills     = $resume->getSelectedSkills();
        $resumeData = $this->resolveResumeData($resume);

        return view('resume.show', compact('resume', 'projects', 'skills', 'resumeData'));
    }

    // =========================================================================
    // Edit form
    // =========================================================================

    public function edit(Resume $resume)
    {
        $this->authorizeResume($resume);

        $projects = auth()->user()->projects;
        $skills   = Skill::orderBy('category')->orderBy('name')->get();

        return view('resume.edit', compact('resume', 'projects', 'skills'));
    }

    // =========================================================================
    // Update — re-validates all fields, recalculates score
    // =========================================================================

    public function update(Request $request, Resume $resume)
    {
        $this->authorizeResume($resume);

        $validated = $request->validate([
            // Core
            'job_title'              => 'sometimes|string|max:255',
            'job_description'        => 'sometimes|string',
            'target_keywords'        => 'nullable|string|max:500',
            'template'               => 'nullable|string|in:modern,classic,minimal,creative,executive,tech',
            'tone'                   => 'nullable|string|in:professional,creative,executive,concise',

            // Contact
            'phone'                  => 'nullable|string|max:50',
            'location'               => 'nullable|string|max:150',
            'linked_in'              => 'nullable|url|max:255',
            'github_url'             => 'nullable|url|max:255',

            // Rich input
            'work_experience'        => 'nullable|string',
            'education_details'      => 'nullable|string',
            'certifications'         => 'nullable|string',
            'spoken_languages'       => 'nullable|string|max:255',
            'bio_seed'               => 'nullable|string',

            // Selections
            'selected_project_ids'   => 'sometimes|array',
            'selected_project_ids.*' => 'exists:projects,id',
            'selected_skill_ids'     => 'sometimes|array',
            'selected_skill_ids.*'   => 'exists:skills,id',
        ]);

        // Recalculate keyword match whenever the job description changes
        if (isset($validated['job_description'])) {
            $keywords = $this->analyzer->extractKeywords($validated['job_description']);

            if (!empty($validated['target_keywords'])) {
                $manualKeywords = array_map('trim', explode(',', $validated['target_keywords']));
                $keywords       = array_values(array_unique(array_merge($keywords, $manualKeywords)));
            }

            $validated['target_keywords'] = implode(', ', $keywords);
            $validated['match_score']     = $this->analyzer->calculateMatchScore(auth()->user(), $keywords);
        }

        $resume->update($validated);

        return redirect()->route('resume.show', $resume)
            ->with('success', 'Resume updated. Use "Regenerate PDF" to rebuild the AI content.');
    }

    // =========================================================================
    // Regenerate PDF (standalone action — re-runs AI + PDF render)
    // =========================================================================

    public function generate(Request $request)
    {
        // Accept resume_id posted from the show page button
        $resumeId = $request->input('resume_id');
        $resume   = Resume::findOrFail($resumeId);

        $this->authorizeResume($resume);

        $user     = auth()->user();
        $projects = $resume->getSelectedProjects()->load('skills');
        $skills   = $resume->getSelectedSkills();

        // Always re-run AI so new fields (work_experience, education_details, etc.)
        // are included in the fresh output
        $resumeData = $this->aiWriter->generate($user, $resume, $projects, $skills);
        $resume->update(['resume_data' => $resumeData]);

        $pdfPath = $this->renderPdf($resume, $resumeData, $user, $projects, $skills);
        $resume->update(['pdf_path' => $pdfPath]);

        return redirect()->route('resume.show', $resume)
            ->with('success', 'Resume regenerated successfully!');
    }

    // =========================================================================
    // Analyze (AJAX)
    // =========================================================================

    public function analyze(Request $request)
    {
        $validated = $request->validate([
            'job_description' => 'required|string',
        ]);

        $user           = auth()->user();
        $keywords       = $this->analyzer->extractKeywords($validated['job_description']);
        $rankedProjects = $this->analyzer->rankProjects($user->projects, $keywords);
        $matchScore     = $this->analyzer->calculateMatchScore($user, $keywords);

        return response()->json([
            'success'     => true,
            'keywords'    => $keywords,
            'projects'    => $rankedProjects->map(fn($p) => [
                'id'              => $p->id,
                'name'            => $p->name,
                'description'     => $p->description,
                'relevance_score' => $p->relevance_score ?? 0,
                'skills'          => $p->skills->pluck('name'),
            ]),
            'match_score' => $matchScore,
        ]);
    }

    // =========================================================================
    // Download
    // =========================================================================

    public function download(Resume $resume)
    {
        $this->authorizeResume($resume);

        if (!$resume->pdf_path) {
            return redirect()->back()->with('error', 'No PDF has been generated yet.');
        }

        // Regenerate on-the-fly if the file was deleted from storage
        if (!Storage::disk('local')->exists($resume->pdf_path)) {
            $projects   = $resume->getSelectedProjects()->load('skills');
            $skills     = $resume->getSelectedSkills();
            $resumeData = $this->resolveResumeData($resume);
            $pdfPath    = $this->renderPdf($resume, $resumeData, $resume->user, $projects, $skills);
            $resume->update(['pdf_path' => $pdfPath]);
        }

        return Storage::disk('local')->download(
            $resume->pdf_path,
            'resume_' . str()->slug($resume->job_title) . '.pdf'
        );
    }

    // =========================================================================
    // Destroy
    // =========================================================================

    public function destroy(Resume $resume)
    {
        $this->authorizeResume($resume);

        if ($resume->pdf_path && Storage::disk('local')->exists($resume->pdf_path)) {
            Storage::disk('local')->delete($resume->pdf_path);
        }
        $resume->delete();

        return redirect()->route('resume.index')
            ->with('success', 'Resume deleted.');
    }

    // =========================================================================
    // Private helpers
    // =========================================================================

    private function authorizeResume(Resume $resume): void
    {
        abort_if($resume->user_id !== auth()->id(), 403);
    }

    /**
     * Return stored resume_data as an array, or build a minimal fallback.
     * Handles both the old 5-field format and new 9-field format gracefully.
     */
    private function resolveResumeData(Resume $resume): array
    {
        if (!empty($resume->resume_data)) {
            $data = is_array($resume->resume_data)
                ? $resume->resume_data
                : json_decode($resume->resume_data, true);

            if (is_array($data) && !empty($data)) {
                // Back-fill keys missing from older 5-field records
                $data = array_merge([
                    'headline'       => '',
                    'certifications' => '',
                    'languages'      => '',
                    'achievements'   => '',
                ], $data);

                return $this->sanitiseResumeData($data);
            }
        }

        // Minimal fallback (no AI data stored yet)
        $projects = $resume->getSelectedProjects();
        $skills   = $resume->getSelectedSkills();

        return $this->sanitiseResumeData([
            'headline'       => $resume->job_title,
            'summary'        => "Tailored resume for {$resume->job_title}.",
            'skills'         => $skills->pluck('name')->join(', '),
            'experience'     => $resume->work_experience ?? '',
            'projects'       => $projects->map(fn($p) => "• {$p->name}: {$p->description}")->join("\n"),
            'education'      => $resume->education_details ?? '',
            'certifications' => $resume->certifications ?? '',
            'languages'      => $resume->spoken_languages ?? '',
            'achievements'   => '',
        ]);
    }

    /**
     * Guarantee every resume field is a plain string.
     *
     * Gemini occasionally returns `skills` (or other fields) as a JSON array
     * instead of a comma-separated string, which breaks any blade template
     * that calls explode() or other string functions on the value.
     */
    private function sanitiseResumeData(array $data): array
    {
        $stringKeys = [
            'headline', 'summary', 'skills', 'experience',
            'projects', 'education', 'certifications', 'languages', 'achievements',
        ];

        foreach ($stringKeys as $key) {
            if (!isset($data[$key])) {
                $data[$key] = '';
                continue;
            }

            $value = $data[$key];

            if (is_array($value)) {
                // Flat sequential array (e.g. ["PHP","Laravel"]) → comma-separated string
                // Associative / nested array → JSON-encode as a last resort
                $isFlat = array_keys($value) === range(0, count($value) - 1)
                          && count(array_filter($value, 'is_array')) === 0;

                $data[$key] = $isFlat ? implode(', ', $value) : json_encode($value);

            } elseif (!is_string($value)) {
                $data[$key] = (string) $value;
            }
        }

        return $data;
    }

    /**
     * Render the PDF blade view and persist it to local storage.
     */
    private function renderPdf(Resume $resume, array $resumeData, $user, $projects = null, $skills = null): string
    {
        $projects = $projects ?? $resume->getSelectedProjects();
        $skills   = $skills   ?? $resume->getSelectedSkills();

        $pdf = Pdf::loadView('resume.pdf', compact('resume', 'resumeData', 'user', 'projects', 'skills'))
                  ->setPaper('a4', 'portrait');

        $path = 'resume/' . $resume->id . '/resume-' . str()->slug($resume->job_title) . '-' . $resume->id . '.pdf';

        Storage::disk('local')->put($path, $pdf->output());

        return $path;
    }
}
