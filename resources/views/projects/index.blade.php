@extends('layouts.app')

@section('title', 'My Projects')
@section('page_title', 'My Projects')
@section('page_subtitle', 'Your portfolio — every project earns XP and unlocks skills')

@section('content')
@php
$typeConfig = [
    'web'       => ['label' => 'Web',      'icon' => 'fas fa-globe',       'gradient' => 'from-blue-500 to-cyan-500'],
    'backend'   => ['label' => 'Backend',  'icon' => 'fas fa-server',      'gradient' => 'from-violet-500 to-purple-500'],
    'fullstack' => ['label' => 'Full Stack','icon' => 'fas fa-layer-group','gradient' => 'from-pink-500 to-rose-500'],
    'mobile'    => ['label' => 'Mobile',   'icon' => 'fas fa-mobile-alt',  'gradient' => 'from-green-500 to-emerald-500'],
    'devops'    => ['label' => 'DevOps',   'icon' => 'fas fa-cloud',       'gradient' => 'from-amber-500 to-orange-500'],
    'ai'        => ['label' => 'AI / ML',  'icon' => 'fas fa-brain',       'gradient' => 'from-red-500 to-pink-500'],
    'other'     => ['label' => 'Other',    'icon' => 'fas fa-code',        'gradient' => 'from-gray-500 to-slate-500'],
];
@endphp

