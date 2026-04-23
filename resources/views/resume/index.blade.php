@extends('layouts.app')

@section('title', 'Resume Builder')
@section('page_title', 'Resume Builder')
@section('page_subtitle', 'AI-powered • Auto-fills from your profile')

@section('content')
@php
    $hasBio      = !empty($user->bio);
    $hasWork     = !empty($user->work_experience);
    $hasEducation= !empty($user->education);
    $hasSkills   = !empty($user->technical_skills);
    $profileScore= ($hasBio + $hasWork + $hasEducation + $hasSkills) * 25;
@endphp

{{-- Flash messages --}}
@foreach(['cert_success','cert_error','error'] as $key)
    @if(session($key))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(()=>show=false,4000)"
         class="mb-4 px-4 py-3 rounded-xl text-sm flex items-center gap-2 {{ $key==='cert_success' ? 'bg-emerald-500/20 border border-emerald-500/40 text-emerald-300' : 'bg-red-500/20 border border-red-500/40 text-red-300' }}">
        <i class="fas {{ $key==='cert_success' ? 'fa-check-circle' : 'fa-exclamation-circle' }}"></i>
        {{ session($key) }}
    </div>
    @endif
@endforeach

<div class="max-w-7xl mx-auto" x-data="resumeBuilder()" x-cloak>

    {{-- Profile completeness banner --}}
    @if($profileScore < 100)
    <div class="mb-6 glow-border rounded-2xl p-4 bg-gradient-to-r from-amber-900/30 to-amber-800/20 backdrop-blur flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-amber-500/20 border border-amber-400/40 flex items-center justify-center flex-shrink-0">
            <i class="fas fa-exclamation-triangle text-amber-400 text-xl"></i>
        </div>
        <div class="flex-1">
            <p class="font-bold text-amber-300 text-sm">Profile {{ $profileScore }}% complete — richer profile = better AI resume</p>
            <p class="text-xs text-amber-400/70 mt-1">
                Missing:
                @if(!$hasBio)<span class="mr-2">Bio</span>@endif
                @if(!$hasWork)<span class="mr-2">Work Experience</span>@endif
                @if(!$hasEducation)<span class="mr-2">Education</span>@endif
                @if(!$hasSkills)<span class="mr-2">Technical Skills</span>@endif
            </p>
        </div>
        <a href="{{ route('profile.edit') }}" class="flex-shrink-0 px-4 py-2 rounded-xl bg-amber-500/20 border border-amber-400/40 text-amber-300 text-sm hover:bg-amber-500/30 transition font-semibold">
            Complete Profile →
        </a>
    </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

        {{-- ============================================================
             LEFT PANEL — USER DATA (auto-populated)
        ============================================================ --}}
        <div class="space-y-5">

            {{-- Profile Info --}}
            <div class="glow-border rounded-2xl p-6 bg-gradient-to-br from-purple-900/40 to-indigo-950/40 backdrop-blur">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-purple-500/30 flex items-center justify-center">
                        <i class="fas fa-user text-purple-400 text-sm"></i>
                    </div>
                    <h3 class="font-bold text-white">Profile Information</h3>
                    <span class="ml-auto text-xs px-2 py-1 rounded-full bg-green-500/20 text-green-300 border border-green-500/30">
                        <i class="fas fa-check-circle mr-1"></i>Auto-filled
                    </span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="bg-white/5 rounded-xl p-3">
                        <p class="text-xs text-purple-400 mb-1">Full Name</p>
                        <p class="text-white font-semibold">{{ $user->name }}</p>
                    </div>
                    <div class="bg-white/5 rounded-xl p-3">
                        <p class="text-xs text-purple-400 mb-1">Title</p>
                        <p class="text-white font-semibold">{{ $user->title ?? $user->resume_job_title ?? '—' }}</p>
                    </div>
                    <div class="bg-white/5 rounded-xl p-3">
                        <p class="text-xs text-purple-400 mb-1">Email</p>
                        <p class="text-white text-sm">{{ $user->email }}</p>
                    </div>
                    <div class="bg-white/5 rounded-xl p-3">
                        <p class="text-xs text-purple-400 mb-1">Location</p>
                        <p class="text-white text-sm">{{ collect([$user->city, $user->country])->filter()->join(', ') ?: '—' }}</p>
                    </div>
                    @if($user->phone_number)
                    <div class="bg-white/5 rounded-xl p-3">
                        <p class="text-xs text-purple-400 mb-1">Phone</p>
                        <p class="text-white text-sm">{{ $user->phone_number }}</p>
                    </div>
                    @endif
                    @if($user->linkedin_url || $user->github_url)
                    <div class="bg-white/5 rounded-xl p-3">
                        <p class="text-xs text-purple-400 mb-1">Links</p>
                        <div class="flex gap-3">
                            @if($user->linkedin_url)
                            <a href="{{ $user->linkedin_url }}" class="text-blue-400 text-sm hover:text-blue-300" target="_blank"><i class="fab fa-linkedin mr-1"></i>LinkedIn</a>
                            @endif
                            @if($user->github_url)
                            <a href="{{ $user->github_url }}" class="text-purple-400 text-sm hover:text-purple-300" target="_blank"><i class="fab fa-github mr-1"></i>GitHub</a>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
                @if($user->bio)
                <div class="mt-3 bg-white/5 rounded-xl p-3">
                    <p class="text-xs text-purple-400 mb-1">Bio</p>
                    <p class="text-gray-300 text-sm leading-relaxed">{{ $user->bio }}</p>
                </div>
                @endif
            </div>

            {{-- Skills --}}
            <div class="glow-border rounded-2xl p-5 bg-gradient-to-br from-blue-900/30 to-indigo-950/40 backdrop-blur">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-blue-500/30 flex items-center justify-center">
                        <i class="fas fa-code text-blue-400 text-sm"></i>
                    </div>
                    <h3 class="font-bold text-white">Skills</h3>
                </div>
                @php
                    $techSkillTags = array_filter(array_map('trim', explode(',', $user->technical_skills ?? '')));
                    $unlockedSkills = $user->unlockedNodes->map(fn($n) => $n->skill?->name)->filter()->unique()->values();
                @endphp
                @if(count($techSkillTags) > 0)
                <div class="mb-3">
                    <p class="text-xs text-blue-300 mb-2 font-semibold uppercase tracking-wider">Technical Skills</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($techSkillTags as $skill)
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-500/20 border border-blue-500/40 text-blue-300">{{ $skill }}</span>
                        @endforeach
                    </div>
                </div>
                @endif
                @if($unlockedSkills->count() > 0)
                <div>
                    <p class="text-xs text-purple-300 mb-2 font-semibold uppercase tracking-wider">Skill Tree Unlocked</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($unlockedSkills as $skill)
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-purple-500/20 border border-purple-500/40 text-purple-300">{{ $skill }}</span>
                        @endforeach
                    </div>
                </div>
                @endif
                @if(count($techSkillTags) === 0 && $unlockedSkills->count() === 0)
                <a href="{{ route('profile.edit') }}" class="text-sm text-purple-400 hover:text-purple-300 flex items-center gap-2">
                    <i class="fas fa-plus-circle"></i> Add skills to your profile
                </a>
                @endif
            </div>

            {{-- Projects --}}
            <div class="glow-border rounded-2xl p-5 bg-gradient-to-br from-indigo-900/30 to-purple-950/40 backdrop-blur">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-indigo-500/30 flex items-center justify-center">
                        <i class="fas fa-folder-open text-indigo-400 text-sm"></i>
                    </div>
                    <h3 class="font-bold text-white">Projects <span class="text-purple-400 text-sm">({{ $user->projects->count() }})</span></h3>
                    <span class="ml-auto text-xs text-purple-400">Select for resume</span>
                </div>
                @if($user->projects->count() > 0)
                <div class="space-y-2 max-h-64 overflow-y-auto pr-1">
                    @foreach($user->projects as $project)
                    <label class="flex items-start gap-3 p-3 rounded-xl cursor-pointer transition-all border"
                           :class="selectedProjects.includes({{ $project->id }}) ? 'bg-purple-500/20 border-purple-500/40' : 'bg-white/5 border-white/10 hover:border-purple-500/30'">
                        <input type="checkbox" :value="{{ $project->id }}" x-model="selectedProjects"
                               class="mt-0.5 rounded text-purple-500 bg-white/10 border-white/20">
                        <div class="flex-1 min-w-0">
                            <p class="text-white text-sm font-semibold truncate">{{ $project->name }}</p>
                            <p class="text-gray-400 text-xs mt-0.5 line-clamp-2">{{ $project->description }}</p>
                            @if($project->skills->count())
                            <div class="flex flex-wrap gap-1 mt-1">
                                @foreach($project->skills->take(4) as $skill)
                                <span class="text-xs px-1.5 py-0.5 rounded bg-purple-500/20 text-purple-300">{{ $skill->name }}</span>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </label>
                    @endforeach
                </div>
                <div class="flex gap-2 mt-3">
                    <button @click="selectedProjects = {{ json_encode($user->projects->pluck('id')->values()) }}"
                            class="text-xs px-3 py-1.5 rounded-lg bg-purple-500/20 text-purple-300 hover:bg-purple-500/30 transition border border-purple-500/30">
                        Select All
                    </button>
                    <button @click="selectedProjects = []"
                            class="text-xs px-3 py-1.5 rounded-lg bg-white/5 text-gray-400 hover:bg-white/10 transition border border-white/10">
                        Clear
                    </button>
                </div>
                @else
                <a href="{{ route('projects.create') }}" class="text-sm text-purple-400 hover:text-purple-300 flex items-center gap-2">
                    <i class="fas fa-plus-circle"></i> Add your first project
                </a>
                @endif
            </div>

            {{-- Certificates --}}
            <div class="glow-border rounded-2xl p-5 bg-gradient-to-br from-pink-900/30 to-rose-950/40 backdrop-blur">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-pink-500/30 flex items-center justify-center">
                        <i class="fas fa-certificate text-pink-400 text-sm"></i>
                    </div>
                    <h3 class="font-bold text-white">Certificates <span class="text-pink-400 text-sm">({{ $user->certificates->count() }})</span></h3>
                </div>

                {{-- Upload form --}}
                <div x-data="{ open: false }" class="mb-4">
                    <button @click="open = !open"
                            class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl border-2 border-dashed border-pink-500/40 text-pink-400 text-sm hover:border-pink-400/60 hover:bg-pink-500/10 transition font-semibold">
                        <i class="fas fa-plus-circle"></i>
                        <span x-text="open ? 'Cancel' : 'Upload Certificate'"></span>
                    </button>
                    <div x-show="open" x-transition class="mt-3">
                        <form action="{{ route('certificates.store') }}" method="POST" enctype="multipart/form-data"
                              class="space-y-3 bg-white/5 rounded-xl p-4 border border-pink-500/20">
                            @csrf
                            <div>
                                <label class="text-xs text-pink-300 font-semibold uppercase tracking-wider">Certificate Title *</label>
                                <input type="text" name="title" required placeholder="e.g., AWS Certified Solutions Architect"
                                       class="w-full mt-1 bg-white/5 border border-white/10 rounded-xl px-3 py-2 text-white text-sm placeholder-gray-500 focus:outline-none focus:border-pink-400/60">
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="text-xs text-pink-300 font-semibold uppercase tracking-wider">Issuer</label>
                                    <input type="text" name="issuer" placeholder="e.g., Amazon"
                                           class="w-full mt-1 bg-white/5 border border-white/10 rounded-xl px-3 py-2 text-white text-sm placeholder-gray-500 focus:outline-none focus:border-pink-400/60">
                                </div>
                                <div>
                                    <label class="text-xs text-pink-300 font-semibold uppercase tracking-wider">Issued Date</label>
                                    <input type="date" name="issued_date"
                                           class="w-full mt-1 bg-white/5 border border-white/10 rounded-xl px-3 py-2 text-white text-sm focus:outline-none focus:border-pink-400/60">
                                </div>
                            </div>
                            <div>
                                <label class="text-xs text-pink-300 font-semibold uppercase tracking-wider">File (PDF, JPG, PNG — max 10MB) *</label>
                                <input type="file" name="file" required accept=".pdf,.jpg,.jpeg,.png,.webp"
                                       class="w-full mt-1 text-gray-300 text-sm file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-pink-500/30 file:text-pink-300 file:text-xs cursor-pointer">
                            </div>
                            <p class="text-xs text-gray-500 flex items-center gap-1.5">
                                <i class="fas fa-robot text-pink-400"></i>
                                Gemini AI will automatically summarize this certificate for your resume.
                            </p>
                            <button type="submit"
                                    class="w-full py-2.5 rounded-xl bg-gradient-to-r from-pink-600 to-rose-600 text-white font-bold text-sm hover:from-pink-500 hover:to-rose-500 transition btn-glow shadow-lg shadow-pink-500/20">
                                <i class="fas fa-upload mr-2"></i>Upload & AI Summarize
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Cert list --}}
                @if($user->certificates->count() > 0)
                <div class="space-y-2 max-h-56 overflow-y-auto pr-1">
                    @foreach($user->certificates as $cert)
                    <div class="flex items-start gap-3 p-3 rounded-xl bg-white/5 border border-white/10 group">
                        <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0 {{ $cert->isPdf() ? 'bg-red-500/20 border border-red-500/30' : 'bg-blue-500/20 border border-blue-500/30' }}">
                            <i class="fas {{ $cert->isPdf() ? 'fa-file-pdf text-red-400' : 'fa-image text-blue-400' }} text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <p class="text-white text-sm font-semibold">{{ $cert->title }}</p>
                                    @if($cert->issuer)
                                    <p class="text-gray-400 text-xs">{{ $cert->issuer }}{{ $cert->issued_date ? ' · ' . $cert->issued_date->format('M Y') : '' }}</p>
                                    @endif
                                </div>
                                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0">
                                    <a href="{{ $cert->file_path }}" target="_blank"
                                       class="w-6 h-6 rounded-lg bg-blue-500/20 border border-blue-500/30 flex items-center justify-center hover:bg-blue-500/30 transition" title="View">
                                        <i class="fas fa-external-link-alt text-blue-400 text-xs"></i>
                                    </a>
                                    <form method="POST" action="{{ route('certificates.regenerate-summary', $cert) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="w-6 h-6 rounded-lg bg-purple-500/20 border border-purple-500/30 flex items-center justify-center hover:bg-purple-500/30 transition" title="Re-summarize with AI">
                                            <i class="fas fa-sync-alt text-purple-400 text-xs"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('certificates.destroy', $cert) }}" class="inline" onsubmit="return confirm('Delete this certificate?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-6 h-6 rounded-lg bg-red-500/20 border border-red-500/30 flex items-center justify-center hover:bg-red-500/30 transition" title="Delete">
                                            <i class="fas fa-trash text-red-400 text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @if($cert->ai_summary)
                            <p class="text-xs text-purple-300 mt-1 italic leading-relaxed">{{ $cert->ai_summary }}</p>
                            @else
                            <p class="text-xs text-gray-500 mt-1 italic">AI summary pending...</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-center text-sm text-gray-500 py-4">
                    No certificates yet. Upload PDFs or images of your certifications.
                </p>
                @endif
            </div>

        </div>{{-- end left panel --}}

        {{-- ============================================================
             RIGHT PANEL — AI GENERATOR OUTPUT
        ============================================================ --}}
        <div class="space-y-5">

            {{-- Generator controls --}}
            <div class="glow-border rounded-2xl p-6 bg-gradient-to-br from-emerald-900/30 to-teal-950/40 backdrop-blur">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/30 flex items-center justify-center">
                        <i class="fas fa-robot text-emerald-400 text-sm"></i>
                    </div>
                    <h3 class="font-bold text-white">AI Resume Generator</h3>
                    <span class="ml-auto text-xs px-2 py-1 rounded-full bg-blue-500/20 text-blue-300 border border-blue-500/30">
                        <i class="fas fa-gem mr-1"></i>Gemini AI
                    </span>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="text-xs text-emerald-300 font-semibold uppercase tracking-wider">Target Job Title *</label>
                        <input type="text" x-model="jobTitle" id="job_title_input"
                               placeholder="e.g., Full Stack Laravel Developer"
                               class="w-full mt-1 bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-emerald-400/60 focus:ring-1 focus:ring-emerald-400/30">
                    </div>

                    <div>
                        <label class="text-xs text-emerald-300 font-semibold uppercase tracking-wider">Job Description <span class="text-gray-500 normal-case font-normal">(optional — improves keyword matching)</span></label>
                        <textarea x-model="jobDescription" rows="4"
                                  placeholder="Paste the job description here to improve AI tailoring and keyword analysis..."
                                  class="w-full mt-1 bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-emerald-400/60 resize-none text-sm"></textarea>
                    </div>

                    {{-- Template --}}
                    <div>
                        <label class="text-xs text-emerald-300 font-semibold uppercase tracking-wider mb-2 block">PDF Template</label>
                        <div class="grid grid-cols-4 gap-2">
                            @foreach(['modern' => 'fa-layer-group', 'classic' => 'fa-book', 'minimal' => 'fa-minus', 'creative' => 'fa-paint-brush'] as $tmpl => $icon)
                            <label class="cursor-pointer">
                                <input type="radio" value="{{ $tmpl }}" x-model="template" class="sr-only">
                                <div class="p-2 rounded-xl border-2 text-center transition-all"
                                     :class="template === '{{ $tmpl }}' ? 'border-emerald-400 bg-emerald-500/10' : 'border-white/10 hover:border-white/20'">
                                    <i class="fas {{ $icon }} text-emerald-400/70 text-sm"></i>
                                    <p class="text-xs text-white mt-1 capitalize">{{ $tmpl }}</p>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <p class="text-xs text-gray-400 flex items-center gap-1">
                        <i class="fas fa-folder-open text-purple-400"></i>
                        <span x-text="selectedProjects.length"></span> project(s) selected · all certificates, skills, and profile data included automatically
                    </p>

                    <button @click="generateResume()" id="generate_btn"
                            :disabled="generating || !jobTitle"
                            class="w-full py-3.5 rounded-xl font-bold text-white transition-all btn-glow disabled:opacity-50 disabled:cursor-not-allowed shadow-lg shadow-emerald-500/20"
                            style="background: linear-gradient(135deg, #059669, #0d9488);">
                        <span x-show="!generating" class="flex items-center justify-center gap-2">
                            <i class="fas fa-magic"></i> Generate AI Resume
                        </span>
                        <span x-show="generating" class="flex items-center justify-center gap-2">
                            <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Generating with Gemini AI...
                        </span>
                    </button>

                    <div x-show="matchScore > 0" x-transition class="flex items-center gap-3 bg-white/5 rounded-xl p-3 border border-white/10">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center font-bold text-sm flex-shrink-0"
                             :class="matchScore >= 70 ? 'bg-green-500/20 text-green-400 border border-green-500/30' : matchScore >= 40 ? 'bg-yellow-500/20 text-yellow-400 border border-yellow-500/30' : 'bg-red-500/20 text-red-400 border border-red-500/30'"
                             x-text="matchScore + '%'">
                        </div>
                        <div>
                            <p class="text-white text-sm font-semibold">Profile Match Score</p>
                            <p class="text-gray-400 text-xs">Keyword alignment with job description</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- AI Output sections --}}
            <div x-show="aiContent" x-transition class="space-y-4">

                {{-- Download bar --}}
                <div class="glow-border rounded-2xl p-4 bg-gradient-to-r from-purple-900/50 to-pink-900/30 backdrop-blur flex items-center gap-4">
                    <div>
                        <p class="text-white font-bold">✨ Resume Ready!</p>
                        <p class="text-xs text-purple-300">Download as PDF in your chosen template</p>
                    </div>
                    <div class="ml-auto flex gap-2 flex-shrink-0">
                        <form method="GET" action="{{ route('resume.download') }}" class="inline">
                            <input type="hidden" name="template" :value="template">
                            <button type="submit" id="download_pdf_btn"
                                    class="flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-purple-600 to-pink-600 text-white text-sm font-bold hover:from-purple-500 hover:to-pink-500 transition shadow-lg shadow-purple-500/20">
                                <i class="fas fa-download"></i> Download PDF
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Tab Navigation --}}
                <div class="flex gap-6 mb-4 border-b border-white/10 px-2 mt-6">
                    <button @click="viewMode = 'preview'" 
                            :class="viewMode === 'preview' ? 'text-emerald-400 border-emerald-400' : 'text-gray-400 border-transparent hover:text-gray-300'" 
                            class="pb-3 text-sm font-bold border-b-2 transition-all">
                        <i class="fas fa-file-pdf mr-1.5"></i> Live PDF Preview
                    </button>
                    <button @click="viewMode = 'data'" 
                            :class="viewMode === 'data' ? 'text-purple-400 border-purple-400' : 'text-gray-400 border-transparent hover:text-gray-300'" 
                            class="pb-3 text-sm font-bold border-b-2 transition-all">
                        <i class="fas fa-code mr-1.5"></i> Content Sections
                    </button>
                </div>

                {{-- Live Preview Iframe --}}
                <div x-show="viewMode === 'preview'" x-transition 
                     class="w-full rounded-2xl overflow-hidden bg-gray-100 border-4 border-gray-800 shadow-2xl relative" style="height: 800px;">
                    <template x-if="pdfPreviewUrl">
                        <iframe :src="pdfPreviewUrl" class="w-full h-full border-0"></iframe>
                    </template>
                    <div x-show="!pdfPreviewUrl" class="absolute inset-0 flex items-center justify-center text-gray-500">
                        <i class="fas fa-spinner fa-spin mr-2"></i> Loading PDF Preview...
                    </div>
                </div>

                {{-- Content Sections (Data View) --}}
                <div x-show="viewMode === 'data'" x-transition class="space-y-4">
                    {{-- Summary --}}
                    <div x-show="aiContent.summary" class="glow-border rounded-2xl p-5 bg-gradient-to-br from-indigo-900/30 to-purple-950/40 backdrop-blur">
                        <div class="flex items-center gap-2 mb-3">
                            <i class="fas fa-id-badge text-indigo-400"></i>
                            <h4 class="font-bold text-white text-sm uppercase tracking-wider">Professional Summary</h4>
                            <button @click="copyText(aiContent.summary)" class="ml-auto text-xs text-gray-500 hover:text-gray-300 transition px-2 py-1 rounded hover:bg-white/5">
                                <i class="fas fa-copy mr-1"></i>Copy
                            </button>
                        </div>
                        <p class="text-gray-300 text-sm leading-relaxed" x-text="aiContent.summary"></p>
                    </div>

                    {{-- Skills --}}
                    <div x-show="aiContent.skills" class="glow-border rounded-2xl p-5 bg-gradient-to-br from-blue-900/30 to-indigo-950/40 backdrop-blur">
                        <div class="flex items-center gap-2 mb-3">
                            <i class="fas fa-code text-blue-400"></i>
                            <h4 class="font-bold text-white text-sm uppercase tracking-wider">Skills</h4>
                            <button @click="copyText(aiContent.skills)" class="ml-auto text-xs text-gray-500 hover:text-gray-300 transition px-2 py-1 rounded hover:bg-white/5">
                                <i class="fas fa-copy mr-1"></i>Copy
                            </button>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <template x-for="skill in (aiContent.skills || '').split(',').map(s => s.trim()).filter(Boolean)" :key="skill">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-500/20 border border-blue-500/40 text-blue-300" x-text="skill"></span>
                            </template>
                        </div>
                    </div>

                    {{-- Experience --}}
                    <div x-show="aiContent.experience" class="glow-border rounded-2xl p-5 bg-gradient-to-br from-emerald-900/30 to-teal-950/40 backdrop-blur">
                        <div class="flex items-center gap-2 mb-3">
                            <i class="fas fa-briefcase text-emerald-400"></i>
                            <h4 class="font-bold text-white text-sm uppercase tracking-wider">Work Experience</h4>
                            <button @click="copyText(aiContent.experience)" class="ml-auto text-xs text-gray-500 hover:text-gray-300 transition px-2 py-1 rounded hover:bg-white/5">
                                <i class="fas fa-copy mr-1"></i>Copy
                            </button>
                        </div>
                        <div class="text-gray-300 text-sm whitespace-pre-line leading-relaxed" x-text="aiContent.experience"></div>
                    </div>

                    {{-- Projects --}}
                    <div x-show="aiContent.projects" class="glow-border rounded-2xl p-5 bg-gradient-to-br from-indigo-900/30 to-purple-950/40 backdrop-blur">
                        <div class="flex items-center gap-2 mb-3">
                            <i class="fas fa-folder-open text-indigo-400"></i>
                            <h4 class="font-bold text-white text-sm uppercase tracking-wider">Projects</h4>
                            <button @click="copyText(aiContent.projects)" class="ml-auto text-xs text-gray-500 hover:text-gray-300 transition px-2 py-1 rounded hover:bg-white/5">
                                <i class="fas fa-copy mr-1"></i>Copy
                            </button>
                        </div>
                        <div class="text-gray-300 text-sm whitespace-pre-line leading-relaxed" x-text="aiContent.projects"></div>
                    </div>

                    {{-- Education --}}
                    <div x-show="aiContent.education" class="glow-border rounded-2xl p-5 bg-gradient-to-br from-amber-900/30 to-orange-950/40 backdrop-blur">
                        <div class="flex items-center gap-2 mb-3">
                            <i class="fas fa-graduation-cap text-amber-400"></i>
                            <h4 class="font-bold text-white text-sm uppercase tracking-wider">Education</h4>
                            <button @click="copyText(aiContent.education)" class="ml-auto text-xs text-gray-500 hover:text-gray-300 transition px-2 py-1 rounded hover:bg-white/5">
                                <i class="fas fa-copy mr-1"></i>Copy
                            </button>
                        </div>
                        <div class="text-gray-300 text-sm whitespace-pre-line leading-relaxed" x-text="aiContent.education"></div>
                    </div>

                    {{-- Certifications --}}
                    <div x-show="aiContent.certifications" class="glow-border rounded-2xl p-5 bg-gradient-to-br from-pink-900/30 to-rose-950/40 backdrop-blur">
                        <div class="flex items-center gap-2 mb-3">
                            <i class="fas fa-certificate text-pink-400"></i>
                            <h4 class="font-bold text-white text-sm uppercase tracking-wider">Certifications</h4>
                            <button @click="copyText(aiContent.certifications)" class="ml-auto text-xs text-gray-500 hover:text-gray-300 transition px-2 py-1 rounded hover:bg-white/5">
                                <i class="fas fa-copy mr-1"></i>Copy
                            </button>
                        </div>
                        <div class="text-gray-300 text-sm whitespace-pre-line leading-relaxed" x-text="aiContent.certifications"></div>
                    </div>
                </div>

            </div>{{-- end ai content --}}

            {{-- Empty state --}}
            <div x-show="!aiContent && !generating" class="glow-border rounded-2xl p-12 bg-gradient-to-br from-gray-900/30 to-gray-950/40 backdrop-blur text-center">
                <div class="w-20 h-20 rounded-2xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-file-alt text-4xl text-purple-400/40"></i>
                </div>
                <p class="text-gray-400 font-semibold mb-2">Your AI resume will appear here</p>
                <p class="text-gray-600 text-sm leading-relaxed">Enter a job title above and click Generate.<br>Gemini AI uses all your profile data automatically — bio, projects, skills, and certificates.</p>
                @if($resume && $resume->ai_content)
                <div class="mt-6 pt-6 border-t border-white/10">
                    <p class="text-xs text-gray-500 mb-3">Resume previously generated on {{ $resume->updated_at->diffForHumans() }}</p>
                    <button @click="loadSavedResume()"
                            class="px-5 py-2 rounded-xl bg-purple-500/20 border border-purple-500/30 text-purple-300 text-sm hover:bg-purple-500/30 transition font-semibold">
                        <i class="fas fa-history mr-2"></i>Load Previous Resume
                    </button>
                </div>
                @endif
            </div>

        </div>{{-- end right panel --}}

    </div>{{-- end grid --}}
