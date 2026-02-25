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
            'template'               => 'nullable|string|in:modern,classic,minimal,creative',
        ]);

        $user     = auth()->user();
        $keywords = $this->analyzer->extractKeywords($validated['job_description']);
        $matchScore = $this->analyzer->calculateMatchScore($user, $keywords);

        // Create the resume record first (your original logic)
        $resume = $user->resumes()->create([
            'job_title'            => $validated['job_title'],
            'job_description'      => $validated['job_description'],
            'selected_project_ids' => $validated['selected_project_ids'],
            'selected_skill_ids'   => $validated['selected_skill_ids'] ?? [],
            'target_keywords'      => implode(', ', $keywords),
            'match_score'          => $matchScore,
            'template'             => $validated['template'] ?? 'modern',
        ]);

        // Now generate AI content and PDF
        $projects   = Project::whereIn('id', $validated['selected_project_ids'])->get();
        $skills     = Skill::whereIn('id', $validated['selected_skill_ids'] ?? [])->get();
        $resumeData = $this->aiWriter->generate(
            $user,
            $resume,
            $projects,
            $skills
        );

        // Store AI sections back into the record if column exists
        if (in_array('resume_data', $resume->getFillable())) {
            $resume->update(['resume_data' => $resumeData]);
        }

        // Generate PDF
        $pdfPath = $this->renderPdf($resume, $resumeData, $user);
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

    public function generate(Resume $resume)
    {
        $this->authorizeResume($resume);

        $resumeData = $this->buildResumeDataFromRecord($resume);
        $pdfPath    = $this->renderPdf($resume, $resumeData, auth()->user());
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

        $fullPath = storage_path('app/' . $resume->pdf_path);

        if (!file_exists($fullPath)) {
            // Attempt to regenerate on the fly
            $resumeData = $this->buildResumeDataFromRecord($resume);
            $pdfPath    = $this->renderPdf($resume, $resumeData, $resume->user);
            $resume->update(['pdf_path' => $pdfPath]);
            $fullPath = storage_path('app/' . $pdfPath);
        }

        return response()->download($fullPath, 'resume_' . str()->slug($resume->job_title) . '.pdf');
    }

    // -------------------------------------------------------------------------
    // Destroy
    // -------------------------------------------------------------------------

    public function destroy(Resume $resume)
    {
        $this->authorizeResume($resume);

        if ($resume->pdf_path && file_exists(storage_path('app/' . $resume->pdf_path))) {
            unlink(storage_path('app/' . $resume->pdf_path));
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
     * Call Claude AI and return parsed resume sections.
     */
//     private function generateWithAI($user, Resume $resume, $projects, $skills): array
//     {
//         $projectsList = $projects->map(fn($p) => "- {$p->name}: {$p->description}")->join("\n");
//         $skillsList   = $skills->map(fn($s) => $s->name)->join(', ');

//         $prompt = <<<PROMPT
// You are an expert resume writer. Generate a professional, ATS-optimised resume for the following candidate.

// ## Candidate Information
// Name: {$user->name}
// Email: {$user->email}

// ## Target Role
// Job Title: {$resume->job_title}

// ## Job Description
// {$resume->job_description}

// ## Target Keywords to Include
// {$resume->target_keywords}

// ## Selected Projects
// {$projectsList}

// ## Selected Skills
// {$skillsList}

// ## Instructions
// Write a complete resume with the following clearly labelled sections:
// 1. PROFESSIONAL_SUMMARY — 3-4 sentences tailored to the job description
// 2. SKILLS — a comma-separated list of relevant technical and soft skills
// 3. PROJECTS — for each project write a title and 2-3 bullet points highlighting impact and tech used
// 4. EXPERIENCE — if no work history is provided, omit this section
// 5. EDUCATION — placeholder if unknown

// Use action verbs. Incorporate the target keywords naturally. Keep language concise and impactful.

// Return ONLY the resume content, clearly separated by the section labels above (e.g. "PROFESSIONAL_SUMMARY:", "SKILLS:", etc.). No extra commentary.
// PROMPT;

//         $response = Gemini::generativeModel('gemini-1.5-flash')
//             ->generateContent($prompt);
//         try {

//             $response = Gemini::generativeModel('gemini-1.5-flash')
//                 ->generateContent($prompt);

//             $text = trim($response->text() ?? '');

//             if (!$text) {
//                 throw new \Exception('Empty AI response');
//             }

//             return $this->parseAiResponse($text);

//         } catch (\Throwable $e) {

//             logger()->error('Gemini resume generation failed', [
//                 'error' => $e->getMessage()
//             ]);

//             // graceful fallback
//             return [
//                 'summary' => 'Professional summary unavailable.',
//                 'skills' => $skills->pluck('name')->join(', '),
//                 'projects' => $projects->pluck('description')->join("\n"),
//                 'experience' => '',
//                 'education' => '',
//             ];
//         }
//     }

//     /**
//      * Parse raw AI text into named sections.
//      */
//     private function parseAiResponse(string $text): array
//     {
//         $sections = [
//             'summary'    => '',
//             'skills'     => '',
//             'projects'   => '',
//             'experience' => '',
//             'education'  => '',
//         ];

//         $map = [
//             'PROFESSIONAL_SUMMARY' => 'summary',
//             'SKILLS'               => 'skills',
//             'PROJECTS'             => 'projects',
//             'EXPERIENCE'           => 'experience',
//             'EDUCATION'            => 'education',
//         ];

//         $pattern = '/(' . implode('|', array_keys($map)) . '):\s*/';
//         $parts   = preg_split($pattern, $text, -1, PREG_SPLIT_DELIM_CAPTURE);

//         for ($i = 1; $i < count($parts) - 1; $i += 2) {
//             $label = trim($parts[$i]);
//             $body  = trim($parts[$i + 1]);
//             if (isset($map[$label])) {
//                 $sections[$map[$label]] = $body;
//             }
//         }

//         return $sections;
//     }

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
    private function renderPdf(Resume $resume, array $resumeData, $user): string
    {
        $pdf  = Pdf::loadView('resume.pdf', compact('resume', 'resumeData', 'user'))
                   ->setPaper('a4', 'portrait');

        $path = 'resume/' . $resume->id . '/resume-' . str()->slug($resume->job_title) . '-' . $resume->id . '.pdf';

        Storage::disk('local')->put($path, $pdf->output());

        return $path;
    }
}