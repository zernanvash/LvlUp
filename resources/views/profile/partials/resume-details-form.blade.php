<div class="space-y-6" x-data="{ editing: {{ $errors->any() ? 'true' : 'false' }} }">

    {{-- Header notice --}}
    <div class="flex items-start gap-3 p-4 rounded-xl bg-amber-500/10 border border-amber-500/30">
        <i class="fas fa-lock text-amber-400 mt-0.5"></i>
        <div>
            <p class="text-sm font-semibold text-amber-300">Private — Resume Use Only</p>
            <p class="text-xs text-amber-400/70 mt-0.5">This information is never shown on your public profile. It's used by the AI Resume Builder to generate tailored resumes.</p>
        </div>
    </div>

    {{-- Read-Only Display Mode --}}
    <div x-show="!editing" class="space-y-6" x-cloak x-transition>
        {{-- Contact Info --}}
        <div class="lvl-panel rounded-2xl p-6">
            <h3 class="font-display text-base font-bold text-white mb-4 flex items-center gap-2">
                <i class="fas fa-address-card text-purple-400"></i> Contact Information
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-white/5 rounded-xl p-3 border border-white/5">
                    <p class="text-xs text-[var(--lvl-muted)] mb-1">Phone Number</p>
                    <p class="text-white font-semibold text-sm">{{ $user->phone_number ?: '—' }}</p>
                </div>
                <div class="bg-white/5 rounded-xl p-3 border border-white/5">
                    <p class="text-xs text-[var(--lvl-muted)] mb-1">Home Address</p>
                    <p class="text-white font-semibold text-sm">{{ $user->home_address ?: '—' }}</p>
                </div>
                <div class="bg-white/5 rounded-xl p-3 border border-white/5">
                    <p class="text-xs text-[var(--lvl-muted)] mb-1">City</p>
                    <p class="text-white font-semibold text-sm">{{ $user->city ?: '—' }}</p>
                </div>
                <div class="bg-white/5 rounded-xl p-3 border border-white/5">
                    <p class="text-xs text-[var(--lvl-muted)] mb-1">Country</p>
                    <p class="text-white font-semibold text-sm">{{ $user->country ?: '—' }}</p>
                </div>
            </div>
        </div>

        {{-- Resume Summary --}}
        <div class="lvl-panel rounded-2xl p-6">
            <h3 class="font-display text-base font-bold text-white mb-4 flex items-center gap-2">
                <i class="fas fa-file-alt text-purple-400"></i> Resume Summary
            </h3>
            <div class="space-y-3">
                <div class="bg-white/5 rounded-xl p-3 border border-white/5">
                    <p class="text-xs text-[var(--lvl-muted)] mb-1">Target Job Title</p>
                    <p class="text-white font-semibold text-sm">{{ $user->resume_job_title ?: '—' }}</p>
                </div>
                <div class="bg-white/5 rounded-xl p-3 border border-white/5">
                    <p class="text-xs text-[var(--lvl-muted)] mb-1">Professional Summary</p>
                    <p class="text-gray-300 text-sm leading-relaxed whitespace-pre-line">{{ $user->resume_summary ?: '—' }}</p>
                </div>
            </div>
        </div>

        {{-- Work Experience --}}
        <div class="lvl-panel rounded-2xl p-6">
            <h3 class="font-display text-base font-bold text-white mb-3 flex items-center gap-2">
                <i class="fas fa-briefcase text-purple-400"></i> Work Experience
            </h3>
            <div class="bg-white/5 rounded-xl p-4 border border-white/5">
                @if($user->work_experience)
                    <div class="space-y-3">
                        @foreach(explode("\n", $user->work_experience) as $line)
                            @if(trim($line))
                                <div class="border-l-2 border-purple-500/40 pl-3 py-0.5">
                                    <p class="text-sm text-gray-300 font-mono leading-relaxed">{{ $line }}</p>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-sm italic">No work experience listed yet.</p>
                @endif
            </div>
        </div>

        {{-- Education --}}
        <div class="lvl-panel rounded-2xl p-6">
            <h3 class="font-display text-base font-bold text-white mb-3 flex items-center gap-2">
                <i class="fas fa-graduation-cap text-purple-400"></i> Education
            </h3>
            <div class="bg-white/5 rounded-xl p-4 border border-white/5">
                @if($user->education)
                    <div class="space-y-3">
                        @foreach(explode("\n", $user->education) as $line)
                            @if(trim($line))
                                <div class="border-l-2 border-purple-500/40 pl-3 py-0.5">
                                    <p class="text-sm text-gray-300 font-mono leading-relaxed">{{ $line }}</p>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-sm italic">No education listed yet.</p>
                @endif
            </div>
        </div>

        {{-- Certifications & Languages --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div class="lvl-panel rounded-2xl p-6">
                <h3 class="font-display text-base font-bold text-white mb-3 flex items-center gap-2">
                    <i class="fas fa-certificate text-purple-400"></i> Certifications
                </h3>
                <div class="bg-white/5 rounded-xl p-4 border border-white/5 min-h-[80px]">
                    @if($user->certifications)
                        <ul class="list-disc list-inside space-y-1 text-sm text-gray-300">
                            @foreach(explode("\n", $user->certifications) as $line)
                                @if(trim($line))
                                    <li>{{ $line }}</li>
                                @endif
                            @endforeach
                        </ul>
                    @else
                        <p class="text-gray-500 text-sm italic">No certifications listed yet.</p>
                    @endif
                </div>
            </div>
            <div class="lvl-panel rounded-2xl p-6">
                <h3 class="font-display text-base font-bold text-white mb-3 flex items-center gap-2">
                    <i class="fas fa-language text-purple-400"></i> Languages
                </h3>
                <div class="bg-white/5 rounded-xl p-4 border border-white/5 min-h-[80px]">
                    @if($user->languages)
                        <ul class="list-disc list-inside space-y-1 text-sm text-gray-300">
                            @foreach(explode("\n", $user->languages) as $line)
                                @if(trim($line))
                                    <li>{{ $line }}</li>
                                @endif
                            @endforeach
                        </ul>
                    @else
                        <p class="text-gray-500 text-sm italic">No languages listed yet.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="pt-2">
            <button @click="editing = true" class="btn-glow px-8 py-2.5 rounded-lg font-bold">
                <i class="fas fa-edit mr-2"></i> Edit Resume Details
            </button>
        </div>
    </div>

    {{-- Editable Form Mode --}}
    <form x-show="editing" method="post" action="{{ route('profile.update') }}" class="space-y-6" x-cloak x-transition>
        @csrf
        @method('patch')
        <input type="hidden" name="tab" value="resume">

        {{-- Contact Info --}}
        <div class="lvl-panel rounded-2xl p-6">
            <h3 class="font-display text-base font-bold text-white mb-4 flex items-center gap-2">
                <i class="fas fa-address-card text-purple-400"></i> Contact Information
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-[var(--lvl-muted)] mb-1.5">
                        <i class="fas fa-phone mr-1"></i> Phone Number
                    </label>
                    <input type="text" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}"
                           placeholder="+1 (555) 000-0000" class="w-full px-4 py-2.5">
                    @error('phone_number') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[var(--lvl-muted)] mb-1.5">
                        <i class="fas fa-map-marker-alt mr-1"></i> Home Address
                    </label>
                    <input type="text" name="home_address" value="{{ old('home_address', $user->home_address) }}"
                           placeholder="123 Main St" class="w-full px-4 py-2.5">
                    @error('home_address') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[var(--lvl-muted)] mb-1.5">City</label>
                    <input type="text" name="city" value="{{ old('city', $user->city) }}"
                           placeholder="San Francisco" class="w-full px-4 py-2.5">
                    @error('city') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[var(--lvl-muted)] mb-1.5">Country</label>
                    <input type="text" name="country" value="{{ old('country', $user->country) }}"
                           placeholder="United States" class="w-full px-4 py-2.5">
                    @error('country') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Resume Summary --}}
        <div class="lvl-panel rounded-2xl p-6">
            <h3 class="font-display text-base font-bold text-white mb-4 flex items-center gap-2">
                <i class="fas fa-file-alt text-purple-400"></i> Resume Summary
            </h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-[var(--lvl-muted)] mb-1.5">Target Job Title</label>
                    <input type="text" name="resume_job_title" value="{{ old('resume_job_title', $user->resume_job_title) }}"
                           placeholder="e.g. Senior Full Stack Developer" class="w-full px-4 py-2.5">
                    @error('resume_job_title') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[var(--lvl-muted)] mb-1.5">Professional Summary</label>
                    <textarea name="resume_summary" rows="4"
                              placeholder="A brief 2-3 sentence summary of your professional background and goals..."
                              class="w-full px-4 py-2.5 resize-none">{{ old('resume_summary', $user->resume_summary) }}</textarea>
                    @error('resume_summary') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Work Experience --}}
        <div class="lvl-panel rounded-2xl p-6">
            <h3 class="font-display text-base font-bold text-white mb-2 flex items-center gap-2">
                <i class="fas fa-briefcase text-purple-400"></i> Work Experience
            </h3>
            <p class="text-xs text-purple-300/60 mb-3">List your work history. Each entry on a new line. Format: Company | Role | Duration | Description</p>
            <textarea name="work_experience" rows="6"
                      placeholder="Acme Corp | Software Engineer | Jan 2022 - Present | Built scalable APIs using Laravel and Vue.js&#10;Startup Inc | Junior Dev | Jun 2020 - Dec 2021 | Developed React frontend features"
                      class="w-full px-4 py-2.5 resize-none font-mono text-sm">{{ old('work_experience', $user->work_experience) }}</textarea>
            @error('work_experience') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
        </div>

        {{-- Education --}}
        <div class="lvl-panel rounded-2xl p-6">
            <h3 class="font-display text-base font-bold text-white mb-2 flex items-center gap-2">
                <i class="fas fa-graduation-cap text-purple-400"></i> Education
            </h3>
            <p class="text-xs text-purple-300/60 mb-3">Format: Institution | Degree | Year | Notes (one per line)</p>
            <textarea name="education" rows="4"
                      placeholder="State University | B.S. Computer Science | 2020 | Dean's List&#10;Online Bootcamp | Full Stack Web Dev | 2019"
                      class="w-full px-4 py-2.5 resize-none font-mono text-sm">{{ old('education', $user->education) }}</textarea>
            @error('education') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
        </div>

        {{-- Certifications & Languages --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div class="lvl-panel rounded-2xl p-6">
                <h3 class="font-display text-base font-bold text-white mb-2 flex items-center gap-2">
                    <i class="fas fa-certificate text-purple-400"></i> Certifications
                </h3>
                <p class="text-xs text-purple-300/60 mb-3">One per line</p>
                <textarea name="certifications" rows="5"
                          placeholder="AWS Certified Developer - 2023&#10;Google Cloud Professional - 2022&#10;Meta Frontend Developer - 2021"
                          class="w-full px-4 py-2.5 resize-none text-sm">{{ old('certifications', $user->certifications) }}</textarea>
                @error('certifications') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>
            <div class="lvl-panel rounded-2xl p-6">
                <h3 class="font-display text-base font-bold text-white mb-2 flex items-center gap-2">
                    <i class="fas fa-language text-purple-400"></i> Languages
                </h3>
                <p class="text-xs text-purple-300/60 mb-3">Format: Language - Proficiency (one per line)</p>
                <textarea name="languages" rows="5"
                          placeholder="English - Native&#10;Spanish - Conversational&#10;Japanese - Basic"
                          class="w-full px-4 py-2.5 resize-none text-sm">{{ old('languages', $user->languages) }}</textarea>
                @error('languages') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="pt-2 flex gap-4">
            <button type="submit" class="btn-glow px-8 py-2.5 rounded-lg font-bold">
                <i class="fas fa-save mr-2"></i> Save Resume Details
            </button>
            <button type="button" @click="editing = false" class="btn-secondary px-8 py-2.5 rounded-lg font-bold">
                Cancel
            </button>
        </div>
    </form>
</div>
