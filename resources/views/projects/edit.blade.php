@extends('layouts.app')

@section('title', 'Edit Project')
@section('page_title', 'Edit Project')
@section('page_subtitle', 'Update your portfolio entry')

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('projects.update', $project) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Project Details Card -->
        <div class="glow-border rounded-2xl p-8 bg-gradient-to-br from-purple-900/40 to-purple-950/40 backdrop-blur">
            <h2 class="font-display text-2xl font-bold text-white mb-6 flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-info-circle"></i>
                </div>
                Project Information
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-purple-200 mb-2">Project Name <span class="text-red-400">*</span></label>
                    <input type="text" name="name" required
                        class="w-full bg-purple-950/50 border-2 border-purple-500/30 rounded-xl px-4 py-3 text-white placeholder-purple-400/50 focus:border-purple-400 focus:outline-none transition"
                        value="{{ old('name', $project->name) }}">
                    @error('name')<p class="text-red-400 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-purple-200 mb-2">Primary Language <span class="text-red-400">*</span></label>
                    <select name="language" required
                        class="w-full bg-purple-950/50 border-2 border-purple-500/30 rounded-xl px-4 py-3 text-white focus:border-purple-400 focus:outline-none transition">
                        @foreach(['PHP','JavaScript','Python','Java','C++','C#','Ruby','Go','Rust','TypeScript'] as $lang)
                            <option value="{{ $lang }}" {{ old('language', $project->language) == $lang ? 'selected' : '' }}>{{ $lang }}</option>
                        @endforeach
                    </select>
                    @error('language')<p class="text-red-400 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-purple-200 mb-2">Live URL (Optional)</label>
                    <input type="url" name="url" placeholder="https://myproject.com"
                        class="w-full bg-purple-950/50 border-2 border-purple-500/30 rounded-xl px-4 py-3 text-white placeholder-purple-400/50 focus:border-purple-400 focus:outline-none transition"
                        value="{{ old('url', $project->url) }}">
                    @error('url')<p class="text-red-400 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-purple-200 mb-2 flex items-center gap-2">
                        <i class="fab fa-github"></i> GitHub Repository
               
                    </label>
                    <input type="url" name="github_url" placeholder="https://github.com/username/repo"
                        class="w-full bg-purple-950/50 border-2 border-purple-500/30 rounded-xl px-4 py-3 text-white placeholder-purple-400/50 focus:border-purple-400 focus:outline-none transition"
                        value="{{ old('github_url', $project->github_url) }}">
                    @error('github_url')<p class="text-red-400 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-purple-200 mb-2">Description</label>
                    <textarea name="description" rows="4"
                        placeholder="Describe your project..."
                        class="w-full bg-purple-950/50 border-2 border-purple-500/30 rounded-xl px-4 py-3 text-white placeholder-purple-400/50 focus:border-purple-400 focus:outline-none transition resize-none"
                    >{{ old('description', $project->description) }}</textarea>
                    @error('description')<p class="text-red-400 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <!-- Project Type Card -->
        <div class="glow-border rounded-2xl p-8 bg-gradient-to-br from-indigo-900/40 to-indigo-950/40 backdrop-blur">
            <h2 class="font-display text-2xl font-bold text-white mb-2 flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-violet-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-layer-group"></i>
                </div>
                Project Type
            </h2>
            <p class="text-indigo-300 text-sm mb-6">This determines which skill tree nodes you can unlock.</p>

            @error('project_type')<p class="text-red-400 text-sm mb-4">{{ $message }}</p>@enderror

            <input type="hidden" name="project_type" id="project_type_input" value="{{ old('project_type', $project->project_type) }}" required>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3" x-data="{ selected: '{{ old('project_type', $project->project_type) }}' }">
                @php
                $types = [
                    ['value' => 'web',       'label' => 'Web / Frontend', 'icon' => 'fas fa-globe',       'color' => 'from-blue-500 to-cyan-500'],
                    ['value' => 'backend',   'label' => 'Backend / API',  'icon' => 'fas fa-server',      'color' => 'from-violet-500 to-purple-500'],
                    ['value' => 'fullstack', 'label' => 'Full Stack',     'icon' => 'fas fa-layer-group', 'color' => 'from-pink-500 to-rose-500'],
                    ['value' => 'mobile',    'label' => 'Mobile',         'icon' => 'fas fa-mobile-alt',  'color' => 'from-green-500 to-emerald-500'],
                    ['value' => 'devops',    'label' => 'DevOps / Cloud', 'icon' => 'fas fa-cloud',       'color' => 'from-amber-500 to-orange-500'],
                    ['value' => 'ai',        'label' => 'AI / ML',        'icon' => 'fas fa-brain',       'color' => 'from-red-500 to-pink-500'],
                    ['value' => 'other',     'label' => 'Other',          'icon' => 'fas fa-code',        'color' => 'from-gray-500 to-slate-500'],
                ];
                @endphp

                @foreach($types as $type)
                <button type="button"
                    x-on:click="selected = '{{ $type['value'] }}'; document.getElementById('project_type_input').value = '{{ $type['value'] }}';"
                    :class="selected === '{{ $type['value'] }}' ? 'ring-2 ring-white scale-105 opacity-100' : 'opacity-60 hover:opacity-90'"
                    class="relative flex flex-col items-center gap-2 p-4 rounded-xl bg-gradient-to-br {{ $type['color'] }} bg-opacity-20 border border-white/10 transition-all duration-200 cursor-pointer">
                    <i class="{{ $type['icon'] }} text-2xl text-white"></i>
                    <span class="text-xs font-bold text-white text-center leading-tight">{{ $type['label'] }}</span>
                    <span x-show="selected === '{{ $type['value'] }}'" class="absolute top-2 right-2 w-4 h-4 bg-white rounded-full flex items-center justify-center">
                        <i class="fas fa-check text-indigo-600 text-xs"></i>
                    </span>
                </button>
                @endforeach
            </div>
        </div>

        <!-- Skills Card -->
        <div class="glow-border rounded-2xl p-8 bg-gradient-to-br from-blue-900/40 to-blue-950/40 backdrop-blur">
            <h2 class="font-display text-2xl font-bold text-white mb-6 flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-code"></i>
                </div>
                Technologies & Skills
            </h2>

            <div>
                <label class="block text-sm font-bold text-blue-200 mb-2">Technologies Used</label>
                <input type="text" name="tags" id="tag_input"
                    placeholder="React, Node.js, MongoDB..."
                    class="w-full bg-blue-950/50 border-2 border-blue-500/30 rounded-xl px-4 py-3 text-white placeholder-blue-400/50 focus:border-blue-400 focus:outline-none transition"
                    value="{{ old('tags', $project->skills->pluck('name')->join(', ')) }}">
                <p class="text-xs text-blue-300 mt-2">Separate with commas.</p>
            </div>

            <div class="mt-4">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $project->is_featured) ? 'checked' : '' }}
                        class="w-5 h-5 rounded border-blue-500/30 bg-blue-950/50 text-purple-500 focus:ring-purple-500">
                    <span class="text-blue-200 font-bold text-sm">Feature this project on my public profile</span>
                </label>
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
                @if($project->thumbnail)
                    <div class="w-24 h-24 rounded-lg overflow-hidden border-2 border-pink-500/30 shrink-0">
                        <img src="{{ $project->thumbnail }}" class="w-full h-full object-cover" alt="Current Thumbnail">
                    </div>
                @endif
                <label class="flex-1 cursor-pointer">
                    <div class="border-2 border-dashed border-pink-500/30 rounded-xl p-8 hover:border-pink-400 transition text-center">
                        <i class="fas fa-cloud-upload-alt text-4xl text-pink-400 mb-3"></i>
                        <p class="text-pink-200 font-bold mb-1">Click to upload new thumbnail</p>
                        <p class="text-xs text-pink-400">PNG, JPG up to 2MB (Replaces old image)</p>
                    </div>
                    <input type="file" name="thumbnail" accept="image/*" class="hidden">
                </label>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex items-center justify-between">
            <a href="{{ route('projects.show', $project) }}" class="px-6 py-3 bg-gray-700 hover:bg-gray-600 rounded-xl font-bold transition">
                Cancel
            </a>
            <button type="submit"
                class="btn-glow bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-500 hover:to-pink-500 px-8 py-3 rounded-xl font-display font-bold shadow-lg transition">
                <span class="relative z-10 flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    Save Changes
                </span>
            </button>
        </div>
    </form>
</div>
@endsection
