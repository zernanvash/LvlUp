@extends('layouts.app')

@section('title', 'Create Project')
@section('page_title', 'New Project')
@section('page_subtitle', 'Add to your portfolio and earn XP')

@section('content')
<div class="max-w-3xl mx-auto" x-data="{ projectType: '{{ old('project_type') }}' }">
    <form action="{{ route('projects.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
        @csrf
        <input type="hidden" name="project_type" id="project_type_input" :value="projectType" required>

        {{-- ── Project Info ── --}}
        <div class="rounded-xl overflow-hidden border border-white/10 bg-purple-950/30 backdrop-blur">
            <div class="flex items-center gap-2 px-4 py-2.5 bg-white/5 border-b border-white/10">
                <i class="fas fa-file-alt text-purple-400 text-xs"></i>
                <span class="text-xs font-bold uppercase tracking-widest text-purple-200">Project Info</span>
            </div>

            <div class="p-4 grid grid-cols-1 md:grid-cols-3 gap-3">
                {{-- Name --}}
                <div class="md:col-span-3">
                    <label class="block text-xs font-bold uppercase tracking-wider text-purple-300 mb-1.5">
                        Project Name <span class="text-red-400">*</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        required
                        placeholder="e.g. Task Manager API"
                        class="w-full px-3 py-2"
                        value="{{ old('name') }}"
                    >
                    @error('name')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Language --}}
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-purple-300 mb-1.5">
                        Language <span class="text-red-400">*</span>
                    </label>
                    <select
                        name="language"
                        required
                        class="w-full px-3 py-2"
                    >
                        <option value="">Select...</option>
                        @foreach(['PHP','JavaScript','TypeScript','Python','Java','C++','C#','Ruby','Go','Rust'] as $lang)
                            <option value="{{ $lang }}" {{ old('language') == $lang ? 'selected' : '' }}>{{ $lang }}</option>
                        @endforeach
                    </select>
                    @error('language')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Live URL --}}
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-purple-300 mb-1.5">
                        Live URL
                    </label>
                    <div class="relative">
                        <i class="fas fa-globe absolute left-3 top-1/2 -translate-y-1/2 text-purple-400/60 text-xs pointer-events-none"></i>
                        <input
                            type="url"
                            name="url"
                            placeholder="https://..."
                            class="w-full pl-8 pr-3 py-2"
                            value="{{ old('url') }}"
                        >
                    </div>
                    @error('url')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- GitHub --}}
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-purple-300 mb-1.5">
                        GitHub
                    </label>
                    <div class="relative">
                        <i class="fab fa-github absolute left-3 top-1/2 -translate-y-1/2 text-purple-400/60 text-xs pointer-events-none"></i>
                        <input
                            type="url"
                            name="github_url"
                            placeholder="github.com/..."
                            class="w-full pl-8 pr-3 py-2"
                            value="{{ old('github_url') }}"
                        >
                    </div>
                    @error('github_url')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div class="md:col-span-3">
                    <label class="block text-xs font-bold uppercase tracking-wider text-purple-300 mb-1.5">
                        Description
                    </label>
                    <textarea
                        name="description"
                        rows="2"
                        placeholder="What does this project do? What problems does it solve?"
                        class="w-full px-3 py-2 resize-none"
                    >{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- ── Project Type ── --}}
        <div class="rounded-xl overflow-hidden border border-white/10 bg-indigo-950/30 backdrop-blur">
            <div class="flex items-center gap-2 px-4 py-2.5 bg-white/5 border-b border-white/10">
                <i class="fas fa-layer-group text-indigo-400 text-xs"></i>
                <span class="text-xs font-bold uppercase tracking-widest text-indigo-200">Project Type</span>
                <span class="ml-auto text-xs text-indigo-400/70">Affects skill tree unlocks</span>
            </div>

            @error('project_type')
                <p class="text-red-400 text-xs px-4 pt-3">{{ $message }}</p>
            @enderror

            <div class="p-3">
                @php
                $types = [
                    ['value' => 'web',       'label' => 'Web',       'icon' => 'fas fa-globe'],
                    ['value' => 'backend',   'label' => 'Backend',   'icon' => 'fas fa-server'],
                    ['value' => 'fullstack', 'label' => 'Full Stack','icon' => 'fas fa-layer-group'],
                    ['value' => 'mobile',    'label' => 'Mobile',    'icon' => 'fas fa-mobile-alt'],
                    ['value' => 'devops',    'label' => 'DevOps',    'icon' => 'fas fa-cloud'],
                    ['value' => 'ai',        'label' => 'AI / ML',   'icon' => 'fas fa-brain'],
                    ['value' => 'other',     'label' => 'Other',     'icon' => 'fas fa-code'],
                ];
                @endphp

                <div class="grid grid-cols-7 gap-2">
                    @foreach($types as $type)
                    <button
                        type="button"
                        x-on:click="projectType = '{{ $type['value'] }}'"
                        :class="projectType === '{{ $type['value'] }}'
                            ? 'border-indigo-400 bg-indigo-500/20 text-indigo-200 ring-1 ring-indigo-400/50'
                            : 'border-white/10 bg-white/5 text-purple-300/60 hover:border-white/20 hover:text-purple-200'"
                        class="flex flex-col items-center gap-1.5 py-3 rounded-lg border transition-all duration-150 cursor-pointer"
                    >
                        <i class="{{ $type['icon'] }} text-base"></i>
                        <span class="text-xs font-bold leading-tight text-center">{{ $type['label'] }}</span>
                    </button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ── Skills & Code ── --}}
        <div class="rounded-xl overflow-hidden border border-white/10 bg-blue-950/30 backdrop-blur">
            <div class="flex items-center gap-2 px-4 py-2.5 bg-white/5 border-b border-white/10">
                <i class="fas fa-magic text-blue-400 text-xs"></i>
                <span class="text-xs font-bold uppercase tracking-widest text-blue-200">Skills & Code</span>
                <span class="ml-auto flex items-center gap-1 text-xs text-amber-400/80">
                    <i class="fas fa-bolt text-amber-400"></i>+2 XP/line · max +400 XP
                </span>
            </div>

            <div class="p-4 space-y-3">
                {{-- Code sample --}}
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-blue-300 mb-1.5">
                        Code Sample
                        <span class="normal-case tracking-normal font-normal text-blue-400/60 ml-1">(optional — AI detects skills)</span>
                    </label>
                    <textarea
                        name="content"
                        id="code_input"
                        rows="5"
                        placeholder="// Paste your code here — skills will be detected as you type"
                        class="w-full font-mono text-xs bg-black/40 border border-blue-500/20 rounded-lg px-3 py-2.5 text-cyan-300 placeholder-blue-400/30 focus:border-blue-400 focus:outline-none transition resize-none"
                    >{{ old('content') }}</textarea>
                </div>

                {{-- Tags --}}
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-blue-300 mb-1.5">
                        Technologies & Skills
                    </label>
                    <input
                        type="text"
                        name="tags"
                        id="tag_input"
                        placeholder="React, Node.js, MongoDB... or click detected skills below"
                        class="w-full px-3 py-2"
                        value="{{ old('tags') }}"
                    >
                    <p class="text-xs text-blue-400/60 mt-1">Separate with commas.</p>
                    <div id="suggestions" class="flex flex-wrap gap-2 mt-2"></div>
                </div>
            </div>
        </div>

        {{-- ── Thumbnail ── --}}
        <div class="rounded-xl overflow-hidden border border-white/10 bg-pink-950/30 backdrop-blur">
            <div class="flex items-center gap-2 px-4 py-2.5 bg-white/5 border-b border-white/10">
                <i class="fas fa-image text-pink-400 text-xs"></i>
                <span class="text-xs font-bold uppercase tracking-widest text-pink-200">Thumbnail</span>
                <span class="ml-auto text-xs text-pink-400/70">PNG or JPG · max 2 MB</span>
            </div>

            <div class="p-4">
                <label class="cursor-pointer block">
                    <div id="drop_zone" class="flex items-center gap-3 border border-dashed border-pink-500/30 rounded-lg px-4 py-4 hover:border-pink-400/60 transition text-center">
                        <i class="fas fa-cloud-upload-alt text-2xl text-pink-400/60 flex-shrink-0"></i>
                        <div class="text-left">
                            <p class="text-sm text-pink-200 font-bold">Drag & drop or click to browse</p>
                            <p class="text-xs text-pink-400/60 mt-0.5">PNG, JPG up to 2 MB</p>
                        </div>
                        <p id="file_name" class="ml-auto text-xs text-pink-300 hidden"></p>
                    </div>
                    <input type="file" name="thumbnail" accept="image/*" class="hidden" id="thumbnail_input">
                </label>
            </div>
        </div>

        {{-- ── XP Preview + Actions ── --}}
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-purple-900/30 border border-purple-500/20">
            <i class="fas fa-trophy text-purple-400"></i>
            <span class="text-sm text-purple-200">Estimated XP reward</span>
            <span class="px-2.5 py-0.5 rounded-full bg-purple-500/20 border border-purple-400/30 text-xs font-bold text-purple-200">Base +100 XP</span>
            <span id="xp_bonus" class="text-sm text-purple-300/60"></span>
        </div>

        <div class="flex items-center justify-between pt-1">
            <a href="{{ route('dashboard') }}" class="btn-secondary px-5 py-2 text-sm">
                Cancel
            </a>
            <button
                type="submit"
                class="btn-glow px-6 py-2 rounded-lg font-bold text-sm flex items-center gap-2"
            >
                <i class="fas fa-rocket"></i>
                Launch & Earn XP
            </button>
        </div>
    </form>
