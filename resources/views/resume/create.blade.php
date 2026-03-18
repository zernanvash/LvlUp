@extends('layouts.app')

@section('title', 'Generate Resume')
@section('page_title', 'Resume Builder')
@section('page_subtitle', 'Generate an AI-tailored resume')

@section('content')
<div class="max-w-3xl mx-auto">

    {{-- Errors --}}
    @if($errors->any())
        <div class="mb-6 flex items-start gap-3 bg-red-900/30 border border-red-500/30 text-red-300 px-4 py-3 rounded-2xl backdrop-blur">
            <i class="fas fa-exclamation-circle mt-0.5 flex-shrink-0"></i>
            <ul class="text-sm space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="glow-border rounded-3xl bg-gradient-to-br from-[#2d1b4e]/60 via-[#1a1d3e]/80 to-[#2d1b4e]/60 backdrop-blur-xl">
        <div class="relative p-8">
            <form action="{{ url('/resume') }}" method="POST" class="space-y-8">
                @csrf

                {{-- ── TARGET ROLE ─────────────────────────────────────────── --}}
                <section class="space-y-5">
                    <h3 class="font-display text-xs font-bold uppercase tracking-widest text-purple-400 border-b border-purple-500/20 pb-2">
                        <i class="fas fa-crosshairs mr-2"></i>Target Role
                    </h3>

                    <div>
                        <label class="block text-sm font-semibold text-purple-300 mb-2 uppercase tracking-wider">
                            Job Title <span class="text-pink-400">*</span>
                        </label>
                        <input type="text" name="job_title" value="{{ old('job_title') }}"
                            placeholder="e.g. Senior Full-Stack Developer"
                            class="w-full bg-[#0a0e27]/70 border border-purple-500/40 rounded-xl px-4 py-3 text-white placeholder-purple-400/40
                                   focus:outline-none focus:border-purple-400 focus:ring-2 focus:ring-purple-500/30
                                   hover:border-purple-400/60 transition font-medium">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-purple-300 mb-2 uppercase tracking-wider">
                            Job Description <span class="text-pink-400">*</span>
                        </label>
                        <textarea name="job_description" rows="6"
                            placeholder="Paste the full job description here. The AI will tailor your resume to it."
                            class="w-full bg-[#0a0e27]/70 border border-purple-500/40 rounded-xl px-4 py-3 text-white placeholder-purple-400/40
                                   focus:outline-none focus:border-purple-400 focus:ring-2 focus:ring-purple-500/30
                                   hover:border-purple-400/60 transition resize-none font-medium">{{ old('job_description') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-purple-300 mb-2 uppercase tracking-wider">
                            Target Keywords
                            <span class="text-purple-400/50 normal-case font-normal">(comma-separated, optional)</span>
                        </label>
                        <input type="text" name="target_keywords" value="{{ old('target_keywords') }}"
                            placeholder="e.g. Laravel, Vue.js, REST API, Agile"
                            class="w-full bg-[#0a0e27]/70 border border-purple-500/40 rounded-xl px-4 py-3 text-white placeholder-purple-400/40
                                   focus:outline-none focus:border-purple-400 focus:ring-2 focus:ring-purple-500/30
                                   hover:border-purple-400/60 transition font-medium">
                        <p class="text-xs text-purple-400/50 mt-1.5">Used to calculate your keyword match score.</p>
                    </div>

                    {{-- Writing Tone --}}
                    <div>
                        <label class="block text-sm font-semibold text-purple-300 mb-3 uppercase tracking-wider">Writing Tone</label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            @foreach(['professional' => 'fas fa-briefcase', 'creative' => 'fas fa-palette', 'executive' => 'fas fa-crown', 'concise' => 'fas fa-bolt'] as $tone => $icon)
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="tone" value="{{ $tone }}"
                                        {{ old('tone', 'professional') === $tone ? 'checked' : '' }}
                                        class="sr-only peer">
                                    <div class="flex flex-col items-center gap-1.5 px-3 py-3 rounded-xl border border-purple-500/30
                                                bg-[#0a0e27]/50 text-center text-sm font-semibold text-purple-300
                                                peer-checked:border-purple-400 peer-checked:bg-purple-500/20 peer-checked:text-white
                                                hover:border-purple-400/60 hover:bg-purple-500/10 transition capitalize cursor-pointer">
                                        <i class="{{ $icon }} text-xs"></i>
                                        {{ $tone }}
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- PDF Template --}}
                    <div>
                        <label class="block text-sm font-semibold text-purple-300 mb-3 uppercase tracking-wider">PDF Template</label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach(['modern', 'classic', 'minimal', 'creative', 'executive', 'tech'] as $tpl)
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="template" value="{{ $tpl }}"
                                        {{ old('template', 'modern') === $tpl ? 'checked' : '' }}
                                        class="sr-only peer">
                                    <div class="px-3 py-2.5 rounded-xl border border-purple-500/30
                                                bg-[#0a0e27]/50 text-center text-sm font-semibold text-purple-300
                                                peer-checked:border-purple-400 peer-checked:bg-purple-500/20 peer-checked:text-white
                                                hover:border-purple-400/60 hover:bg-purple-500/10 transition capitalize cursor-pointer">
                                        {{ $tpl }}
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </section>

                {{-- ── PERSONAL & CONTACT ───────────────────────────────────── --}}
                <section class="space-y-5">
                    <h3 class="font-display text-xs font-bold uppercase tracking-widest text-purple-400 border-b border-purple-500/20 pb-2">
                        <i class="fas fa-id-card mr-2"></i>Personal & Contact Info
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-purple-300 mb-2 uppercase tracking-wider">Phone</label>
                            <div class="relative">
                                <i class="fas fa-phone absolute left-4 top-1/2 -translate-y-1/2 text-purple-400/50 text-sm"></i>
                                <input type="text" name="phone" value="{{ old('phone') }}"
                                    placeholder="+1 555 000 0000"
                                    class="w-full bg-[#0a0e27]/70 border border-purple-500/40 rounded-xl pl-10 pr-4 py-3 text-white placeholder-purple-400/40
                                           focus:outline-none focus:border-purple-400 focus:ring-2 focus:ring-purple-500/30
                                           hover:border-purple-400/60 transition font-medium">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-purple-300 mb-2 uppercase tracking-wider">Location</label>
                            <div class="relative">
                                <i class="fas fa-map-marker-alt absolute left-4 top-1/2 -translate-y-1/2 text-purple-400/50 text-sm"></i>
                                <input type="text" name="location" value="{{ old('location') }}"
                                    placeholder="City, Country"
                                    class="w-full bg-[#0a0e27]/70 border border-purple-500/40 rounded-xl pl-10 pr-4 py-3 text-white placeholder-purple-400/40
                                           focus:outline-none focus:border-purple-400 focus:ring-2 focus:ring-purple-500/30
                                           hover:border-purple-400/60 transition font-medium">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-purple-300 mb-2 uppercase tracking-wider">LinkedIn URL</label>
                            <div class="relative">
                                <i class="fab fa-linkedin absolute left-4 top-1/2 -translate-y-1/2 text-purple-400/50 text-sm"></i>
                                <input type="url" name="linked_in" value="{{ old('linked_in') }}"
                                    placeholder="https://linkedin.com/in/yourname"
                                    class="w-full bg-[#0a0e27]/70 border border-purple-500/40 rounded-xl pl-10 pr-4 py-3 text-white placeholder-purple-400/40
                                           focus:outline-none focus:border-purple-400 focus:ring-2 focus:ring-purple-500/30
                                           hover:border-purple-400/60 transition font-medium">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-purple-300 mb-2 uppercase tracking-wider">GitHub / Portfolio</label>
                            <div class="relative">
                                <i class="fab fa-github absolute left-4 top-1/2 -translate-y-1/2 text-purple-400/50 text-sm"></i>
                                <input type="url" name="github_url" value="{{ old('github_url') }}"
                                    placeholder="https://github.com/yourname"
                                    class="w-full bg-[#0a0e27]/70 border border-purple-500/40 rounded-xl pl-10 pr-4 py-3 text-white placeholder-purple-400/40
                                           focus:outline-none focus:border-purple-400 focus:ring-2 focus:ring-purple-500/30
                                           hover:border-purple-400/60 transition font-medium">
                            </div>
                        </div>
                    </div>
                </section>

                {{-- ── WORK EXPERIENCE ──────────────────────────────────────── --}}
                <section class="space-y-5">
                    <h3 class="font-display text-xs font-bold uppercase tracking-widest text-purple-400 border-b border-purple-500/20 pb-2">
                        <i class="fas fa-briefcase mr-2"></i>Work Experience
                    </h3>
                    <div>
                        <label class="block text-sm font-semibold text-purple-300 mb-2 uppercase tracking-wider">
                            Experience Details
                            <span class="text-purple-400/50 normal-case font-normal">(AI will format & enhance)</span>
                        </label>
                        <textarea name="work_experience" rows="8"
                            placeholder="List your roles, companies, dates and key achievements. E.g.:&#10;&#10;Senior Developer @ Acme Corp (2021–Present)&#10;• Led microservices migration, cutting deploy time by 60%&#10;• Mentored 4 junior devs&#10;&#10;Developer @ Startup Ltd (2018–2021)&#10;• Built React dashboard used by 20k users"
                            class="w-full bg-[#0a0e27]/70 border border-purple-500/40 rounded-xl px-4 py-3 text-white placeholder-purple-400/40
                                   focus:outline-none focus:border-purple-400 focus:ring-2 focus:ring-purple-500/30
                                   hover:border-purple-400/60 transition resize-none font-mono text-sm leading-relaxed">{{ old('work_experience') }}</textarea>
                    </div>
                </section>

                {{-- ── EDUCATION & CREDENTIALS ──────────────────────────────── --}}
                <section class="space-y-5">
                    <h3 class="font-display text-xs font-bold uppercase tracking-widest text-purple-400 border-b border-purple-500/20 pb-2">
                        <i class="fas fa-graduation-cap mr-2"></i>Education & Credentials
                    </h3>

                    <div>
                        <label class="block text-sm font-semibold text-purple-300 mb-2 uppercase tracking-wider">Education</label>
                        <textarea name="education_details" rows="3"
                            placeholder="e.g. B.Sc. Computer Science, University of Manila, 2018"
                            class="w-full bg-[#0a0e27]/70 border border-purple-500/40 rounded-xl px-4 py-3 text-white placeholder-purple-400/40
                                   focus:outline-none focus:border-purple-400 focus:ring-2 focus:ring-purple-500/30
                                   hover:border-purple-400/60 transition resize-none font-medium">{{ old('education_details') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-purple-300 mb-2 uppercase tracking-wider">Certifications</label>
                        <textarea name="certifications" rows="3"
                            placeholder="e.g. AWS Solutions Architect (2023), Google Professional Cloud Developer (2022)"
                            class="w-full bg-[#0a0e27]/70 border border-purple-500/40 rounded-xl px-4 py-3 text-white placeholder-purple-400/40
                                   focus:outline-none focus:border-purple-400 focus:ring-2 focus:ring-purple-500/30
                                   hover:border-purple-400/60 transition resize-none font-medium">{{ old('certifications') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-purple-300 mb-2 uppercase tracking-wider">Spoken Languages</label>
                        <div class="relative">
                            <i class="fas fa-language absolute left-4 top-1/2 -translate-y-1/2 text-purple-400/50 text-sm"></i>
                            <input type="text" name="spoken_languages" value="{{ old('spoken_languages') }}"
                                placeholder="e.g. English (fluent), Filipino (native), Spanish (conversational)"
                                class="w-full bg-[#0a0e27]/70 border border-purple-500/40 rounded-xl pl-10 pr-4 py-3 text-white placeholder-purple-400/40
                                       focus:outline-none focus:border-purple-400 focus:ring-2 focus:ring-purple-500/30
                                       hover:border-purple-400/60 transition font-medium">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-purple-300 mb-2 uppercase tracking-wider">
                            Optional Bio / Extra Context
                            <span class="text-purple-400/50 normal-case font-normal">(additional context for the AI)</span>
                        </label>
                        <textarea name="bio_seed" rows="3"
                            placeholder="Any extra context you want the AI to consider when writing your resume…"
                            class="w-full bg-[#0a0e27]/70 border border-purple-500/40 rounded-xl px-4 py-3 text-white placeholder-purple-400/40
                                   focus:outline-none focus:border-purple-400 focus:ring-2 focus:ring-purple-500/30
                                   hover:border-purple-400/60 transition resize-none font-medium">{{ old('bio_seed') }}</textarea>
                    </div>
                </section>

                {{-- ── PROJECTS ─────────────────────────────────────────────── --}}
                @if(isset($projects) && $projects->count())
                <section>
                    <h3 class="font-display text-xs font-bold uppercase tracking-widest text-purple-400 border-b border-purple-500/20 pb-2 mb-4">
                        <i class="fas fa-folder-open mr-2"></i>Include Projects
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($projects as $project)
                            <label class="relative flex items-start gap-3 p-4 rounded-2xl border border-purple-500/20
                                          bg-[#0a0e27]/40 cursor-pointer
                                          hover:border-purple-400/50 hover:bg-purple-500/10 transition
                                          has-[:checked]:border-purple-400 has-[:checked]:bg-purple-500/20">
                                <input type="checkbox"
                                    name="selected_project_ids[]"
                                    value="{{ $project->id }}"
                                    {{ in_array($project->id, old('selected_project_ids', [])) ? 'checked' : '' }}
                                    class="mt-1 rounded border-purple-500/50 bg-purple-950/50 text-purple-500 focus:ring-purple-500">
                                <div class="min-w-0">
                                    <p class="font-bold text-sm text-white truncate">{{ $project->name }}</p>
                                    <p class="text-xs text-purple-300/60 line-clamp-2 mt-0.5">{{ $project->description }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </section>
                @endif

                {{-- ── SKILLS ───────────────────────────────────────────────── --}}
                @if(isset($skills) && $skills->count())
                <section>
                    <h3 class="font-display text-xs font-bold uppercase tracking-widest text-purple-400 border-b border-purple-500/20 pb-2 mb-4">
                        <i class="fas fa-bolt mr-2"></i>Include Skills
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($skills as $skill)
                            <label class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border border-purple-500/30
                                          bg-[#0a0e27]/40 cursor-pointer text-sm text-purple-300
                                          hover:border-purple-400/60 hover:bg-purple-500/10 transition
                                          has-[:checked]:border-purple-400 has-[:checked]:bg-purple-500/20 has-[:checked]:text-white">
                                <input type="checkbox"
                                    name="selected_skill_ids[]"
                                    value="{{ $skill->id }}"
                                    {{ in_array($skill->id, old('selected_skill_ids', [])) ? 'checked' : '' }}
                                    class="sr-only">
                                @if($skill->icon)<i class="{{ $skill->icon }} text-xs"></i>@endif
                                {{ $skill->name }}
                            </label>
                        @endforeach
                    </div>
                </section>
                @endif

                {{-- ── FOOTER ───────────────────────────────────────────────── --}}
                <div class="flex items-center justify-between pt-4 border-t border-purple-500/20">
                    <a href="{{ route('resume.index') }}"
                        class="flex items-center gap-2 text-sm text-purple-400 hover:text-white transition font-medium">
                        <i class="fas fa-arrow-left text-xs"></i>
                        Back
                    </a>
                    <button type="submit"
                        class="btn-glow flex items-center gap-2 bg-gradient-to-r from-purple-600 to-pink-600
                               hover:from-purple-500 hover:to-pink-500 text-white font-bold py-3 px-7
                               rounded-xl transition-all duration-300 shadow-lg shadow-purple-900/50
                               hover:scale-105 hover:shadow-purple-700/50">
                        <i class="fas fa-bolt"></i>
                        Generate Resume with AI
                    </button>
                </div>

            </form>
        </div>
    </div>

</div>
@endsection
