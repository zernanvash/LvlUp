@extends('layouts.app')

@section('title', 'My Resumes')

@section('content')
<div class="py-10 px-6 max-w-7xl mx-auto">

        {{-- Page Header --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-white tracking-tight">My Resumes</h1>
                <p class="text-purple-300/60 text-sm mt-1">AI-tailored resumes for every opportunity</p>
            </div>
            <a href="{{ route('resume.create') }}"
                class="flex items-center gap-2 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-500 hover:to-pink-500 text-white font-semibold py-2.5 px-5 rounded-2xl transition-all duration-300 shadow-lg shadow-purple-900/40 hover:shadow-purple-700/50 hover:scale-105">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Resume
            </a>
        </div>

        {{-- Flash Message --}}
        @if(session('success'))
            <div class="mb-6 flex items-center gap-3 bg-green-900/30 border border-green-500/30 text-green-300 px-4 py-3 rounded-2xl backdrop-blur">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Resume Grid --}}
        @if($resumes->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($resumes as $resume)
                    <div class="group relative glow-border rounded-3xl overflow-hidden bg-gradient-to-br from-purple-900/40 via-pink-900/30 to-purple-900/40 backdrop-blur border border-purple-500/20 hover:border-purple-400/40 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:shadow-purple-900/50">

                        {{-- Animated gradient background --}}
                        <div class="absolute inset-0 bg-gradient-to-r from-purple-600/5 via-pink-600/5 to-purple-600/5 animate-pulse pointer-events-none"></div>

                        {{-- Card Top: Colour band with score --}}
                        <div class="relative h-28 bg-gradient-to-br from-purple-800/60 to-pink-900/60 flex items-center justify-center overflow-hidden">
                            {{-- Decorative circles --}}
                            <div class="absolute -top-6 -right-6 w-24 h-24 rounded-full bg-purple-500/10 blur-xl"></div>
                            <div class="absolute -bottom-4 -left-4 w-16 h-16 rounded-full bg-pink-500/10 blur-lg"></div>

                            {{-- Document icon --}}
                            <svg class="w-10 h-10 text-purple-300/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>

                            {{-- Match score badge --}}
                            @if($resume->match_score)
                                <div class="absolute top-3 right-3">
                                    <span class="flex items-center gap-1 text-xs font-bold px-2.5 py-1 rounded-full
                                        {{ $resume->match_score >= 75 ? 'bg-green-500/20 text-green-300 border border-green-500/30' :
                                           ($resume->match_score >= 50 ? 'bg-yellow-500/20 text-yellow-300 border border-yellow-500/30' :
                                           'bg-red-500/20 text-red-300 border border-red-500/30') }}">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                        {{ $resume->match_score }}% match
                                    </span>
                                </div>
                            @endif

                            {{-- Date badge --}}
                            <div class="absolute bottom-3 left-3">
                                <span class="text-xs text-purple-300/60">
                                    {{ $resume->created_at->format('M d, Y') }}
                                </span>
                            </div>
                        </div>

                        {{-- Card Body --}}
                        <div class="relative p-5">
                            <h3 class="font-bold text-white text-base leading-snug mb-1 line-clamp-2">
                                {{ $resume->job_title }}
                            </h3>

                            @if($resume->target_keywords)
                                <p class="text-xs text-purple-300/50 line-clamp-1 mb-3">
                                    {{ $resume->target_keywords }}
                                </p>
                            @endif

                            {{-- Skills chips --}}
                            @php
                                $skills = $resume->getSelectedSkills()->take(3);
                            @endphp
                            @if($skills->count())
                                <div class="flex flex-wrap gap-1.5 mb-4">
                                    @foreach($skills as $skill)
                                        <span class="text-xs bg-purple-500/20 text-purple-300 border border-purple-500/30 px-2 py-0.5 rounded-full">
                                            {{ $skill->name }}
                                        </span>
                                    @endforeach
                                    @if($resume->getSelectedSkills()->count() > 3)
                                        <span class="text-xs text-purple-400/50">
                                            +{{ $resume->getSelectedSkills()->count() - 3 }} more
                                        </span>
                                    @endif
                                </div>
                            @endif

                            {{-- Divider --}}
                            <div class="border-t border-purple-500/20 mb-4"></div>

                            {{-- Actions --}}
                            <div class="flex items-center justify-between">
                                <a href="{{ route('resume.show', $resume) }}"
                                    class="flex items-center gap-1.5 text-sm text-purple-300 hover:text-white transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Preview
                                </a>

                                <div class="flex items-center gap-3">
                                    @if($resume->pdf_path)
                                        <a href="{{ route('resume.download', $resume) }}"
                                            class="flex items-center gap-1.5 text-sm text-green-400 hover:text-green-300 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                            </svg>
                                            PDF
                                        </a>
                                    @else
                                        <span class="flex items-center gap-1.5 text-sm text-purple-500/50 cursor-default select-none">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            Generate PDF
                                        </span>
                                    @endif

                                    {{-- Delete --}}
                                    <form action="{{ route('resume.destroy', $resume) }}" method="POST"
                                        onsubmit="return confirm('Delete this resume?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-red-400/50 hover:text-red-400 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $resumes->links() }}
            </div>

        @else
            {{-- Empty State --}}
            <div class="flex flex-col items-center justify-center py-24 text-center">
                <div class="w-20 h-20 rounded-3xl bg-gradient-to-br from-purple-800/60 to-pink-900/60 flex items-center justify-center mb-6 shadow-xl shadow-purple-900/30">
                    <svg class="w-10 h-10 text-purple-300/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-white mb-2">No resumes yet</h2>
                <p class="text-purple-300/50 text-sm mb-8 max-w-xs">
                    Let AI craft a tailored resume for your next opportunity in seconds.
                </p>
                <a href="{{ route('resume.create') }}"
                    class="flex items-center gap-2 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-500 hover:to-pink-500 text-white font-semibold py-3 px-8 rounded-2xl transition-all duration-300 shadow-lg shadow-purple-900/40 hover:scale-105">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Generate My First Resume
                </a>
            </div>
        @endif

    </div>

@endsection
