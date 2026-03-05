<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Skill;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function __construct(protected CloudinaryService $cloudinary) {}

    public function index()
    {
        $projects = auth()->user()->projects()
            ->with('skills')
            ->latest()
            ->paginate(12);

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
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'url'         => 'nullable|url',
            'github_url'  => 'nullable|url',
            'language'    => 'required|string',
            'content'     => 'nullable|string',
            'tags'        => 'nullable|string',
            'thumbnail'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        // Calculate XP reward based on complexity
        $xpReward = 100;
        if (!empty($validated['content'])) {
            $lines     = count(explode("\n", $validated['content']));
            $xpReward += min($lines * 2, 400);
        }

        $project = auth()->user()->projects()->create([
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? null,
            'url'         => $validated['url']          ?? null,
            'github_url'  => $validated['github_url']   ?? null,
            'language'    => $validated['language'],
            'xp_reward'   => $xpReward,
            'metadata'    => [
                'code_snippet'  => $validated['content'] ?? null,
                'lines_of_code' => isset($validated['content'])
                    ? count(explode("\n", $validated['content']))
                    : 0,
            ],
        ]);

        // Upload thumbnail to Cloudinary
        if ($request->hasFile('thumbnail')) {
            $url = $this->cloudinary->uploadProjectThumbnail(
                $request->file('thumbnail'),
                $project->id
            );
            $project->update(['thumbnail' => $url]);
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

        return redirect()->route('dashboard')
            ->with('success', "Project created! You earned {$xpReward} XP!");
    }

    public function show(Project $project)
    {
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
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'url'         => 'nullable|url',
            'github_url'  => 'nullable|url',
            'language'    => 'required|string',
            'is_featured' => 'boolean',
            'tags'        => 'nullable|string',
            'thumbnail'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $project->update($validated);

        // Replace thumbnail on Cloudinary if a new one was uploaded
        if ($request->hasFile('thumbnail')) {
            // Delete old one first
            if ($project->thumbnail && str_contains($project->thumbnail, 'cloudinary.com')) {
                $this->cloudinary->deleteByUrl($project->thumbnail);
            }

            $url = $this->cloudinary->uploadProjectThumbnail(
                $request->file('thumbnail'),
                $project->id
            );
            $project->update(['thumbnail' => $url]);
        }

        // Update skills
        if ($request->has('tags')) {
            $project->skills()->detach();
            $tags = array_map('trim', explode(',', $request->tags));
            $project->attachSkillsFromTags($tags);
        }

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project updated successfully!');
    }

    public function destroy(Project $project)
    {
        if ($project->user_id !== auth()->id()) {
            abort(403);
        }

        // Delete thumbnail from Cloudinary
        if ($project->thumbnail && str_contains($project->thumbnail, 'cloudinary.com')) {
            $this->cloudinary->deleteByUrl($project->thumbnail);
        }

        $project->delete();

        return redirect()->route('dashboard')
            ->with('success', 'Project deleted successfully!');
    }
}
