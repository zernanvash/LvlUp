@extends('layouts.app')

@section('title', $node->title)
@section('page_title', $node->title)
@section('page_subtitle', 'Skill Node Details')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('skill-tree.index') }}" class="text-purple-400 hover:text-purple-300 flex items-center gap-2">
            <i class="fas fa-arrow-left"></i>
            Back to Skill Tree
        </a>
    </div>

    <!-- Node Details Card -->
    <div class="glow-border rounded-2xl p-8 bg-gradient-to-br from-purple-900/40 to-purple-950/40 backdrop-blur">
        <div class="flex items-start gap-6 mb-8">
            @if($node->skill)
            <div class="w-20 h-20 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center flex-shrink-0">
                <i class="{{ $node->skill->icon }} text-3xl text-white"></i>
            </div>
            @endif
            
            <div class="flex-1">
                <h2 class="font-display text-3xl font-bold text-white mb-2">{{ $node->title }}</h2>
                <p class="text-purple-300 mb-4">{{ $node->description }}</p>
                
                <!-- Status Badge -->
                @if($isUnlocked)
                <span class="inline-flex items-center gap-2 px-4 py-2 bg-green-500/20 border border-green-500/40 rounded-lg text-green-300">
                    <i class="fas fa-check-circle"></i>
                    Unlocked
                </span>
                @elseif($canUnlock)
                <span class="inline-flex items-center gap-2 px-4 py-2 bg-green-500/20 border border-green-500/40 rounded-lg text-green-300 animate-pulse">
                    <i class="fas fa-unlock"></i>
                    Available to Unlock
                </span>
                @else
                <span class="inline-flex items-center gap-2 px-4 py-2 bg-gray-500/20 border border-gray-500/40 rounded-lg text-gray-300">
                    <i class="fas fa-lock"></i>
                    Locked
                </span>
                @endif
            </div>
        </div>

        <!-- Requirements Section -->
        <div class="space-y-6">
            <h3 class="font-display text-xl font-bold text-white mb-4">Requirements</h3>
            
            <!-- Level Requirement -->
            <div class="bg-purple-900/30 rounded-xl p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-level-up-alt text-purple-400"></i>
                        <span class="text-purple-200">Level Requirement</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="font-bold {{ $requirements['level']['met'] ? 'text-green-400' : 'text-red-400' }}">
                            {{ $requirements['level']['current'] }} / {{ $requirements['level']['required'] }}
                        </span>
                        @if($requirements['level']['met'])
                        <i class="fas fa-check-circle text-green-400"></i>
                        @else
                        <i class="fas fa-times-circle text-red-400"></i>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Parent Requirement -->
            @if($requirements['parent'])
            <div class="bg-purple-900/30 rounded-xl p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-sitemap text-purple-400"></i>
                        <span class="text-purple-200">Parent Node: {{ $requirements['parent']['node']->title }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($requirements['parent']['met'])
                        <span class="font-bold text-green-400">Unlocked</span>
                        <i class="fas fa-check-circle text-green-400"></i>
                        @else
                        <span class="font-bold text-red-400">Locked</span>
                        <i class="fas fa-times-circle text-red-400"></i>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Task Requirements -->
            @if(count($requirements['tasks']) > 0)
            <div class="space-y-3">
                <h4 class="font-bold text-purple-200">Task Requirements</h4>
                @foreach($requirements['tasks'] as $task)
                <div class="bg-purple-900/30 rounded-xl p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-purple-200">{{ $task['description'] }}</span>
                        <div class="flex items-center gap-2">
                            <span class="font-bold {{ $task['completed'] ? 'text-green-400' : 'text-yellow-400' }}">
                                {{ $task['current'] }} / {{ $task['required'] }}
                            </span>
                            @if($task['completed'])
                            <i class="fas fa-check-circle text-green-400"></i>
                            @else
                            <i class="fas fa-clock text-yellow-400"></i>
                            @endif
                        </div>
                    </div>
                    <!-- Progress Bar -->
                    <div class="w-full bg-purple-950/50 rounded-full h-2">
                        <div class="bg-gradient-to-r from-purple-500 to-purple-600 h-2 rounded-full transition-all" 
                             style="width: {{ min(100, ($task['current'] / max(1, $task['required'])) * 100) }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        <!-- Action Button -->
        <div class="mt-8">
            @if($isUnlocked)
            <div class="bg-green-500/20 border-2 border-green-500/40 rounded-xl p-6 text-center">
                <i class="fas fa-check-circle text-green-400 text-3xl mb-3"></i>
                <p class="text-green-300 font-bold text-lg">You have already unlocked this skill node!</p>
            </div>
            @elseif($canUnlock)
            <form action="{{ route('skill-tree.unlock', $node) }}" method="POST">
                @csrf
                <button type="submit" class="w-full btn-glow bg-gradient-to-r from-green-600 to-green-700 hover:from-green-500 hover:to-green-600 px-8 py-4 rounded-xl font-bold text-lg shadow-lg transition">
                    <span class="relative z-10 flex items-center justify-center gap-3">
                        <i class="fas fa-unlock"></i>
                        Unlock This Skill Node
                    </span>
                </button>
            </form>
            @else
            <div class="bg-red-500/20 border-2 border-red-500/40 rounded-xl p-6 text-center">
                <i class="fas fa-lock text-red-400 text-3xl mb-3"></i>
                <p class="text-red-300 font-bold text-lg">Requirements Not Met</p>
                <p class="text-red-300/70 mt-2">Complete the requirements above to unlock this node.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
