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
use Gemini\Laravel\Facades\Gemini;

class ResumeController extends Controller
{
    protected $analyzer;
    protected $aiWriter;

    public function __construct(ResumeAnalyzer $analyzer, AiResumeWriter $aiWriter)
    {
        $this->analyzer = $analyzer;
        $this->aiWriter = $aiWriter;
    }


    // -------------------------------------------------------------------------
    // Index
    // -------------------------------------------------------------------------

    public function index()
    {
        $user    = auth()->user();
        $resumes = $user->resumes()->latest()->paginate(10);

        return view('resume.index', compact('resumes'));
    }

    // -------------------------------------------------------------------------
    // Create form
    // -------------------------------------------------------------------------

    public function create()
    {
        // Projects are user-owned; skills are global (no user_id on skills table)
        $projects = auth()->user()->projects;
        $skills   = Skill::orderBy('category')->orderBy('name')->get();

        return view('resume.create', compact('projects', 'skills'));
    }

    // -------------------------------------------------------------------------
    // Show
    // -------------------------------------------------------------------------

    public function show(Resume $resume)
    {
        $this->authorizeResume($resume);

        $projects   = $resume->getSelectedProjects();
        $skills     = $resume->getSelectedSkills();
        $resumeData = $this->buildResumeDataFromRecord($resume);

        return view('resume.show', compact('resume', 'projects', 'skills', 'resumeData'));
    }

    // -------------------------------------------------------------------------
    // Analyze (AJAX) — unchanged from your original
    // -------------------------------------------------------------------------

    public function analyze(Request $request)
    {
        $validated = $request->validate([
            'job_description' => 'required|string',
        ]);

        $user    = auth()->user();
        $keywords = $this->analyzer->extractKeywords($validated['job_description']);
        $rankedProjects = $this->analyzer->rankProjects($user->projects, $keywords);
        $matchScore     = $this->analyzer->calculateMatchScore($user, $keywords);

        return response()->json([
            'success'     => true,
            'keywords'    => $keywords,
            'projects'    => $rankedProjects->map(fn($project) => [
                'id'              => $project->id,
                'name'            => $project->name,
                'description'     => $project->description,
                'relevance_score' => $project->relevance_score ?? 0,
                'skills'          => $project->skills->pluck('name'),
            ]),
            'match_score' => $matchScore,
        ]);
    }

    // -------------------------------------------------------------------------
    // Store — saves record, then calls AI to generate content + PDF
    // -------------------------------------------------------------------------

    public function store(Request $request)
    {
        $validated = $request->validate([
            'job_title'              => 'required|string|max:255',
            'job_description'        => 'required|string',
            'selected_project_ids'   => 'required|array',
            'selected_project_ids.*' => 'exists:projects,id',
            'selected_skill_ids'     => 'nullable|array',
            'selected_skill_ids.*'   => 'exists:skills,id',
            'template'               => 'nullable|string|in:modern,classic,minimal,creative,executive,tech',
        ]);

        $user       = auth()->user();
        $keywords   = $this->analyzer->extractKeywords($validated['job_description']);
        $matchScore = $this->analyzer->calculateMatchScore($user, $keywords);

        $resume = $user->resumes()->create([
            'job_title'            => $validated['job_title'],
            'job_description'      => $validated['job_description'],
            'selected_project_ids' => $validated['selected_project_ids'],
            'selected_skill_ids'   => $validated['selected_skill_ids'] ?? [],
            'target_keywords'      => implode(', ', $keywords),
            'match_score'          => $matchScore,
            'template'             => $validated['template'] ?? 'modern',
        ]);

        $projects   = Project::whereIn('id', $validated['selected_project_ids'])->get();
        $skills     = Skill::whereIn('id', $validated['selected_skill_ids'] ?? [])->get();
        $resumeData = $this->aiWriter->generate($user, $resume, $projects, $skills);

        // Store AI sections back into the record
        if (in_array('resume_data', $resume->getFillable())) {
            $resume->update(['resume_data' => $resumeData]);
        }

        // Generate PDF — pass $projects and $skills so templates can use them
        $pdfPath = $this->renderPdf($resume, $resumeData, $user, $projects, $skills);
        $resume->update(['pdf_path' => $pdfPath]);

        return redirect()->route('resume.show', $resume)
            ->with('success', 'Resume generated successfully!');
    }

