@extends('layouts.app')

@section('title', $project->name)
@section('page_title', $project->name)
@section('page_subtitle', 'Project Details')

@section('content')
<div class="max-w-5xl mx-auto space-y-8">
    
    <!-- Project Header -->
    <div class="glow-border rounded-2xl overflow-hidden bg-gradient-to-br from-purple-900/40 to-purple-950/40 backdrop-blur">
        <!-- Thumbnail -->
        @if($project->thumbnail)
        <div class="relative h-64 bg-gradient-to-br from-purple-600/20 to-pink-600/20 overflow-hidden">
            <img src="{{ $project->thumbnail }}" class="w-full h-full object-cover" alt="{{ $project->name }}">
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent"></div>
        </div>
        @endif
        
        <div class="p-8">
            <!-- Title and Actions -->
            <div class="flex items-start justify-between mb-6">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-3">
                        <h1 class="font-display text-3xl font-bold text-white">{{ $project->name }}</h1>
                        @if($project->is_featured)
                        <span class="px-3 py-1 bg-amber-500/20 border border-amber-500/30 rounded-lg text-sm text-amber-300 font-bold">
                            <i class="fas fa-star"></i> Featured
                        </span>
                        @endif
                    </div>
                    
                    <div class="flex items-center gap-4 text-sm text-purple-300">
                        <span class="flex items-center gap-2">
                            <i class="fas fa-code"></i>
                            {{ $project->language }}
                        </span>
                        <span class="flex items-center gap-2">
                            <i class="fas fa-calendar"></i>
                            {{ $project->created_at->format('M d, Y') }}
                        </span>
                        <span class="flex items-center gap-2 text-amber-400 font-bold">
                            <i class="fas fa-bolt"></i>
                            +{{ $project->xp_reward }} XP
                        </span>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex items-center gap-3">
                    @if($project->github_url)
                    <a href="{{ $project->github_url }}" target="_blank" 
                       class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded-xl font-bold transition flex items-center gap-2">
                        <i class="fab fa-github"></i> GitHub
                    </a>
                    @endif
                    
                    @if($project->url)
                    <a href="{{ $project->url }}" target="_blank" 
                       class="px-4 py-2 bg-blue-600 hover:bg-blue-500 rounded-xl font-bold transition flex items-center gap-2">
                        <i class="fas fa-external-link-alt"></i> Live Demo
                    </a>
                    @endif
                    
                    <a href="{{ route('projects.edit', $project) }}" 
                       class="px-4 py-2 bg-purple-600 hover:bg-purple-500 rounded-xl font-bold transition flex items-center gap-2">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                </div>
            </div>
            
            <!-- Description -->
            @if($project->description)
            <div class="mb-6">
                <h3 class="text-lg font-bold text-white mb-3">Description</h3>
                <p class="text-purple-100 leading-relaxed">{{ $project->description }}</p>
            </div>
            @endif
            
            <!-- Skills Used -->
            @if($project->skills->count() > 0)
            <div>
                <h3 class="text-lg font-bold text-white mb-3">Technologies Used</h3>
                <div class="flex flex-wrap gap-3">
                    @foreach($project->skills as $skill)
                    @php
                        $rarityColors = [
                            'common' => 'gray',
                            'rare' => 'blue',
                            'epic' => 'purple',
                            'legendary' => 'amber'
                        ];
                        $color = $rarityColors[$skill->rarity];
                    @endphp
                    <div class="px-4 py-2 bg-{{ $color }}-500/20 border-2 border-{{ $color }}-500/30 rounded-xl flex items-center gap-2">
                        <i class="{{ $skill->icon }} text-{{ $color }}-400"></i>
                        <span class="font-bold text-{{ $color }}-200">{{ $skill->name }}</span>
                        <div class="flex gap-1 ml-2">
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
    
    <!-- Code Snippet -->
    @if($project->metadata && isset($project->metadata['code_snippet']))
    <div class="glow-border rounded-2xl p-8 bg-gradient-to-br from-blue-900/40 to-blue-950/40 backdrop-blur">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-display text-xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-code"></i> Code Preview
            </h3>
            <span class="text-sm text-blue-300">
                {{ $project->metadata['lines_of_code'] ?? 0 }} lines
            </span>
        </div>
        <pre class="bg-black/50 rounded-xl p-6 overflow-x-auto"><code class="text-cyan-300 text-sm font-mono">{{ $project->metadata['code_snippet'] }}</code></pre>
    </div>
    @endif
    
    <!-- Project Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="glow-border rounded-2xl p-6 bg-gradient-to-br from-purple-900/40 to-purple-950/40 backdrop-blur">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-bolt text-2xl text-white"></i>
                </div>
                <div>
                    <p class="text-sm text-purple-300">XP Earned</p>
                    <p class="font-display text-2xl font-bold text-white">{{ $project->xp_reward }}</p>
                </div>
            </div>
        </div>
        
        <div class="glow-border rounded-2xl p-6 bg-gradient-to-br from-blue-900/40 to-blue-950/40 backdrop-blur">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-network-wired text-2xl text-white"></i>
                </div>
                <div>
                    <p class="text-sm text-blue-300">Skills Used</p>
                    <p class="font-display text-2xl font-bold text-white">{{ $project->skills->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="glow-border rounded-2xl p-6 bg-gradient-to-br from-pink-900/40 to-pink-950/40 backdrop-blur">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-gradient-to-br from-pink-500 to-pink-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar text-2xl text-white"></i>
                </div>
                <div>
                    <p class="text-sm text-pink-300">Created</p>
                    <p class="font-display text-lg font-bold text-white">{{ $project->created_at->diffForHumans() }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Actions -->
    <div class="flex items-center justify-between">
        <a href="{{ route('dashboard') }}" class="px-6 py-3 bg-gray-700 hover:bg-gray-600 rounded-xl font-bold transition">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
        
        <form action="{{ route('projects.destroy', $project) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this project?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-6 py-3 bg-red-600 hover:bg-red-500 rounded-xl font-bold transition">
                <i class="fas fa-trash"></i> Delete Project
            </button>
        </form>
    </div>
</div>
@endsection
