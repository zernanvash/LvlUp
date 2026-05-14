<?php

namespace App\Http\Controllers;

use App\Models\Resume;
use App\Services\ResumeAiPipeline;
use App\Services\ResumeAnalyzer;
use Illuminate\Http\Request;
use Spatie\Browsershot\Browsershot;

class ResumeController extends Controller
{
    protected $analyzer;
    protected $pipeline;

    public function __construct(ResumeAnalyzer $analyzer, ResumeAiPipeline $pipeline)
    {
        $this->analyzer = $analyzer;
        $this->pipeline = $pipeline;
    }

    /**
     * Show the full resume builder page at /resume.
     * Loads all user profile data automatically.
     */
    public function index()
    {
        $user = auth()->user()->load([
            'projects.skills',
            'certificates',
            'unlockedNodes.skill',
        ]);

        // Latest resume (if any)
        $resume = $user->resumes()->latest()->first();

        return view('resume.index', compact('user', 'resume'));
    }

    /**
     * Analyze a job description — returns ranked projects + keywords.
     */
    public function analyze(Request $request)
    {
        $validated = $request->validate([
            'job_description' => 'required|string',
        ]);

        $user = auth()->user()->load('projects.skills');

        $keywords      = $this->analyzer->extractKeywords($validated['job_description']);
        $rankedProjects = $this->analyzer->rankProjects($user->projects, $keywords);
        $matchScore    = $this->analyzer->calculateMatchScore($user, $keywords);

        return response()->json([
            'success'     => true,
            'keywords'    => $keywords,
            'match_score' => $matchScore,
            'projects'    => $rankedProjects->map(fn($p) => [
                'id'              => $p->id,
                'name'            => $p->name,
                'description'     => $p->description,
                'relevance_score' => round($p->relevance_score ?? 0),
                'skills'          => $p->skills->pluck('name'),
            ]),
        ]);
    }

    /**
     * Generate an AI-written resume from all user profile data.
     */
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'job_title'           => 'required|string|max:255',
            'job_description'     => 'nullable|string',
            'selected_project_ids'=> 'nullable|array',
            'template'            => 'nullable|string|in:modern,classic,minimal,creative',
        ]);

        $user = auth()->user()->load([
            'projects.skills',
            'certificates',
            'unlockedNodes.skill',
        ]);

        $selectedIds = $validated['selected_project_ids'] ?? $user->projects->pluck('id')->toArray();
        $projects    = $user->projects->whereIn('id', $selectedIds);
        $skills      = $user->unlockedNodes->map(fn($n) => $n->skill)->filter();
        $certificates= $user->certificates;

        // Extract keywords for match scoring
        $jobDesc  = $validated['job_description'] ?? '';
        $keywords = $jobDesc ? $this->analyzer->extractKeywords($jobDesc) : [];
        $matchScore = $keywords ? $this->analyzer->calculateMatchScore($user, $keywords) : 0;

        $resumeInput = (object)[
            'job_title'       => $validated['job_title'],
            'job_description' => $jobDesc,
            'template'         => $validated['template'] ?? 'modern',
        ];

        $pipelineResult = $this->pipeline->generate(
            $user,
            $resumeInput,
            $projects,
            $skills,
            $certificates,
            $keywords,
            $matchScore
        );
        $aiContent = $pipelineResult['content'];

        // Save/update the resume record
        $resume = $user->resumes()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'job_title'            => $validated['job_title'],
                'job_description'      => $jobDesc,
                'selected_project_ids' => $selectedIds,
                'target_keywords'      => implode(', ', $keywords),
                'match_score'          => $matchScore,
                'template'             => $validated['template'] ?? 'modern',
                'ai_content'           => json_encode($aiContent),
            ]
        );

        return response()->json([
            'success'    => true,
            'ai_content' => $aiContent,
            'resume_id'  => $resume->id,
            'match_score'=> round($matchScore),
            'pipeline'   => $pipelineResult['metadata'],
        ]);
    }

    /**
     * Download the resume as PDF.
     */
    public function download(Request $request)
    {
        $user = auth()->user()->load([
            'projects.skills',
            'certificates',
            'unlockedNodes.skill',
        ]);

        $resume = $user->resumes()->latest()->first();

        if (!$resume) {
            return back()->with('error', 'Please generate your resume first.');
        }

        $aiContent   = json_decode($resume->ai_content ?? '{}', true);
        $selectedIds = $resume->selected_project_ids ?? [];
        $projects    = $user->projects->whereIn('id', $selectedIds);
        $template    = $request->input('template', $resume->template ?? 'modern');

        $html = view('resume.pdf.' . $template, [
            'user'       => $user,
            'resume'     => $resume,
            'projects'   => $projects,
            'ai_content' => $aiContent,
        ])->render();

        $filename = 'resume_' . str()->slug($user->name) . '_' . now()->format('Ymd') . '.pdf';

        return response()->streamDownload(function () use ($html) {
            echo Browsershot::html($html)
                ->format('A4')
                ->margins(0, 0, 0, 0)
                ->showBackground()
                ->waitUntilNetworkIdle()
                ->pdf();
        }, $filename);
    }

    /**
     * Preview the resume as PDF inline.
     */
    public function preview(Request $request)
    {
        $user = auth()->user()->load([
            'projects.skills',
            'certificates',
            'unlockedNodes.skill',
        ]);

        $resume = $user->resumes()->latest()->first();

        if (!$resume) {
            return response('Please generate your resume first.', 404);
        }

        $aiContent   = json_decode($resume->ai_content ?? '{}', true);
        $selectedIds = $resume->selected_project_ids ?? [];
        $projects    = $user->projects->whereIn('id', $selectedIds);
        $template    = $request->input('template', $resume->template ?? 'modern');

        $html = view('resume.pdf.' . $template, [
            'user'       => $user,
            'resume'     => $resume,
            'projects'   => $projects,
            'ai_content' => $aiContent,
        ])->render();

        $filename = 'resume_' . str()->slug($user->name) . '_' . now()->format('Ymd') . '.pdf';

        $pdf = Browsershot::html($html)
            ->format('A4')
            ->margins(0, 0, 0, 0)
            ->showBackground()
            ->waitUntilNetworkIdle()
            ->pdf();

        return response($pdf)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="' . $filename . '"');
    }
}
