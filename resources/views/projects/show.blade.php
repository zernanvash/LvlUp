@extends('layouts.app')

@section('title', $project->name)
@section('page_title', $project->name)
@section('page_subtitle', 'Project Details')

@section('content')
@php
$typeConfig = [
    'web'       => ['label' => 'Web',       'icon' => 'fas fa-globe',       'gradient' => 'from-blue-500 to-cyan-500'],
    'backend'   => ['label' => 'Backend',   'icon' => 'fas fa-server',      'gradient' => 'from-violet-500 to-purple-500'],
    'fullstack' => ['label' => 'Full Stack','icon' => 'fas fa-layer-group', 'gradient' => 'from-pink-500 to-rose-500'],
    'mobile'    => ['label' => 'Mobile',    'icon' => 'fas fa-mobile-alt',  'gradient' => 'from-green-500 to-emerald-500'],
    'devops'    => ['label' => 'DevOps',    'icon' => 'fas fa-cloud',       'gradient' => 'from-amber-500 to-orange-500'],
    'ai'        => ['label' => 'AI / ML',   'icon' => 'fas fa-brain',       'gradient' => 'from-red-500 to-pink-500'],
    'other'     => ['label' => 'Other',     'icon' => 'fas fa-code',        'gradient' => 'from-gray-500 to-slate-500'],
];
$type = $project->project_type ?? 'other';
$cfg  = $typeConfig[$type] ?? $typeConfig['other'];
@endphp

