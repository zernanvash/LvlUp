@extends('layouts.app')

@section('title', 'Create Project')
@section('page_title', 'New Project')
@section('page_subtitle', 'Add to your portfolio and earn XP')

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('projects.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        
        <!-- Project Details Card -->
        <div class="glow-border rounded-2xl p-8 bg-gradient-to-br from-purple-900/40 to-purple-950/40 backdrop-blur">
            <h2 class="font-display text-2xl font-bold text-white mb-6 flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-info-circle"></i>
                </div>
                Project Information
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Project Name -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-purple-200 mb-2">
                        Project Name <span class="text-red-400">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="name" 
                        required
                        placeholder="My Awesome Project"
                        class="w-full bg-purple-950/50 border-2 border-purple-500/30 rounded-xl px-4 py-3 text-white placeholder-purple-400/50 focus:border-purple-400 focus:outline-none transition"
                        value="{{ old('name') }}"
                    >
                    @error('name')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Language -->
                <div>
                    <label class="block text-sm font-bold text-purple-200 mb-2">
                        Primary Language <span class="text-red-400">*</span>
                    </label>
                    <select 
                        name="language" 
                        required
                        class="w-full bg-purple-950/50 border-2 border-purple-500/30 rounded-xl px-4 py-3 text-white focus:border-purple-400 focus:outline-none transition"
                    >
                        <option value="">Select Language</option>
                        <option value="PHP" {{ old('language') == 'PHP' ? 'selected' : '' }}>PHP</option>
                        <option value="JavaScript" {{ old('language') == 'JavaScript' ? 'selected' : '' }}>JavaScript</option>
                        <option value="Python" {{ old('language') == 'Python' ? 'selected' : '' }}>Python</option>
                        <option value="Java" {{ old('language') == 'Java' ? 'selected' : '' }}>Java</option>
                        <option value="C++" {{ old('language') == 'C++' ? 'selected' : '' }}>C++</option>
                        <option value="C#" {{ old('language') == 'C#' ? 'selected' : '' }}>C#</option>
                        <option value="Ruby" {{ old('language') == 'Ruby' ? 'selected' : '' }}>Ruby</option>
                        <option value="Go" {{ old('language') == 'Go' ? 'selected' : '' }}>Go</option>
                        <option value="Rust" {{ old('language') == 'Rust' ? 'selected' : '' }}>Rust</option>
                        <option value="TypeScript" {{ old('language') == 'TypeScript' ? 'selected' : '' }}>TypeScript</option>
                    </select>
                    @error('language')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Project URL -->
                <div>
                    <label class="block text-sm font-bold text-purple-200 mb-2">
                        Live URL (Optional)
                    </label>
                    <input 
                        type="url" 
                        name="url" 
                        placeholder="https://myproject.com"
                        class="w-full bg-purple-950/50 border-2 border-purple-500/30 rounded-xl px-4 py-3 text-white placeholder-purple-400/50 focus:border-purple-400 focus:outline-none transition"
                        value="{{ old('url') }}"
                    >
                    @error('url')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- GitHub URL -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-purple-200 mb-2 flex items-center gap-2">
                        <i class="fab fa-github"></i> GitHub Repository
                    </label>
                    <input 
                        type="url" 
                        name="github_url" 
                        placeholder="https://github.com/username/repo"
                        class="w-full bg-purple-950/50 border-2 border-purple-500/30 rounded-xl px-4 py-3 text-white placeholder-purple-400/50 focus:border-purple-400 focus:outline-none transition"
                        value="{{ old('github_url') }}"
                    >
                    @error('github_url')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Description -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-purple-200 mb-2">
                        Description
                    </label>
                    <textarea 
                        name="description" 
                        rows="4"
                        placeholder="Describe your project, the problems it solves, and technologies used..."
                        class="w-full bg-purple-950/50 border-2 border-purple-500/30 rounded-xl px-4 py-3 text-white placeholder-purple-400/50 focus:border-purple-400 focus:outline-none transition resize-none"
                    >{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Code & Skills Card -->
        <div class="glow-border rounded-2xl p-8 bg-gradient-to-br from-blue-900/40 to-blue-950/40 backdrop-blur">
            <h2 class="font-display text-2xl font-bold text-white mb-6 flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-code"></i>
                </div>
                Code & Skills
            </h2>
            
            <!-- Code Snippet -->
            <div class="mb-6">
                <label class="block text-sm font-bold text-blue-200 mb-2 flex items-center justify-between">
                    <span>Code Sample (Optional)</span>
                    <span class="text-xs font-normal text-blue-400">
                        <i class="fas fa-magic"></i> Auto-detects skills
                    </span>
                </label>
                <textarea 
                    name="content" 
                    id="code_input"
                    rows="12"
                    placeholder="// Paste your code here...
function example() {
    return 'AI will detect skills from your code!';
}"
                    class="w-full font-mono text-sm bg-black/50 border-2 border-blue-500/30 rounded-xl px-4 py-3 text-cyan-300 placeholder-blue-400/30 focus:border-blue-400 focus:outline-none transition resize-none"
                >{{ old('content') }}</textarea>
                <p class="text-xs text-blue-400 mt-2">
                    <i class="fas fa-bolt text-amber-400"></i> 
                    Earn extra XP based on code complexity! (2 XP per line, max +400 XP)
                </p>
            </div>
            
            <!-- Manual Tags -->
            <div>
                <label class="block text-sm font-bold text-blue-200 mb-2">
                    Technologies & Skills
                </label>
                <input 
                    type="text" 
                    name="tags" 
                    id="tag_input"
                    placeholder="React, Node.js, MongoDB, REST API..."
                    class="w-full bg-blue-950/50 border-2 border-blue-500/30 rounded-xl px-4 py-3 text-white placeholder-blue-400/50 focus:border-blue-400 focus:outline-none transition"
                    value="{{ old('tags') }}"
                >
                <p class="text-xs text-blue-300 mt-2">Separate with commas. Mix with auto-detected skills.</p>
                
                <!-- Skill Suggestions -->
                <div id="suggestions" class="flex flex-wrap gap-2 mt-4"></div>
            </div>
        </div>

        <!-- Thumbnail Upload -->
        <div class="glow-border rounded-2xl p-8 bg-gradient-to-br from-pink-900/40 to-pink-950/40 backdrop-blur">
            <h2 class="font-display text-2xl font-bold text-white mb-6 flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-pink-500 to-rose-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-image"></i>
                </div>
                Project Thumbnail
            </h2>
            
            <div class="flex items-center gap-4">
                <label class="flex-1 cursor-pointer">
                    <div class="border-2 border-dashed border-pink-500/30 rounded-xl p-8 hover:border-pink-400 transition text-center">
                        <i class="fas fa-cloud-upload-alt text-4xl text-pink-400 mb-3"></i>
                        <p class="text-pink-200 font-bold mb-1">Click to upload thumbnail</p>
                        <p class="text-xs text-pink-400">PNG, JPG up to 2MB</p>
                    </div>
                    <input type="file" name="thumbnail" accept="image/*" class="hidden">
                </label>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="px-6 py-3 bg-gray-700 hover:bg-gray-600 rounded-xl font-bold transition">
                Cancel
            </a>
            
            <button 
                type="submit" 
                class="btn-glow bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-500 hover:to-pink-500 px-8 py-3 rounded-xl font-display font-bold shadow-lg transition"
            >
                <span class="relative z-10 flex items-center gap-2">
                    <i class="fas fa-rocket"></i>
                    Launch Project & Earn XP
                </span>
            </button>
        </div>
    </form>
</div>

<script>
    const codeArea = document.getElementById('code_input');
    const tagInput = document.getElementById('tag_input');
    const suggestDiv = document.getElementById('suggestions');

    // Skill detection patterns
    const library = {
        'import React': 'React',
        'from "react"': 'React',
        'import Vue': 'Vue',
        'new Vue': 'Vue',
        'Eloquent': 'Laravel',
        'Route::': 'Laravel',
        'Schema::': 'Laravel',
        'addEventListener': 'JavaScript',
        'querySelector': 'JavaScript',
        'fetch(': 'API',
        'axios': 'API',
        'tailwind': 'Tailwind CSS',
        'SELECT': 'SQL',
        'INSERT INTO': 'SQL',
        'import numpy': 'Python',
        'import pandas': 'Python',
        'tensorflow': 'TensorFlow',
        'pytorch': 'PyTorch',
        'docker': 'Docker',
        'kubernetes': 'Kubernetes',
    };

    let detectedSkills = new Set();

    codeArea.addEventListener('input', () => {
        const val = codeArea.value;
        suggestDiv.innerHTML = '';
        detectedSkills.clear();
        
        Object.keys(library).forEach(pattern => {
            if (val.toLowerCase().includes(pattern.toLowerCase())) {
                const skill = library[pattern];
                
                if (!detectedSkills.has(skill)) {
                    detectedSkills.add(skill);
                    
                    const currentTags = tagInput.value.toLowerCase();
                    if (!currentTags.includes(skill.toLowerCase())) {
                        let btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = "px-4 py-2 bg-gradient-to-r from-purple-500/20 to-pink-500/20 border-2 border-purple-500/40 text-purple-200 rounded-lg text-sm font-bold hover:border-purple-400 transition";
                        btn.innerHTML = `<i class="fas fa-plus mr-2"></i>${skill}`;
                        btn.onclick = () => {
                            const current = tagInput.value;
                            tagInput.value = current ? `${current}, ${skill}` : skill;
                            btn.remove();
                        };
                        suggestDiv.appendChild(btn);
                    }
                }
            }
        });
        
        if (detectedSkills.size > 0) {
            const info = document.createElement('p');
            info.className = 'text-sm text-purple-300 w-full';
            info.innerHTML = `<i class="fas fa-sparkles text-amber-400"></i> Detected ${detectedSkills.size} skill(s) in your code!`;
            suggestDiv.insertBefore(info, suggestDiv.firstChild);
        }
    });
</script>
@endsection
