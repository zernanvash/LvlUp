<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = auth()->user()->projects()->with('skills')->latest();

        if ($request->filled('type')) {
            $query->where('project_type', $request->type);
        }

        $projects = $query->paginate(12);

        return view('projects.index', compact('projects'));
    }
    
    public function create()
    {
        $skills = Skill::orderBy('name')->get();
        return view('projects.create', compact('skills'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'url' => 'nullable|url',
            'github_url' => 'nullable|url',
            'language' => 'required|string',
            'project_type' => 'required|string|in:web,backend,fullstack,mobile,devops,ai,other',
            'content' => 'nullable|string',
            'tags' => 'nullable|string',
            'thumbnail' => 'nullable|image|max:2048',
        ]);
        
        // Calculate XP reward based on complexity
        $xpReward = 100; // Base XP
        
        if (!empty($validated['content'])) {
            $lines = count(explode("\n", $validated['content']));
            $xpReward += min($lines * 2, 400); // Max +400 XP for code
        }
        
        // Snapshot which nodes are already available BEFORE creating the project
        $user = auth()->user();
        $alreadyAvailableIds = $this->getAvailableNodeIds($user);

        $project = auth()->user()->projects()->create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'url' => $validated['url'] ?? null,
            'github_url' => $validated['github_url'] ?? null,
            'language' => $validated['language'],
            'project_type' => $validated['project_type'],
            'xp_reward' => $xpReward,
            'metadata' => [
                'code_snippet' => $validated['content'] ?? null,
                'lines_of_code' => isset($validated['content']) ? count(explode("\n", $validated['content'])) : 0,
            ],
        ]);
        
        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('projects', 'public');
            $project->update(['thumbnail' => '/storage/' . $path]);
        }
        
        // Attach skills from tags
        if (!empty($validated['tags'])) {
            $tags = array_map('trim', explode(',', $validated['tags']));
            $project->attachSkillsFromTags($tags);
        }
        
        // Auto-detect skills from code
        if (!empty($validated['content'])) {
            $suggestions = $project->analyzeCodeAndSuggestSkills($validated['content']);
            $project->attachSkillsFromTags($suggestions);
        }

        // Check badges ONCE after all skills are attached, then flash
        $newBadges = $project->fresh()->checkBadgesAndReturn();
        if (!empty($newBadges)) {
            session()->flash('new_badges', $newBadges);
        }

        // Only notify about nodes that NEWLY became available (weren't available before)
        $this->flashNewlyAvailableNodes($user->fresh(), $alreadyAvailableIds);

        return redirect()->route('dashboard')
            ->with('success', "Project created! You earned {$xpReward} XP!");
    }
    
    public function show(Project $project)
    {
        // Ensure user owns the project
        if ($project->user_id !== auth()->id()) {
            abort(403);
        }
        
        $project->load('skills');
        
        return view('projects.show', compact('project'));
    }
    
    public function edit(Project $project)
    {
        if ($project->user_id !== auth()->id()) {
            abort(403);
        }
        
        $skills = Skill::orderBy('name')->get();
        $project->load('skills');
        
        return view('projects.edit', compact('project', 'skills'));
    }
    
    public function update(Request $request, Project $project)
    {
        if ($project->user_id !== auth()->id()) {
            abort(403);
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'url' => 'nullable|url',
            'github_url' => 'nullable|url',
            'language' => 'required|string',
            'project_type' => 'required|string|in:web,backend,fullstack,mobile,devops,ai,other',
            'is_featured' => 'boolean',
            'tags' => 'nullable|string',
        ]);

        $user = auth()->user();
        $alreadyAvailableIds = $this->getAvailableNodeIds($user);

        $project->update($validated);
        
        // Update skills
        if ($request->has('tags')) {
            $project->skills()->detach();
            $tags = array_map('trim', explode(',', $request->tags));
            $project->attachSkillsFromTags($tags);
        }

        // Check badges ONCE after all changes, then flash
        $newBadges = $project->fresh()->checkBadgesAndReturn();
        if (!empty($newBadges)) {
            session()->flash('new_badges', $newBadges);
        }

        $this->flashNewlyAvailableNodes($user->fresh(), $alreadyAvailableIds);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project updated successfully!');
    }
    
    public function destroy(Project $project)
    {
        if ($project->user_id !== auth()->id()) {
            abort(403);
        }
        
        $project->delete();
        
        return redirect()->route('dashboard')
            ->with('success', 'Project deleted successfully!');
    }

    /**
     * Get IDs of all nodes currently available (but not yet unlocked) for a user.
     */
    private function getAvailableNodeIds($user): array
    {
        $user->loadMissing('unlockedNodes');
        $unlockedIds = $user->unlockedNodes->pluck('id')->toArray();

        return \App\Models\SkillNode::whereNotIn('id', $unlockedIds)
            ->get()
            ->filter(fn($node) => $node->canBeUnlockedBy($user))
            ->pluck('id')
            ->toArray();
    }

    /**
     * Flash only nodes that NEWLY became available after a project change.
     * $previouslyAvailableIds = snapshot taken before the change.
     */
    private function flashNewlyAvailableNodes($user, array $previouslyAvailableIds): void
    {
        $user->loadMissing('unlockedNodes');
        $unlockedIds = $user->unlockedNodes->pluck('id')->toArray();

        $tierColors = [
            'core'      => '#f59e0b',
            'basic'     => '#3b82f6',
            'advanced'  => '#8b5cf6',
            'master'    => '#ec4899',
            'legendary' => '#f97316',
        ];

        $newlyReady = \App\Models\SkillNode::with('skill')
            ->whereNotIn('id', $unlockedIds)
            ->whereNotIn('id', $previouslyAvailableIds) // exclude already-available ones
            ->get()
            ->filter(fn($node) => $node->canBeUnlockedBy($user))
            ->map(fn($node) => [
                'title' => $node->title,
                'tier'  => $node->tier,
                'icon'  => $node->skill->icon ?? 'fas fa-code',
                'color' => $tierColors[$node->tier] ?? '#22c55e',
            ])
            ->values()
            ->toArray();

        if (!empty($newlyReady)) {
            session()->flash('nodes_ready', $newlyReady);
        }
    }
}