<div>
    {{-- ── Header row ── --}}
    <div class="flex items-center justify-between mb-5">
        <p class="text-xs font-bold uppercase tracking-widest text-purple-400/70">
            <i class="fas fa-folder-open mr-1"></i>
            {{ $projects->total() }} project{{ $projects->total() !== 1 ? 's' : '' }}
        </p>
        <a href="{{ route('projects.create') }}"
           class="btn-glow px-4 py-2 rounded-lg font-bold text-sm flex items-center gap-2">
            <i class="fas fa-plus text-xs"></i> New Project
        </a>
    </div>

    {{-- ── Type filter tabs ── --}}
    <div class="flex flex-wrap gap-1.5 mb-5">
        <a href="{{ route('projects.index') }}"
           class="px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider transition border
               {{ !request('type') ? 'bg-white/10 border-white/20 text-white' : 'border-white/10 text-purple-400 hover:border-white/20 hover:text-white' }}">
            All
        </a>
        @foreach($typeConfig as $value => $cfg)
        <a href="{{ route('projects.index', ['type' => $value]) }}"
           class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider transition border
               {{ request('type') === $value ? 'bg-white/10 border-white/20 text-white' : 'border-white/10 text-purple-400 hover:border-white/20 hover:text-white' }}">
            <i class="{{ $cfg['icon'] }}"></i> {{ $cfg['label'] }}
        </a>
        @endforeach
    </div>

    @if($projects->isEmpty())
    {{-- ── Empty state ── --}}
    <div class="rounded-xl border border-white/10 bg-purple-950/30 backdrop-blur p-16 text-center">
        <div class="w-14 h-14 bg-purple-500/10 border border-purple-500/20 rounded-xl flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-rocket text-2xl text-purple-400"></i>
        </div>
        <h3 class="text-lg font-bold text-white mb-2">
            {{ request('type') ? 'No ' . ($typeConfig[request('type')]['label'] ?? '') . ' projects yet' : 'No projects yet' }}
        </h3>
        <p class="text-sm text-purple-300 mb-6 max-w-sm mx-auto">
            Every project you add earns XP and counts toward unlocking skill tree nodes.
        </p>
        <a href="{{ route('projects.create') }}"
           class="btn-glow px-6 py-2 rounded-lg font-bold text-sm inline-flex items-center gap-2">
            <i class="fas fa-plus text-xs"></i> Add Your First Project
        </a>
    </div>

    @else
    {{-- ── Project grid ── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($projects as $project)
        @php
            $type = $project->project_type ?? 'other';
            $cfg  = $typeConfig[$type] ?? $typeConfig['other'];
        @endphp
        <div class="rounded-xl overflow-hidden border border-white/10 bg-purple-950/30 backdrop-blur flex flex-col group hover:border-purple-500/30 transition-all duration-200">

            {{-- Thumbnail / placeholder --}}
            @if($project->thumbnail)
            <div class="relative h-36 overflow-hidden">
                <img src="{{ $project->thumbnail }}"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                     alt="{{ $project->name }}">
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
                <div class="absolute top-2.5 left-2.5">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-bold text-white bg-gradient-to-r {{ $cfg['gradient'] }}">
                        <i class="{{ $cfg['icon'] }} text-xs"></i> {{ $cfg['label'] }}
                    </span>
                </div>
                @if($project->is_featured)
                <div class="absolute top-2.5 right-2.5">
                    <span class="px-2 py-0.5 bg-amber-500/80 rounded-md text-xs font-bold text-white">
                        <i class="fas fa-star text-xs"></i>
                    </span>
                </div>
                @endif
            </div>
            @else
            <div class="relative h-24 bg-gradient-to-br {{ $cfg['gradient'] }} opacity-10 flex items-center justify-center">
                <i class="{{ $cfg['icon'] }} text-4xl text-white/30"></i>
                <div class="absolute top-2.5 left-2.5">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-bold text-white bg-gradient-to-r {{ $cfg['gradient'] }}">
                        <i class="{{ $cfg['icon'] }} text-xs"></i> {{ $cfg['label'] }}
                    </span>
                </div>
                @if($project->is_featured)
                <div class="absolute top-2.5 right-2.5">
                    <span class="px-2 py-0.5 bg-amber-500/80 rounded-md text-xs font-bold text-white">
                        <i class="fas fa-star text-xs"></i>
                    </span>
                </div>
                @endif
            </div>
            @endif

            {{-- Card body --}}
            <div class="p-4 flex flex-col flex-1">
                <h3 class="font-bold text-white text-sm mb-1 truncate">{{ $project->name }}</h3>

                <div class="flex items-center gap-3 text-xs text-purple-400 mb-2">
                    <span class="flex items-center gap-1">
                        <i class="fas fa-code"></i> {{ $project->language }}
                    </span>
                    <span class="flex items-center gap-1 text-amber-400 font-bold">
                        <i class="fas fa-bolt"></i> +{{ $project->xp_reward }} XP
                    </span>
                    <span class="ml-auto flex items-center gap-1 text-purple-500">
                        <i class="fas fa-clock"></i> {{ $project->created_at->diffForHumans(null, true) }}
                    </span>
                </div>

                @if($project->description)
                <p class="text-xs text-purple-300 leading-relaxed line-clamp-2 mb-3">{{ $project->description }}</p>
                @endif

                @if($project->skills->count() > 0)
                <div class="flex flex-wrap gap-1 mb-3">
                    @foreach($project->skills->take(4) as $skill)
                    <span class="px-2 py-0.5 bg-purple-500/20 border border-purple-500/20 rounded text-xs text-purple-300">
                        {{ $skill->name }}
                    </span>
                    @endforeach
                    @if($project->skills->count() > 4)
                    <span class="px-2 py-0.5 bg-purple-500/10 rounded text-xs text-purple-500">
                        +{{ $project->skills->count() - 4 }}
                    </span>
                    @endif
                </div>
                @endif

                {{-- Actions --}}
                <div class="flex items-center gap-1.5 mt-auto pt-3 border-t border-white/5">
                    <a href="{{ route('projects.show', $project) }}"
                       class="btn-secondary flex-1 text-center px-2 py-1.5 text-xs font-bold">
                        <i class="fas fa-eye mr-1"></i> View
                    </a>
                    <a href="{{ route('projects.edit', $project) }}"
                       class="btn-secondary px-2 py-1.5 text-xs font-bold">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('projects.destroy', $project) }}" method="POST"
                          onsubmit="return confirm('Delete \'{{ addslashes($project->name) }}\'? This cannot be undone.')">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="btn-danger px-2 py-1.5 text-xs font-bold">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                    @if($project->github_url)
                    <a href="{{ $project->github_url }}" target="_blank"
                       class="btn-secondary px-2 py-1.5 text-xs font-bold">
                        <i class="fab fa-github"></i>
                    </a>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @if($projects->hasPages())
    <div class="mt-8 flex justify-center">
        {{ $projects->appends(request()->query())->links() }}
    </div>
    @endif
    @endif
</div>
@endsection
