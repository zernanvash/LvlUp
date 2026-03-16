@extends('layouts.app')

@section('title', 'My Projects')
@section('page_title', 'My Projects')
@section('page_subtitle', 'Your portfolio — every project earns XP and unlocks skills')

@section('content')
@php
$typeConfig = [
    'web'       => ['label' => 'Web / Frontend', 'icon' => 'fas fa-globe',       'color' => 'blue',   'gradient' => 'from-blue-500 to-cyan-500'],
    'backend'   => ['label' => 'Backend / API',  'icon' => 'fas fa-server',      'color' => 'violet', 'gradient' => 'from-violet-500 to-purple-500'],
    'fullstack' => ['label' => 'Full Stack',     'icon' => 'fas fa-layer-group', 'color' => 'pink',   'gradient' => 'from-pink-500 to-rose-500'],
    'mobile'    => ['label' => 'Mobile',         'icon' => 'fas fa-mobile-alt',  'color' => 'green',  'gradient' => 'from-green-500 to-emerald-500'],
    'devops'    => ['label' => 'DevOps / Cloud', 'icon' => 'fas fa-cloud',       'color' => 'amber',  'gradient' => 'from-amber-500 to-orange-500'],
    'ai'        => ['label' => 'AI / ML',        'icon' => 'fas fa-brain',       'color' => 'red',    'gradient' => 'from-red-500 to-pink-500'],
    'other'     => ['label' => 'Other',          'icon' => 'fas fa-code',        'color' => 'gray',   'gradient' => 'from-gray-500 to-slate-500'],
];
@endphp

