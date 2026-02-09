<?php

namespace App\Http\Controllers;

use App\Models\Resume;
use App\Models\Project;
use App\Models\Skill;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ResumeController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $resumes = $user->resumes()->latest()->paginate(10);
        
        return view('resume.index', compact('resumes'));
    }
    
    public function analyze(Request $request)
    {
        $validated = $request->validate([
            'job_title' => 'required|string|max:255',
            'job_description' => 'required|string',
        ]);
        
        $user = auth()->user();
        
        // Extract keywords from job description
        $keywords = $this->extractKeywords($validated['job_description']);
        
        // Match user's projects to keywords
        $matchedProjects = $this->matchProjects($user, $keywords);
        
        // Match user's skills
        $matchedSkills = $this->matchSkills($user, $keywords);
        
        // Calculate match score
        $matchScore = $this->calculateMatchScore($matchedProjects, $matchedSkills, $keywords);
        
        return response()->json([
            'success' => true,
            'keywords' => $keywords,
            'matched_projects' => $matchedProjects,
            'matched_skills' => $matchedSkills,
            'match_score' => $matchScore,
        ]);
    }
    
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'job_title' => 'required|string|max:255',
            'job_description' => 'required|string',
            'project_ids' => 'required|array',
            'project_ids.*' => 'exists:projects,id',
            'skill_ids' => 'required|array',
            'skill_ids.*' => 'exists:skills,id',
        ]);
        
        $user = auth()->user();
        
        // Create resume record
        $resume = $user->resumes()->create([
            'job_title' => $validated['job_title'],
            'job_description' => $validated['job_description'],
            'selected_project_ids' => $validated['project_ids'],
            'selected_skill_ids' => $validated['skill_ids'],
            'target_keywords' => implode(', ', $this->extractKeywords($validated['job_description'])),
        ]);
        
        // Get selected data
        $projects = Project::whereIn('id', $validated['project_ids'])->get();
        $skills = Skill::whereIn('id', $validated['skill_ids'])->get();
        
        // Generate PDF
        $pdf = Pdf::loadView('resume.template', [
            'user' => $user,
            'resume' => $resume,
            'projects' => $projects,
            'skills' => $skills,
        ]);
        
        // Save PDF
        $filename = 'resume_' . $resume->id . '_' . time() . '.pdf';
        $path = 'resumes/' . $filename;
        $pdf->save(storage_path('app/public/' . $path));
        
        $resume->update(['pdf_path' => '/storage/' . $path]);
        
        return redirect()->route('resume.index')
            ->with('success', 'Resume generated successfully!');
    }
    
    public function download(Resume $resume)
    {
        if ($resume->user_id !== auth()->id()) {
            abort(403);
        }
        
        if (!$resume->pdf_path || !file_exists(public_path($resume->pdf_path))) {
            return redirect()->back()->with('error', 'Resume file not found.');
        }
        
        return response()->download(public_path($resume->pdf_path));
    }
    
    private function extractKeywords(string $text): array
    {
        // Convert to lowercase
        $text = strtolower($text);
        
        // Common tech keywords to look for
        $techKeywords = [
            'php', 'laravel', 'javascript', 'react', 'vue', 'node', 'python', 'django',
            'mysql', 'postgresql', 'mongodb', 'docker', 'kubernetes', 'aws', 'azure',
            'git', 'api', 'rest', 'graphql', 'typescript', 'html', 'css', 'tailwind',
            'bootstrap', 'sass', 'webpack', 'agile', 'scrum', 'ci/cd', 'testing',
            'frontend', 'backend', 'fullstack', 'database', 'security', 'devops'
        ];
        
        $found = [];
        foreach ($techKeywords as $keyword) {
            if (stripos($text, $keyword) !== false) {
                $found[] = $keyword;
            }
        }
        
        return array_unique($found);
    }
    
    private function matchProjects($user, array $keywords)
    {
        return $user->projects()
            ->with('skills')
            ->get()
            ->filter(function ($project) use ($keywords) {
                $projectText = strtolower($project->name . ' ' . $project->description . ' ' . $project->language);
                foreach ($keywords as $keyword) {
                    if (stripos($projectText, $keyword) !== false) {
                        return true;
                    }
                }
                foreach ($project->skills as $skill) {
                    if (in_array(strtolower($skill->name), $keywords)) {
                        return true;
                    }
                }
                return false;
            })
            ->values();
    }
    
    private function matchSkills($user, array $keywords)
    {
        return $user->projects()
            ->with('skills')
            ->get()
            ->pluck('skills')
            ->flatten()
            ->unique('id')
            ->filter(function ($skill) use ($keywords) {
                return in_array(strtolower($skill->name), $keywords);
            })
            ->values();
    }
    
    private function calculateMatchScore($projects, $skills, array $keywords): int
    {
        $totalKeywords = count($keywords);
        if ($totalKeywords === 0) return 0;
        
        $matchedKeywords = 0;
        
        // Check projects
        foreach ($projects as $project) {
            $projectText = strtolower($project->name . ' ' . $project->description);
            foreach ($keywords as $keyword) {
                if (stripos($projectText, $keyword) !== false) {
                    $matchedKeywords++;
                    break;
                }
            }
        }
        
        // Check skills
        foreach ($skills as $skill) {
            if (in_array(strtolower($skill->name), $keywords)) {
                $matchedKeywords++;
            }
        }
        
        return min(100, (int)(($matchedKeywords / $totalKeywords) * 100));
    }
}