    // -------------------------------------------------------------------------
    // Update — unchanged from your original, recalculates score if needed
    // -------------------------------------------------------------------------

    public function update(Request $request, Resume $resume)
    {
        $this->authorizeResume($resume);

        $validated = $request->validate([
            'job_title'              => 'sometimes|string|max:255',
            'job_description'        => 'sometimes|string',
            'target_keywords'        => 'nullable|string',
            'template'               => 'nullable|string|in:modern,classic,minimal,creative,executive,tech',
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

    // -------------------------------------------------------------------------
    // Generate PDF (standalone action, e.g. re-generate button)
    // -------------------------------------------------------------------------
        public function edit(Resume $resume)
    {
        $this->authorizeResume($resume);

        $projects = auth()->user()->projects;
        $skills   = Skill::orderBy('category')->orderBy('name')->get();

        return view('resume.edit', compact('resume', 'projects', 'skills'));
    }

    public function generate(Resume $resume)
    {
        $this->authorizeResume($resume);

        $projects   = $resume->getSelectedProjects();
        $skills     = $resume->getSelectedSkills();
        $resumeData = $this->buildResumeDataFromRecord($resume);
        $pdfPath    = $this->renderPdf($resume, $resumeData, auth()->user(), $projects, $skills);
        $resume->update(['pdf_path' => $pdfPath]);

        return redirect()->route('resume.show', $resume)
            ->with('success', 'Resume PDF generated successfully!');
    }

    // -------------------------------------------------------------------------
    // Download — your original logic preserved
    // -------------------------------------------------------------------------

    public function download(Resume $resume)
    {
        $this->authorizeResume($resume);

        if (!$resume->pdf_path) {
            return redirect()->back()->with('error', 'Resume PDF has not been generated yet.');
        }

        if (!Storage::disk('local')->exists($resume->pdf_path)) {
            $projects   = $resume->getSelectedProjects();
            $skills     = $resume->getSelectedSkills();
            $resumeData = $this->buildResumeDataFromRecord($resume);
            $pdfPath    = $this->renderPdf($resume, $resumeData, $resume->user, $projects, $skills);
            $resume->update(['pdf_path' => $pdfPath]);
        }

        return Storage::disk('local')->download(
            $resume->pdf_path,
            'resume_' . str()->slug($resume->job_title) . '.pdf'
        );
    }

    // -------------------------------------------------------------------------
    // Destroy
    // -------------------------------------------------------------------------

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

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function authorizeResume(Resume $resume): void
    {
        abort_if($resume->user_id !== auth()->id(), 403);
    }

    /**
     * Build resumeData from stored record fields (used when AI data isn't separately stored).
     */
    
    private function buildResumeDataFromRecord(Resume $resume): array
    {
        // If resume_data JSON column exists and is populated, use it directly
        if (!empty($resume->resume_data)) {
            return is_array($resume->resume_data)
                ? $resume->resume_data
                : json_decode($resume->resume_data, true);
        }

        // Fallback: build from related models
        $projects = $resume->getSelectedProjects();
        $skills   = $resume->getSelectedSkills();

        return [
            'summary'    => "Tailored resume for {$resume->job_title}.",
            'skills'     => $skills->pluck('name')->join(', '),
            'projects'   => $projects->map(fn($p) => "**{$p->name}**: {$p->description}")->join("\n\n"),
            'experience' => '',
            'education'  => '',
        ];
    }

    /**
     * Render the PDF blade view and save to storage.
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