<div x-data="{ filter: '{{ request('type', 'all') }}' }">

    <!-- Header Row -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <p class="text-purple-300 text-sm">
                <i class="fas fa-folder-open mr-1"></i>
                {{ $projects->total() }} project{{ $projects->total() !== 1 ? 's' : '' }} total
            </p>
        </div>
        <a href="{{ route('projects.create') }}"
           class="btn-glow bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-500 hover:to-pink-500 px-6 py-3 rounded-xl font-display font-bold shadow-lg transition flex items-center gap-2">
            <i class="fas fa-plus"></i> New Project
        </a>
    </div>

    <!-- Type Filter Tabs -->
    <div class="flex flex-wrap gap-2 mb-8">
        <a href="{{ route('projects.index') }}"
           class="px-4 py-2 rounded-xl text-sm font-bold transition border-2 {{ !request('type') ? 'bg-white/10 border-white/30 text-white' : 'border-white/10 text-purple-300 hover:border-white/20 hover:text-white' }}">
            <i class="fas fa-th mr-1"></i> All
        </a>
        @foreach($typeConfig as $value => $cfg)
        <a href="{{ route('projects.index', ['type' => $value]) }}"
           class="px-4 py-2 rounded-xl text-sm font-bold transition border-2 {{ request('type') === $value ? 'bg-white/10 border-white/30 text-white' : 'border-white/10 text-purple-300 hover:border-white/20 hover:text-white' }}">
            <i class="{{ $cfg['icon'] }} mr-1"></i> {{ $cfg['label'] }}
        </a>
        @endforeach
    </div>

    @if($projects->isEmpty())
    <!-- Empty State -->
    <div class="glow-border rounded-2xl p-16 bg-gradient-to-br from-purple-900/40 to-purple-950/40 backdrop-blur text-center">
        <div class="w-20 h-20 bg-gradient-to-br from-purple-500/20 to-pink-500/20 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-rocket text-4xl text-purple-400"></i>
        </div>
        <h3 class="font-display text-2xl font-bold text-white mb-3">
            {{ request('type') ? 'No ' . ($typeConfig[request('type')]['label'] ?? '') . ' projects yet' : 'No projects yet' }}
        </h3>
        <p class="text-purple-300 mb-8 max-w-md mx-auto">
            Every project you add earns XP and counts toward unlocking skill tree nodes. Start building your portfolio!
        </p>
        <a href="{{ route('projects.create') }}"
           class="btn-glow bg-gradient-to-r from-purple-600 to-pink-600 px-8 py-3 rounded-xl font-display font-bold inline-flex items-center gap-2">
            <i class="fas fa-plus"></i> Add Your First Project
        </a>
    </div>
    @else

    <!-- Project Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @foreach($projects as $project)
        @php
            $type = $project->project_type ?? 'other';
            $cfg  = $typeConfig[$type] ?? $typeConfig['other'];
        @endphp
        <div class="glow-border rounded-2xl overflow-hidden bg-gradient-to-br from-purple-900/40 to-purple-950/40 backdrop-blur flex flex-col group hover:scale-[1.02] transition-transform duration-200">

            <!-- Thumbnail / Placeholder -->
            @if($project->thumbnail)
            <div class="relative h-40 overflow-hidden">
                <img src="{{ $project->thumbnail }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" alt="{{ $project->name }}">
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
                <!-- Type badge over image -->
                <div class="absolute top-3 left-3">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold text-white bg-gradient-to-r {{ $cfg['gradient'] }} shadow-lg">
                        <i class="{{ $cfg['icon'] }}"></i> {{ $cfg['label'] }}
                    </span>
                </div>
                @if($project->is_featured)
                <div class="absolute top-3 right-3">
                    <span class="px-2 py-1 bg-amber-500/80 rounded-lg text-xs font-bold text-white">
                        <i class="fas fa-star"></i>
                    </span>
                </div>
                @endif
            </div>
            @else
            <!-- Gradient placeholder with type icon -->
            <div class="relative h-32 bg-gradient-to-br {{ $cfg['gradient'] }} opacity-20 flex items-center justify-center">
                <i class="{{ $cfg['icon'] }} text-5xl text-white/40"></i>
                <div class="absolute top-3 left-3">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold text-white bg-gradient-to-r {{ $cfg['gradient'] }} shadow-lg">
                        <i class="{{ $cfg['icon'] }}"></i> {{ $cfg['label'] }}
                    </span>
                </div>
                @if($project->is_featured)
                <div class="absolute top-3 right-3">
                    <span class="px-2 py-1 bg-amber-500/80 rounded-lg text-xs font-bold text-white">
                        <i class="fas fa-star"></i>
                    </span>
                </div>
                @endif
            </div>
            @endif

            <!-- Card Body -->
            <div class="p-5 flex flex-col flex-1">
                <h3 class="font-display text-lg font-bold text-white mb-1 truncate">{{ $project->name }}</h3>

                <div class="flex items-center gap-3 text-xs text-purple-300 mb-3">
                    <span class="flex items-center gap-1">
                        <i class="fas fa-code"></i> {{ $project->language }}
                    </span>
                    <span class="flex items-center gap-1 text-amber-400 font-bold">
                        <i class="fas fa-bolt"></i> +{{ $project->xp_reward }} XP
                    </span>
                    <span class="flex items-center gap-1 ml-auto text-purple-400">
                        <i class="fas fa-clock"></i> {{ $project->created_at->diffForHumans(null, true) }}
                    </span>
                </div>

                @if($project->description)
                <p class="text-purple-200 text-sm leading-relaxed line-clamp-2 mb-4">{{ $project->description }}</p>
                @endif

                <!-- Skills -->
                @if($project->skills->count() > 0)
                <div class="flex flex-wrap gap-1.5 mb-4">
                    @foreach($project->skills->take(4) as $skill)
                    <span class="px-2 py-0.5 bg-purple-500/20 border border-purple-500/30 rounded-md text-xs text-purple-200">
                        {{ $skill->name }}
                    </span>
                    @endforeach
                    @if($project->skills->count() > 4)
                    <span class="px-2 py-0.5 bg-purple-500/10 rounded-md text-xs text-purple-400">
                        +{{ $project->skills->count() - 4 }} more
                    </span>
                    @endif
                </div>
                @endif

                <!-- Actions -->
                <div class="flex items-center gap-2 mt-auto pt-3 border-t border-white/5">
                    <a href="{{ route('projects.show', $project) }}"
                       class="flex-1 text-center px-3 py-2 bg-purple-600/30 hover:bg-purple-600/50 border border-purple-500/30 rounded-lg text-sm font-bold text-purple-200 transition">
                        <i class="fas fa-eye mr-1"></i> View
                    </a>
                    <a href="{{ route('projects.edit', $project) }}"
                       class="px-3 py-2 bg-blue-600/30 hover:bg-blue-600/50 border border-blue-500/30 rounded-lg text-sm font-bold text-blue-200 transition">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('projects.destroy', $project) }}" method="POST"
                          onsubmit="return confirm('Delete \'{{ addslashes($project->name) }}\'? This cannot be undone.')">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="px-3 py-2 bg-red-600/30 hover:bg-red-600/50 border border-red-500/30 rounded-lg text-sm font-bold text-red-300 transition">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                    @if($project->github_url)
                    <a href="{{ $project->github_url }}" target="_blank"
                       class="px-3 py-2 bg-gray-600/30 hover:bg-gray-600/50 border border-gray-500/30 rounded-lg text-sm font-bold text-gray-300 transition">
                        <i class="fab fa-github"></i>
                    </a>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    @if($projects->hasPages())
    <div class="mt-10 flex justify-center">
        {{ $projects->appends(request()->query())->links() }}
    </div>
    @endif

    @endif
</div>
@endsection
