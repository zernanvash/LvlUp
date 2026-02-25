@extends('layouts.app')

@section('title', $resume->job_title)
@section('page_title', 'Resume Preview')
@section('page_subtitle', $resume->job_title)

@section('content')
<div class="max-w-4xl mx-auto space-y-5">

    {{-- Flash --}}
    @if(session('success'))
        <div class="flex items-center gap-3 bg-green-900/30 border border-green-500/30 text-green-300 px-4 py-3 rounded-2xl backdrop-blur">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Top bar --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('resume.index') }}"
            class="flex items-center gap-2 text-sm text-purple-400 hover:text-white transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            All Resumes
        </a>
        <div class="flex items-center gap-3">
            <a href="{{ route('resume.generate', $resume) }}"
                class="flex items-center gap-2 text-sm bg-purple-600/30 hover:bg-purple-600/50 text-purple-300 hover:text-white border border-purple-500/30 px-4 py-2 rounded-xl transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Regenerate PDF
            </a>
            @if($resume->pdf_path)
                <a href="{{ route('resume.download', $resume) }}"
                    class="flex items-center gap-2 text-sm bg-gradient-to-r from-green-700/50 to-emerald-700/50 hover:from-green-600/60 hover:to-emerald-600/60 text-green-300 border border-green-500/30 px-4 py-2 rounded-xl transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download PDF
                </a>
            @endif
        </div>
    </div>

    {{-- Match Score Banner --}}
    @if($resume->match_score)
    <div class="glow-border rounded-2xl p-4 bg-gradient-to-br from-purple-900/40 to-pink-900/40 backdrop-blur flex items-center gap-4">
        <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-lg font-bold font-display flex-shrink-0
            {{ $resume->match_score >= 75 ? 'bg-green-500/20 text-green-300 border border-green-500/30' :
               ($resume->match_score >= 50 ? 'bg-yellow-500/20 text-yellow-300 border border-yellow-500/30' :
               'bg-red-500/20 text-red-300 border border-red-500/30') }}">
            {{ $resume->match_score }}%
        </div>
        <div>
            <p class="font-semibold text-white">Keyword Match Score</p>
            <p class="text-sm text-purple-300/70">
                @if($resume->match_score >= 75) Great match! Your resume covers most target keywords.
                @elseif($resume->match_score >= 50) Decent match. Consider adding more target keywords.
                @else Low match. Try selecting more relevant projects and skills.
                @endif
            </p>
        </div>
    </div>
    @endif

    {{-- Resume Preview Card --}}
    <div class="glow-border rounded-3xl bg-gradient-to-br from-purple-900/40 via-pink-900/30 to-purple-900/40 backdrop-blur overflow-hidden">
        <div class="absolute inset-0 rounded-3xl bg-gradient-to-r from-purple-600/5 via-pink-600/5 to-purple-600/5 animate-pulse pointer-events-none"></div>

        <div class="relative p-8 max-w-2xl mx-auto font-sans">

            {{-- Resume Header --}}
            <div class="text-center border-b border-purple-500/30 pb-6 mb-6">
                <h1 class="text-2xl font-bold text-white">{{ auth()->user()->name }}</h1>
                <p class="text-purple-300/70 text-sm mt-1">{{ auth()->user()->email }}</p>
                <p class="text-pink-400 font-semibold mt-2">{{ $resume->job_title }}</p>
            </div>

            {{-- Summary --}}
            @if(!empty($resumeData['summary']))
            <div class="mb-6">
                <h2 class="text-xs font-bold uppercase tracking-widest text-purple-400 mb-2">Professional Summary</h2>
                <p class="text-gray-300 text-sm leading-relaxed">{{ $resumeData['summary'] }}</p>
            </div>
            @endif

            {{-- Skills --}}
            @if(!empty($resumeData['skills']))
            <div class="mb-6">
                <h2 class="text-xs font-bold uppercase tracking-widest text-purple-400 mb-3">Skills</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach(explode(',', $resumeData['skills']) as $skill)
                        @if(trim($skill))
                        <span class="bg-purple-500/20 text-purple-300 border border-purple-500/30 text-xs px-2.5 py-1 rounded-full">
                            {{ trim($skill) }}
                        </span>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Projects --}}
            @if(!empty($resumeData['projects']))
            <div class="mb-6">
                <h2 class="text-xs font-bold uppercase tracking-widest text-purple-400 mb-3">Projects</h2>
                <div class="space-y-3 text-sm text-gray-300">
                    {!! nl2br(e($resumeData['projects'])) !!}
                </div>
            </div>
            @endif

            {{-- Experience --}}
            @if(!empty($resumeData['experience']))
            <div class="mb-6">
                <h2 class="text-xs font-bold uppercase tracking-widest text-purple-400 mb-2">Experience</h2>
                <div class="text-sm text-gray-300 leading-relaxed">
                    {!! nl2br(e($resumeData['experience'])) !!}
                </div>
            </div>
            @endif

            {{-- Education --}}
            @if(!empty($resumeData['education']))
            <div class="mb-6">
                <h2 class="text-xs font-bold uppercase tracking-widest text-purple-400 mb-2">Education</h2>
                <div class="text-sm text-gray-300">
                    {!! nl2br(e($resumeData['education'])) !!}
                </div>
            </div>
            @endif

        </div>
    </div>

    {{-- Delete --}}
    <div class="flex justify-end">
        <form action="{{ route('resume.destroy', $resume) }}" method="POST"
            onsubmit="return confirm('Permanently delete this resume?')">
            @csrf
            @method('DELETE')
            <button type="submit"
                class="flex items-center gap-2 text-sm text-red-400/60 hover:text-red-400 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Delete Resume
            </button>
        </form>
    </div>

</div>
@endsection