</div>

<script>
    const codeArea   = document.getElementById('code_input');
    const tagInput   = document.getElementById('tag_input');
    const suggestDiv = document.getElementById('suggestions');
    const xpBonus    = document.getElementById('xp_bonus');
    const fileInput  = document.getElementById('thumbnail_input');
    const fileName   = document.getElementById('file_name');

    const library = {
        'import React'   : 'React',
        'from "react"'   : 'React',
        'import Vue'     : 'Vue',
        'new Vue'        : 'Vue',
        'Eloquent'       : 'Laravel',
        'Route::'        : 'Laravel',
        'Schema::'       : 'Laravel',
        'addEventListener': 'JavaScript',
        'querySelector'  : 'JavaScript',
        'fetch('         : 'API',
        'axios'          : 'API',
        'tailwind'       : 'Tailwind CSS',
        'SELECT'         : 'SQL',
        'INSERT INTO'    : 'SQL',
        'import numpy'   : 'Python',
        'import pandas'  : 'Python',
        'tensorflow'     : 'TensorFlow',
        'pytorch'        : 'PyTorch',
        'docker'         : 'Docker',
        'kubernetes'     : 'Kubernetes',
    };

    let detectedSkills = new Set();

    codeArea.addEventListener('input', () => {
        const val  = codeArea.value;
        const lines = val.split('\n').filter(l => l.trim()).length;
        const bonus = Math.min(lines * 2, 400);

        xpBonus.textContent = lines > 0 ? `+ ${bonus} XP code bonus` : '';

        suggestDiv.innerHTML = '';
        detectedSkills.clear();

        Object.keys(library).forEach(pattern => {
            if (val.toLowerCase().includes(pattern.toLowerCase())) {
                const skill = library[pattern];
                if (!detectedSkills.has(skill)) {
                    detectedSkills.add(skill);
                    const currentTags = tagInput.value.toLowerCase();
                    if (!currentTags.includes(skill.toLowerCase())) {
                        const btn       = document.createElement('button');
                        btn.type        = 'button';
                        btn.className   = 'flex items-center gap-1.5 px-3 py-1 bg-purple-500/20 border border-purple-400/30 text-purple-200 rounded-full text-xs font-bold hover:border-purple-400 transition';
                        btn.innerHTML   = `<i class="fas fa-plus text-xs"></i>${skill}`;
                        btn.onclick     = () => {
                            const current = tagInput.value;
                            tagInput.value = current ? `${current}, ${skill}` : skill;
                            btn.remove();
                            if (!suggestDiv.querySelector('button')) {
                                suggestDiv.innerHTML = '';
                            }
                        };
                        suggestDiv.appendChild(btn);
                    }
                }
            }
        });

        if (detectedSkills.size > 0 && suggestDiv.querySelector('button')) {
            const info       = document.createElement('span');
            info.className   = 'flex items-center gap-1 px-2.5 py-1 bg-teal-500/10 border border-teal-400/30 text-teal-300 rounded-full text-xs font-bold';
            info.innerHTML   = `<i class="fas fa-sparkles text-teal-400"></i>${detectedSkills.size} detected`;
            suggestDiv.insertBefore(info, suggestDiv.firstChild);
        }
    });

    fileInput.addEventListener('change', () => {
        if (fileInput.files.length > 0) {
            fileName.textContent = fileInput.files[0].name;
            fileName.classList.remove('hidden');
        }
    });
</script>
@endsection
