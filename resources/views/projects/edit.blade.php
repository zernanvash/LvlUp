@extends('layouts.app')

@section('title', 'Edit Project')
@section('page_title', 'Edit Project')
@section('page_subtitle', 'Update your portfolio entry')

@section('content')
<div class="max-w-3xl mx-auto" x-data="{ projectType: '{{ old('project_type', $project->project_type) }}' }">
    <form action="{{ route('projects.update', $project) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
        @csrf
        @method('PUT')
        <input type="hidden" name="project_type" id="project_type_input" :value="projectType" required>

        {{-- ── Project Info ── --}}
        <div class="rounded-xl overflow-hidden border border-white/10 bg-purple-950/30 backdrop-blur">
            <div class="flex items-center gap-2 px-4 py-2.5 bg-white/5 border-b border-white/10">
                <i class="fas fa-file-alt text-purple-400 text-xs"></i>
                <span class="text-xs font-bold uppercase tracking-widest text-purple-200">Project Info</span>
            </div>

            <div class="p-4 grid grid-cols-1 md:grid-cols-3 gap-3">
                <div class="md:col-span-3">
                    <label class="block text-xs font-bold uppercase tracking-wider text-purple-300 mb-1.5">
                        Project Name <span class="text-red-400">*</span>
                    </label>
                    <input type="text" name="name" required
                        placeholder="e.g. Task Manager API"
                        class="w-full px-3 py-2"
                        value="{{ old('name', $project->name) }}">
                    @error('name')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-purple-300 mb-1.5">
                        Language <span class="text-red-400">*</span>
                    </label>
                    <select name="language" required
                        class="w-full px-3 py-2">
                        @foreach(['PHP','JavaScript','TypeScript','Python','Java','C++','C#','Ruby','Go','Rust'] as $lang)
                            <option value="{{ $lang }}" {{ old('language', $project->language) == $lang ? 'selected' : '' }}>{{ $lang }}</option>
                        @endforeach
                    </select>
                    @error('language')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-purple-300 mb-1.5">Live URL</label>
                    <div class="relative">
                        <i class="fas fa-globe absolute left-3 top-1/2 -translate-y-1/2 text-purple-400/60 text-xs pointer-events-none"></i>
                        <input type="url" name="url" placeholder="https://..."
                            class="w-full pl-8 pr-3 py-2"
                            value="{{ old('url', $project->url) }}">
                    </div>
                    @error('url')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-purple-300 mb-1.5">GitHub</label>
                    <div class="relative">
                        <i class="fab fa-github absolute left-3 top-1/2 -translate-y-1/2 text-purple-400/60 text-xs pointer-events-none"></i>
                        <input type="url" name="github_url" placeholder="github.com/..."
                            class="w-full pl-8 pr-3 py-2"
                            value="{{ old('github_url', $project->github_url) }}">
                    </div>
                    @error('github_url')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-3">
                    <label class="block text-xs font-bold uppercase tracking-wider text-purple-300 mb-1.5">Description</label>
                    <textarea name="description" rows="2"
                        placeholder="What does this project do? What problems does it solve?"
                        class="w-full px-3 py-2 resize-none"
                    >{{ old('description', $project->description) }}</textarea>
                    @error('description')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
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

            @error('project_type')<p class="text-red-400 text-xs px-4 pt-3">{{ $message }}</p>@enderror

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
                    <button type="button"
                        x-on:click="projectType = '{{ $type['value'] }}'"
                        :class="projectType === '{{ $type['value'] }}'
                            ? 'border-indigo-400 bg-indigo-500/20 text-indigo-200 ring-1 ring-indigo-400/50'
                            : 'border-white/10 bg-white/5 text-purple-300/60 hover:border-white/20 hover:text-purple-200'"
                        class="flex flex-col items-center gap-1.5 py-3 rounded-lg border transition-all duration-150 cursor-pointer">
                        <i class="{{ $type['icon'] }} text-base"></i>
                        <span class="text-xs font-bold leading-tight text-center">{{ $type['label'] }}</span>
                    </button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ── Skills ── --}}
        <div class="rounded-xl overflow-hidden border border-white/10 bg-blue-950/30 backdrop-blur">
            <div class="flex items-center gap-2 px-4 py-2.5 bg-white/5 border-b border-white/10">
                <i class="fas fa-magic text-blue-400 text-xs"></i>
                <span class="text-xs font-bold uppercase tracking-widest text-blue-200">Skills & Technologies</span>
            </div>

            <div class="p-4 space-y-3">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-blue-300 mb-1.5">Technologies & Skills</label>
                    <input type="text" name="tags" id="tag_input"
                        placeholder="React, Node.js, MongoDB..."
                        class="w-full px-3 py-2"
                        value="{{ old('tags', $project->skills->pluck('name')->join(', ')) }}">
                    <p class="text-xs text-blue-400/60 mt-1">Separate with commas.</p>
                </div>

                <label class="flex items-center gap-2.5 cursor-pointer select-none">
                    <input type="checkbox" name="is_featured" value="1"
                        {{ old('is_featured', $project->is_featured) ? 'checked' : '' }}
                        class="rounded accent-purple-500">
                    <span class="text-xs font-bold uppercase tracking-wider text-blue-300">Feature on public profile</span>
                </label>
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
                <div class="flex items-center gap-3">
                    @if($project->thumbnail)
                    <div class="w-16 h-16 rounded-lg overflow-hidden border border-pink-500/30 flex-shrink-0">
                        <img src="{{ $project->thumbnail }}" class="w-full h-full object-cover" alt="Current Thumbnail">
                    </div>
                    @endif
                    <label class="flex-1 cursor-pointer">
                        <div class="flex items-center gap-3 border border-dashed border-pink-500/30 rounded-lg px-4 py-3 hover:border-pink-400/60 transition">
                            <i class="fas fa-cloud-upload-alt text-xl text-pink-400/60 flex-shrink-0"></i>
                            <div>
                                <p class="text-sm text-pink-200 font-bold">
                                    {{ $project->thumbnail ? 'Click to replace thumbnail' : 'Click to upload thumbnail' }}
                                </p>
                                <p class="text-xs text-pink-400/60 mt-0.5">PNG, JPG up to 2 MB</p>
                            </div>
                            <p id="file_name" class="ml-auto text-xs text-pink-300 hidden"></p>
                        </div>
                        <input type="file" name="thumbnail" accept="image/*" class="hidden" id="thumbnail_input">
                    </label>
                </div>
            </div>
        </div>

        {{-- ── Actions ── --}}
        <div class="flex items-center justify-between pt-1">
            <a href="{{ route('projects.show', $project) }}"
               class="btn-secondary px-5 py-2 text-sm">
                Cancel
            </a>
            <button type="submit"
                class="btn-glow px-6 py-2 rounded-lg font-bold text-sm flex items-center gap-2">
                <i class="fas fa-save"></i> Save Changes
            </button>
        </div>
    </form>
</div>

<script>
    document.getElementById('thumbnail_input').addEventListener('change', function () {
        const label = document.getElementById('file_name');
        if (this.files.length > 0) {
            label.textContent = this.files[0].name;
            label.classList.remove('hidden');
        }
    });
</script>
@endsection
