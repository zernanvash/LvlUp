<?php

namespace App\Http\Controllers;

use App\Models\Resume;
use App\Models\Project;
use App\Models\Skill;
use App\Services\ResumeAnalyzer;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ResumeController extends Controller
{
    protected $analyzer;

    public function __construct(ResumeAnalyzer $analyzer)
    {
        $this->analyzer = $analyzer;
    }

    public function index()
    {
        $user = auth()->user();
        $resumes = $user->resumes()->latest()->paginate(10);
        
        return view('resumes.index', compact('resumes'));
    }

    public function create()
    {
        return view('resumes.create');
    }

    public function show(Resume $resume)
    {
        // Ensure user owns this resume
        if ($resume->user_id !== auth()->id()) {
            abort(403);
        }

        $projects = $resume->getSelectedProjects();
        $skills = $resume->getSelectedSkills();

        return view('resumes.show', compact('resume', 'projects', 'skills'));
    }

    
    public function analyze(Request $request)
    {
        $validated = $request->validate([
            'job_description' => 'required|string',
        ]);
        
        $user = auth()->user();
        
        // Extract keywords from job description
        $keywords = $this->analyzer->extractKeywords($validated['job_description']);
        
        // Rank user's projects by relevance
        $rankedProjects = $this->analyzer->rankProjects($user->projects, $keywords);
        
        // Calculate match score
        $matchScore = $this->analyzer->calculateMatchScore($user, $keywords);
        
        return response()->json([
            'success' => true,
            'keywords' => $keywords,
            'projects' => $rankedProjects->map(function ($project) {
                return [
                    'id' => $project->id,
                    'name' => $project->name,
                    'description' => $project->description,
                    'relevance_score' => $project->relevance_score ?? 0,
                    'skills' => $project->skills->pluck('name'),
                ];
            }),
            'match_score' => $matchScore,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'job_title' => 'required|string|max:255',
            'job_description' => 'required|string',
            'selected_project_ids' => 'required|array',
            'selected_project_ids.*' => 'exists:projects,id',
            'selected_skill_ids' => 'nullable|array',
            'selected_skill_ids.*' => 'exists:skills,id',
            'template' => 'nullable|string|in:modern,classic,minimal,creative',
        ]);
        
        $user = auth()->user();
        
        // Extract keywords for storage
        $keywords = $this->analyzer->extractKeywords($validated['job_description']);
        
        // Calculate match score
        $matchScore = $this->analyzer->calculateMatchScore($user, $keywords);
        
        // Create resume record
        $resume = $user->resumes()->create([
            'job_title' => $validated['job_title'],
            'job_description' => $validated['job_description'],
            'selected_project_ids' => $validated['selected_project_ids'],
            'selected_skill_ids' => $validated['selected_skill_ids'] ?? [],
            'target_keywords' => implode(', ', $keywords),
            'match_score' => $matchScore,
            'template' => $validated['template'] ?? 'modern',
        ]);
        
        return redirect()->route('resumes.show', $resume)
            ->with('success', 'Resume configuration saved successfully!');
    }

    public function update(Request $request, Resume $resume)
    {
        // Ensure user owns this resume
        if ($resume->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'job_title' => 'sometimes|string|max:255',
            'job_description' => 'sometimes|string',
            'selected_project_ids' => 'sometimes|array',
            'selected_project_ids.*' => 'exists:projects,id',
            'selected_skill_ids' => 'sometimes|array',
            'selected_skill_ids.*' => 'exists:skills,id',
        ]);
        
        // Update resume
        $resume->update($validated);
        
        // Recalculate match score if job description changed
        if (isset($validated['job_description'])) {
            $keywords = $this->analyzer->extractKeywords($validated['job_description']);
            $matchScore = $this->analyzer->calculateMatchScore(auth()->user(), $keywords);
            $resume->update([
                'target_keywords' => implode(', ', $keywords),
                'match_score' => $matchScore,
            ]);
        }
        
        return redirect()->route('resumes.show', $resume)
            ->with('success', 'Resume updated successfully!');
    }
    
    public function generate(Resume $resume)
    {
        // Ensure user owns this resume
        if ($resume->user_id !== auth()->id()) {
            abort(403);
        }
        
        // Generate PDF using Resume model method
        $path = $resume->generatePDF('modern');
        
        return redirect()->route('resumes.show', $resume)
            ->with('success', 'Resume PDF generated successfully!');
    }
    
    public function download(Resume $resume)
    {
        // Ensure user owns this resume
        if ($resume->user_id !== auth()->id()) {
            abort(403);
        }
        
        if (!$resume->pdf_path) {
            return redirect()->back()->with('error', 'Resume PDF has not been generated yet.');
        }
        
        $fullPath = storage_path('app/' . $resume->pdf_path);
        
        if (!file_exists($fullPath)) {
            return redirect()->back()->with('error', 'Resume file not found.');
        }
        
        return response()->download($fullPath, 'resume_' . $resume->job_title . '.pdf');
    }
}
