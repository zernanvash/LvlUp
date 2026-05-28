<div class="space-y-6">

    {{-- Header notice --}}
    <div class="flex items-start gap-3 p-4 rounded-xl bg-amber-500/10 border border-amber-500/30">
        <i class="fas fa-lock text-amber-400 mt-0.5"></i>
        <div>
            <p class="text-sm font-semibold text-amber-300">Private — Resume Use Only</p>
            <p class="text-xs text-amber-400/70 mt-0.5">This information is never shown on your public profile. It's used by the AI Resume Builder to generate tailored resumes.</p>
        </div>
    </div>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
        @csrf
        @method('patch')

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

        <div class="pt-2">
            <button type="submit" class="btn-glow px-8 py-2.5 rounded-lg font-bold">
                <i class="fas fa-save mr-2"></i> Save Resume Details
            </button>
        </div>
    </form>
</div>
