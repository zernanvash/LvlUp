<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use Illuminate\Support\Str;
class ProjectController extends Controller
{
    // Show Dashboard
    public function index() {
        $projects = Project::latest()->get();
        return view('dashboard', compact('projects'));
    }

    // Show Create Page
    public function create() {
        return view('projects.create');
    }

    // Save Project
    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable',
            'language' => 'required',
            'content' => 'nullable',
            'tags' => 'nullable'
        ]);

        $validated['slug'] = Str::slug($request->name);

        Project::create($validated);

        return redirect('/dashboard')->with('success', 'Project added!');
    }
}