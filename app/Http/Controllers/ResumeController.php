<?php

namespace App\Http\Controllers;

use App\Models\Resume;
use App\Models\Project;
use App\Models\Skill;
use App\Services\ResumeAnalyzer;
use App\Services\AiResumeWriter;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class ResumeController extends Controller
{
    public function __construct(
        protected ResumeAnalyzer    $analyzer,
        protected AiResumeWriter    $aiWriter,
        protected CloudinaryService $cloudinary,
    ) {}

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
    // Store
    // =========================================================================

    public function store(Request $request)
    {
        $validated = $request->validate([
            'job_title'              => 'required|string|max:255',
            'job_description'        => 'required|string',
            'target_keywords'        => 'nullable|string|max:500',
            'template'               => 'nullable|string|in:modern,classic,minimal,creative,executive,tech',
            'tone'                   => 'nullable|string|in:professional,creative,executive,concise',
            'phone'                  => 'nullable|string|max:50',
            'location'               => 'nullable|string|max:150',
            'linked_in'              => 'nullable|url|max:255',
            'github_url'             => 'nullable|url|max:255',
            'work_experience'        => 'nullable|string',
            'education_details'      => 'nullable|string',
            'certifications'         => 'nullable|string',
            'spoken_languages'       => 'nullable|string|max:255',
            'bio_seed'               => 'nullable|string',
            'selected_project_ids'   => 'nullable|array',
            'selected_project_ids.*' => 'exists:projects,id',
            'selected_skill_ids'     => 'nullable|array',
            'selected_skill_ids.*'   => 'exists:skills,id',
        ]);

        $user     = auth()->user();
        $keywords = $this->analyzer->extractKeywords($validated['job_description']);

        if (!empty($validated['target_keywords'])) {
            $manualKeywords = array_map('trim', explode(',', $validated['target_keywords']));
            $keywords       = array_values(array_unique(array_merge($keywords, $manualKeywords)));
        }

        $matchScore = $this->analyzer->calculateMatchScore($user, $keywords);

        $resume = $user->resumes()->create([
            'job_title'            => $validated['job_title'],
            'job_description'      => $validated['job_description'],
            'target_keywords'      => implode(', ', $keywords),
            'match_score'          => $matchScore,
            'template'             => $validated['template']          ?? 'modern',
            'tone'                 => $validated['tone']              ?? 'professional',
            'phone'                => $validated['phone']             ?? null,
            'location'             => $validated['location']          ?? null,
            'linked_in'            => $validated['linked_in']         ?? null,
            'github_url'           => $validated['github_url']        ?? null,
            'work_experience'      => $validated['work_experience']   ?? null,
            'education_details'    => $validated['education_details'] ?? null,
            'certifications'       => $validated['certifications']    ?? null,
            'spoken_languages'     => $validated['spoken_languages']  ?? null,
            'bio_seed'             => $validated['bio_seed']          ?? null,
            'selected_project_ids' => $validated['selected_project_ids'] ?? [],
            'selected_skill_ids'   => $validated['selected_skill_ids']   ?? [],
        ]);

        $projects   = Project::with('skills')
            ->whereIn('id', $validated['selected_project_ids'] ?? [])
            ->get();
        $skills     = Skill::whereIn('id', $validated['selected_skill_ids'] ?? [])->get();

        $resumeData = $this->aiWriter->generate($user, $resume, $projects, $skills);
        $resume->update(['resume_data' => $resumeData]);

        $pdfUrl = $this->renderAndUploadPdf($resume, $resumeData, $user, $projects, $skills);
        $resume->update(['pdf_path' => $pdfUrl]);

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
    // Update
    // =========================================================================

    public function update(Request $request, Resume $resume)
    {
        $this->authorizeResume($resume);

        $validated = $request->validate([
            'job_title'              => 'sometimes|string|max:255',
            'job_description'        => 'sometimes|string',
            'target_keywords'        => 'nullable|string|max:500',
            'template'               => 'nullable|string|in:modern,classic,minimal,creative,executive,tech',
            'tone'                   => 'nullable|string|in:professional,creative,executive,concise',
            'phone'                  => 'nullable|string|max:50',
            'location'               => 'nullable|string|max:150',
            'linked_in'              => 'nullable|url|max:255',
            'github_url'             => 'nullable|url|max:255',
            'work_experience'        => 'nullable|string',
            'education_details'      => 'nullable|string',
            'certifications'         => 'nullable|string',
            'spoken_languages'       => 'nullable|string|max:255',
            'bio_seed'               => 'nullable|string',
            'selected_project_ids'   => 'sometimes|array',
            'selected_project_ids.*' => 'exists:projects,id',
            'selected_skill_ids'     => 'sometimes|array',
            'selected_skill_ids.*'   => 'exists:skills,id',
        ]);

        $resume->update($validated);

        if (isset($validated['job_description'])) {
            $keywords   = $this->analyzer->extractKeywords($validated['job_description']);
            $matchScore = $this->analyzer->calculateMatchScore(auth()->user(), $keywords);
            $resume->update([
                'target_keywords' => implode(', ', $keywords),
                'match_score'     => $matchScore,
            ]);
        }

        return redirect()->route('resume.show', $resume)
            ->with('success', 'Resume updated successfully!');
    }

    // =========================================================================
    // Generate / Regenerate PDF
    // =========================================================================

    public function generate(Request $request)
    {
        $resumeId = $request->input('resume_id') ?? $request->route('resume');
        $resume   = Resume::findOrFail($resumeId instanceof Resume ? $resumeId->id : $resumeId);

        $this->authorizeResume($resume);

        $user       = auth()->user();
        $projects   = $resume->getSelectedProjects()->load('skills');
        $skills     = $resume->getSelectedSkills();
        $resumeData = $this->aiWriter->generate($user, $resume, $projects, $skills);
        $resume->update(['resume_data' => $resumeData]);

        $pdfUrl = $this->renderAndUploadPdf($resume, $resumeData, $user, $projects, $skills);
        $resume->update(['pdf_path' => $pdfUrl]);

        return redirect()->route('resume.show', $resume)
            ->with('success', 'Resume regenerated successfully!');
    }

    // =========================================================================
    // Download — redirects to Cloudinary URL
    // =========================================================================

    public function download(Resume $resume)
    {
        $this->authorizeResume($resume);

        if (!$resume->pdf_path) {
            return redirect()->back()->with('error', 'No PDF has been generated yet.');
        }

        // Cloudinary URL — redirect directly, no local file needed
        if (str_contains($resume->pdf_path, 'cloudinary.com')) {
            return redirect($resume->pdf_path);
        }

        // Legacy local file fallback
        if (!Storage::disk('local')->exists($resume->pdf_path)) {
            $projects   = $resume->getSelectedProjects()->load('skills');
            $skills     = $resume->getSelectedSkills();
            $resumeData = $this->resolveResumeData($resume);
            $pdfUrl     = $this->renderAndUploadPdf($resume, $resumeData, $resume->user, $projects, $skills);
            $resume->update(['pdf_path' => $pdfUrl]);
            return redirect($pdfUrl);
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

        if ($resume->pdf_path && str_contains($resume->pdf_path, 'cloudinary.com')) {
            $this->cloudinary->deleteByUrl($resume->pdf_path, 'raw');
        } elseif ($resume->pdf_path && Storage::disk('local')->exists($resume->pdf_path)) {
            Storage::disk('local')->delete($resume->pdf_path);
        }

        $resume->delete();

        return redirect()->route('resume.index')->with('success', 'Resume deleted.');
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
    // Private helpers
    // =========================================================================

    private function authorizeResume(Resume $resume): void
    {
        abort_if($resume->user_id !== auth()->id(), 403);
    }

    private function resolveResumeData(Resume $resume): array
    {
        if (!empty($resume->resume_data)) {
            $data = is_array($resume->resume_data)
                ? $resume->resume_data
                : json_decode($resume->resume_data, true);

            if (is_array($data) && !empty($data)) {
                $data = array_merge([
                    'headline'       => '',
                    'certifications' => '',
                    'languages'      => '',
                    'achievements'   => '',
                ], $data);

                return $this->sanitiseResumeData($data);
            }
        }

        $projects = $resume->getSelectedProjects();
        $skills   = $resume->getSelectedSkills();

        return $this->sanitiseResumeData([
            'headline'       => $resume->job_title,
            'summary'        => "Tailored resume for {$resume->job_title}.",
            'skills'         => $skills->pluck('name')->join(', '),
            'experience'     => $resume->work_experience   ?? '',
            'projects'       => $projects->map(fn($p) => "• {$p->name}: {$p->description}")->join("\n"),
            'education'      => $resume->education_details ?? '',
            'certifications' => $resume->certifications    ?? '',
            'languages'      => $resume->spoken_languages  ?? '',
            'achievements'   => '',
        ]);
    }

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
                $isFlat     = array_keys($value) === range(0, count($value) - 1)
                              && count(array_filter($value, 'is_array')) === 0;
                $data[$key] = $isFlat ? implode(', ', $value) : json_encode($value);
            } elseif (!is_string($value)) {
                $data[$key] = (string) $value;
            }
        }

        return $data;
    }

    /**
     * Render the PDF and upload it to Cloudinary.
     * Returns the Cloudinary secure URL stored as pdf_path.
     */
    private function renderAndUploadPdf(Resume $resume, array $resumeData, $user, $projects = null, $skills = null): string
    {
        $projects = $projects ?? $resume->getSelectedProjects();
        $skills   = $skills   ?? $resume->getSelectedSkills();

        $pdf = Pdf::loadView('resume.pdf', compact('resume', 'resumeData', 'user', 'projects', 'skills'))
                  ->setPaper('a4', 'portrait');

        return $this->cloudinary->uploadResumePdf(
            $pdf->output(),
            $resume->id,
            $resume->job_title
        );
    }
}
