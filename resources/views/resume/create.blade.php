@extends('layouts.app')

@section('title', 'Generate Resume')
@section('page_title', 'Resume Builder')
@section('page_subtitle', 'Generate an AI-tailored resume')

@section('content')
<div class="max-w-3xl mx-auto">

    {{-- Errors --}}
    @if($errors->any())
        <div class="mb-6 flex items-start gap-3 bg-red-900/30 border border-red-500/30 text-red-300 px-4 py-3 rounded-2xl backdrop-blur">
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <ul class="text-sm space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="relative glow-border rounded-3xl bg-gradient-to-br from-purple-900/40 via-pink-900/30 to-purple-900/40 backdrop-blur">
        <div class="absolute inset-0 rounded-3xl bg-gradient-to-r from-purple-600/5 via-pink-600/5 to-purple-600/5 animate-pulse pointer-events-none"></div>

        <div class="relative p-8">
            <form action="{{ url('/resume') }}" method="POST" class="space-y-7">
                @csrf

                {{-- Job Title --}}
                <div>
                    <label class="block text-sm font-semibold text-purple-300 mb-2 uppercase tracking-wider">
                        Job Title <span class="text-pink-400">*</span>
                    </label>
                    <input type="text" name="job_title" value="{{ old('job_title') }}"
                        placeholder="e.g. Senior Full-Stack Developer"
                        class="w-full bg-purple-950/40 border border-purple-500/30 rounded-xl px-4 py-3 text-white placeholder-purple-400/40 focus:outline-none focus:border-purple-400 focus:ring-1 focus:ring-purple-400 transition">
                </div>

                {{-- Job Description --}}
                <div>
                    <label class="block text-sm font-semibold text-purple-300 mb-2 uppercase tracking-wider">
                        Job Description <span class="text-pink-400">*</span>
                    </label>
                    <textarea name="job_description" rows="6"
                        placeholder="Paste the full job description here. The AI will tailor your resume to it."
                        class="w-full bg-purple-950/40 border border-purple-500/30 rounded-xl px-4 py-3 text-white placeholder-purple-400/40 focus:outline-none focus:border-purple-400 focus:ring-1 focus:ring-purple-400 transition resize-none">{{ old('job_description') }}</textarea>
                </div>

                {{-- Target Keywords --}}
                <div>
                    <label class="block text-sm font-semibold text-purple-300 mb-2 uppercase tracking-wider">
                        Target Keywords
                        <span class="text-purple-400/50 normal-case font-normal">(comma-separated, optional)</span>
                    </label>
                    <input type="text" name="target_keywords" value="{{ old('target_keywords') }}"
                        placeholder="e.g. Laravel, Vue.js, REST API, Agile"
                        class="w-full bg-purple-950/40 border border-purple-500/30 rounded-xl px-4 py-3 text-white placeholder-purple-400/40 focus:outline-none focus:border-purple-400 focus:ring-1 focus:ring-purple-400 transition">
                    <p class="text-xs text-purple-400/50 mt-1.5">Used to calculate your keyword match score.</p>
                </div>

                {{-- Template --}}
                <div>
                    <label class="block text-sm font-semibold text-purple-300 mb-2 uppercase tracking-wider">Template</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach(['modern', 'classic', 'minimal', 'creative', 'executive', 'tech'] as $tpl)
                            <label class="relative cursor-pointer">
                                <input type="radio" name="template" value="{{ $tpl }}"
                                    {{ old('template', 'modern') === $tpl ? 'checked' : '' }}
                                    class="sr-only peer">
                                <div class="px-3 py-2 rounded-xl border border-purple-500/30 text-center text-sm font-medium text-purple-300
                                    peer-checked:border-purple-400 peer-checked:bg-purple-500/20 peer-checked:text-white
                                    hover:border-purple-400/60 transition capitalize">
                                    {{ $tpl }}
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Select Projects --}}
                @if(isset($projects) && $projects->count())
                <div>
                    <label class="block text-sm font-semibold text-purple-300 mb-3 uppercase tracking-wider">Include Projects</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($projects as $project)
                            <label class="relative flex items-start gap-3 p-4 rounded-2xl border border-purple-500/20 cursor-pointer
                                hover:border-purple-400/40 hover:bg-purple-500/10 transition
                                has-[:checked]:border-purple-400 has-[:checked]:bg-purple-500/20">
                                <input type="checkbox"
                                    name="selected_project_ids[]"
                                    value="{{ $project->id }}"
                                    {{ in_array($project->id, old('selected_project_ids', [])) ? 'checked' : '' }}
                                    class="mt-1 rounded border-purple-500/50 bg-purple-950/50 text-purple-500 focus:ring-purple-500">
                                <div>
                                    <p class="font-semibold text-sm text-white">{{ $project->name }}</p>
                                    <p class="text-xs text-purple-300/60 line-clamp-2 mt-0.5">{{ $project->description }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Select Skills --}}
                @if(isset($skills) && $skills->count())
                <div>
                    <label class="block text-sm font-semibold text-purple-300 mb-3 uppercase tracking-wider">Include Skills</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach($skills as $skill)
                            <label class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border border-purple-500/30 cursor-pointer text-sm
                                text-purple-300 hover:border-purple-400/60 hover:bg-purple-500/10 transition
                                has-[:checked]:border-purple-400 has-[:checked]:bg-purple-500/20 has-[:checked]:text-white">
                                <input type="checkbox"
                                    name="selected_skill_ids[]"
                                    value="{{ $skill->id }}"
                                    {{ in_array($skill->id, old('selected_skill_ids', [])) ? 'checked' : '' }}
                                    class="sr-only">
                                @if($skill->icon)
                                    <i class="{{ $skill->icon }} text-xs"></i>
                                @endif
                                {{ $skill->name }}
                            </label>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Footer --}}
                <div class="flex items-center justify-between pt-2 border-t border-purple-500/20">
                    <a href="{{ route('resume.index') }}"
                        class="flex items-center gap-2 text-sm text-purple-400 hover:text-white transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Back
                    </a>
                    <button type="submit"
                        class="flex items-center gap-2 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-500 hover:to-pink-500 text-white font-semibold py-2.5 px-6 rounded-xl transition-all duration-300 shadow-lg shadow-purple-900/40 hover:scale-105">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Generate Resume with AI
                    </button>
                </div>

            </form>
        </div>
    </div>

</div>
@endsection