</div>{{-- end alpine --}}

@push('scripts')
<script>
function resumeBuilder() {
    return {
        jobTitle:        '{{ addslashes($resume?->job_title ?? '') }}',
        jobDescription:  '',
        template:        '{{ $resume?->template ?? 'modern' }}',
        selectedProjects: @json($resume ? ($resume->selected_project_ids ?? []) : $user->projects->pluck('id')->take(5)->values()),
        generating:      false,
        aiContent:       null,
        matchScore:      0,
        viewMode:        'preview',
        pdfPreviewUrl:   '',

        init() {
            this.$watch('template', (value) => {
                if (this.aiContent) {
                    this.updatePreviewUrl();
                }
            });
        },

        updatePreviewUrl() {
            this.pdfPreviewUrl = '{{ route('resume.preview') }}?template=' + this.template + '&t=' + Date.now();
        },

        async generateResume() {
            if (!this.jobTitle.trim()) {
                alert('Please enter a target job title.');
                return;
            }
            this.generating = true;
            try {
                const res = await fetch('{{ route('resume.generate') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        job_title:            this.jobTitle,
                        job_description:      this.jobDescription,
                        selected_project_ids: this.selectedProjects,
                        template:             this.template,
                    }),
                });
                const data = await res.json();
                if (data.success) {
                    this.aiContent  = data.ai_content;
                    this.matchScore = data.match_score ?? 0;
                    this.updatePreviewUrl();
                    this.$nextTick(() => {
                        document.getElementById('download_pdf_btn')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    });
                } else {
                    alert('Failed to generate resume. Please try again.');
                }
            } catch (e) {
                console.error(e);
                alert('An error occurred. Please check your connection and try again.');
            } finally {
                this.generating = false;
            }
        },

        loadSavedResume() {
            @if($resume && $resume->ai_content)
            this.aiContent = @json(json_decode($resume->ai_content ?? '{}', true));
            this.matchScore = {{ round($resume->match_score ?? 0) }};
            this.updatePreviewUrl();
            @endif
        },

        copyText(text) {
            if (!text) return;
            navigator.clipboard.writeText(text).catch(() => {});
        },
    };
}
</script>
@endpush
@endsection
