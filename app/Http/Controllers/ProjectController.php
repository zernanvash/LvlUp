<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = auth()->user()->projects()->with('skills')->latest();

        if ($request->filled('type')) {
            $query->where('project_type', $request->type);
        }

        $cacheKey = 'projects.index.' . auth()->id() . '.' . md5(json_encode([
            'type' => $request->get('type', 'all'),
            'page' => $request->integer('page', 1),
        ]));

        $projects = Cache::remember($cacheKey, now()->addSeconds(30), fn () => $query->paginate(12));

        return view('projects.index', compact('projects'));
    }
    
    public function create()
    {
        $skills = Cache::remember('skills.options', now()->addMinutes(10), fn () => Skill::orderBy('name')->get());
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
            $cloudinaryData = $this->uploadToCloudinary($request->file('thumbnail'));
            if ($cloudinaryData) {
                $project->update(['thumbnail' => $cloudinaryData['secure_url']]);
            }
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

        // Record activity streak (max +1 per calendar day regardless of project count)
        $user->fresh()->recordActivityStreak();

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
        
        $skills = Cache::remember('skills.options', now()->addMinutes(10), fn () => Skill::orderBy('name')->get());
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
            'thumbnail' => 'nullable|image|max:2048',
        ]);
        
        $thumbnailFile = $validated['thumbnail'] ?? null;
        unset($validated['thumbnail']);

        $user = auth()->user();
        $alreadyAvailableIds = $this->getAvailableNodeIds($user);

        $project->update($validated);
        
        // Handle thumbnail upload updates
        if ($thumbnailFile) {
            $cloudinaryData = $this->uploadToCloudinary($thumbnailFile);
            if ($cloudinaryData) {
                $project->update(['thumbnail' => $cloudinaryData['secure_url']]);
            }
        }
        
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

        // Record activity streak (max +1 per calendar day)
        $user->fresh()->recordActivityStreak();

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
        auth()->user()->clearFastUiCaches();
        
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

    /**
     * Upload an image file to Cloudinary using the REST API.
     */
    private function uploadToCloudinary($file): ?array
    {
        try {
            $cloudinaryUrl = config('services.cloudinary.url') ?? env('CLOUDINARY_URL');

            $parsed    = parse_url($cloudinaryUrl);
            $cloudName = $parsed['host'];
            $apiKey    = $parsed['user'];
            $apiSecret = $parsed['pass'];

            $endpoint = "https://api.cloudinary.com/v1_1/{$cloudName}/image/upload";

            $timestamp = time();
            $folder = 'projects';
            $signature = sha1("folder={$folder}&timestamp={$timestamp}{$apiSecret}");

            $response = Http::attach(
                'file',
                file_get_contents($file->getRealPath()),
                $file->getClientOriginalName()
            )->post($endpoint, [
                'api_key'       => $apiKey,
                'timestamp'     => $timestamp,
                'signature'     => $signature,
                'folder'        => $folder,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Cloudinary project upload failed', ['response' => $response->body()]);
            return null;

        } catch (\Throwable $e) {
            Log::error('Cloudinary project upload exception', ['error' => $e->getMessage()]);
            return null;
        }
    }
}