<div class="max-w-4xl mx-auto space-y-3">

    {{-- ── Header card ── --}}
    <div class="rounded-xl overflow-hidden border border-white/10 bg-purple-950/30 backdrop-blur">

        {{-- Thumbnail --}}
        @if($project->thumbnail)
        <div class="relative h-52 overflow-hidden">
            <img src="{{ $project->thumbnail }}" class="w-full h-full object-cover" alt="{{ $project->name }}">
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent"></div>
        </div>
        @endif

        {{-- Strip header --}}
        <div class="flex items-center gap-2 px-4 py-2.5 bg-white/5 border-b border-white/10">
            <i class="fas fa-folder-open text-purple-400 text-xs"></i>
            <span class="text-xs font-bold uppercase tracking-widest text-purple-200">Project</span>
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-bold text-white bg-gradient-to-r {{ $cfg['gradient'] }} ml-1">
                <i class="{{ $cfg['icon'] }} text-xs"></i> {{ $cfg['label'] }}
            </span>
            @if($project->is_featured)
            <span class="px-2 py-0.5 bg-amber-500/20 border border-amber-500/30 rounded-md text-xs font-bold text-amber-300">
                <i class="fas fa-star text-xs"></i> Featured
            </span>
            @endif
            <div class="ml-auto flex items-center gap-1.5">
                @if($project->github_url)
                <a href="{{ $project->github_url }}" target="_blank"
                   class="btn-secondary px-3 py-1 text-xs font-bold flex items-center gap-1">
                    <i class="fab fa-github"></i> GitHub
                </a>
                @endif
                @if($project->url)
                <a href="{{ $project->url }}" target="_blank"
                   class="btn-secondary px-3 py-1 text-xs font-bold flex items-center gap-1">
                    <i class="fas fa-external-link-alt"></i> Live
                </a>
                @endif
                <a href="{{ route('projects.edit', $project) }}"
                   class="btn-secondary px-3 py-1 text-xs font-bold flex items-center gap-1">
                    <i class="fas fa-edit"></i> Edit
                </a>
            </div>
        </div>

        <div class="p-4 space-y-4">
            {{-- Title + meta --}}
            <div>
                <h1 class="text-xl font-bold text-white mb-1">{{ $project->name }}</h1>
                <div class="flex items-center gap-4 text-xs text-purple-400">
                    <span class="flex items-center gap-1"><i class="fas fa-code"></i> {{ $project->language }}</span>
                    <span class="flex items-center gap-1"><i class="fas fa-calendar"></i> {{ $project->created_at->format('M d, Y') }}</span>
                    <span class="flex items-center gap-1 text-amber-400 font-bold"><i class="fas fa-bolt"></i> +{{ $project->xp_reward }} XP earned</span>
                </div>
            </div>

            {{-- Description --}}
            @if($project->description)
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-purple-400/70 mb-1">Description</p>
                <p class="text-sm text-purple-100 leading-relaxed">{{ $project->description }}</p>
            </div>
            @endif

            {{-- Skills --}}
            @if($project->skills->count() > 0)
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-purple-400/70 mb-2">Technologies Used</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($project->skills as $skill)
                    @php
                        $rarityColors = ['common' => 'gray', 'rare' => 'blue', 'epic' => 'purple', 'legendary' => 'amber'];
                        $color = $rarityColors[$skill->rarity] ?? 'gray';
                    @endphp
                    <div class="flex items-center gap-1.5 px-3 py-1 bg-{{ $color }}-500/10 border border-{{ $color }}-500/20 rounded-lg">
                        <i class="{{ $skill->icon }} text-{{ $color }}-400 text-xs"></i>
                        <span class="text-xs font-bold text-{{ $color }}-200">{{ $skill->name }}</span>
                        <div class="flex gap-0.5 ml-1">
                            @for($i = 0; $i < $skill->pivot->proficiency; $i++)
                                <i class="fas fa-star text-{{ $color }}-400 text-xs"></i>
                            @endfor
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- ── Stats row ── --}}
    <div class="grid grid-cols-3 gap-3">
        <div class="rounded-xl border border-white/10 bg-purple-950/30 backdrop-blur p-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-purple-500/20 border border-purple-500/20 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-bolt text-purple-400 text-sm"></i>
                </div>
                <div>
                    <p class="text-xs text-purple-400 uppercase tracking-wider font-bold">XP Earned</p>
                    <p class="text-lg font-bold text-white">{{ $project->xp_reward }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-white/10 bg-blue-950/30 backdrop-blur p-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-blue-500/20 border border-blue-500/20 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-network-wired text-blue-400 text-sm"></i>
                </div>
                <div>
                    <p class="text-xs text-blue-400 uppercase tracking-wider font-bold">Skills Used</p>
                    <p class="text-lg font-bold text-white">{{ $project->skills->count() }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-white/10 bg-pink-950/30 backdrop-blur p-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-pink-500/20 border border-pink-500/20 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-calendar text-pink-400 text-sm"></i>
                </div>
                <div>
                    <p class="text-xs text-pink-400 uppercase tracking-wider font-bold">Created</p>
                    <p class="text-sm font-bold text-white">{{ $project->created_at->diffForHumans() }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Code preview ── --}}
    @if($project->metadata && isset($project->metadata['code_snippet']))
    <div class="rounded-xl overflow-hidden border border-white/10 bg-blue-950/30 backdrop-blur">
        <div class="flex items-center gap-2 px-4 py-2.5 bg-white/5 border-b border-white/10">
            <i class="fas fa-code text-blue-400 text-xs"></i>
            <span class="text-xs font-bold uppercase tracking-widest text-blue-200">Code Preview</span>
            <span class="ml-auto text-xs text-blue-400/70">{{ $project->metadata['lines_of_code'] ?? 0 }} lines</span>
        </div>
        <div class="p-4">
            <pre class="bg-black/40 rounded-lg p-4 overflow-x-auto"><code class="text-cyan-300 text-xs font-mono leading-relaxed">{{ $project->metadata['code_snippet'] }}</code></pre>
        </div>
    </div>
    @endif

    {{-- ── Footer actions ── --}}
    <div class="flex items-center justify-between pt-1">
        <a href="{{ route('projects.index') }}"
           class="btn-secondary px-5 py-2 text-sm flex items-center gap-2">
            <i class="fas fa-arrow-left text-xs"></i> Back to Projects
        </a>
        <form action="{{ route('projects.destroy', $project) }}" method="POST"
              onsubmit="return confirm('Are you sure you want to delete this project?');">
            @csrf @method('DELETE')
            <button type="submit"
                    class="btn-danger px-5 py-2 text-sm font-bold flex items-center gap-2">
                <i class="fas fa-trash text-xs"></i> Delete Project
            </button>
        </form>
    </div>

</div>
@endsection
