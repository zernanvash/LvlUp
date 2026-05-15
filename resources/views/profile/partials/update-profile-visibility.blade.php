<div class="space-y-4">
    <div class="glow-border rounded-2xl p-6 bg-gradient-to-br from-blue-900/40 to-blue-950/40 backdrop-blur">
        <h3 class="mb-4 text-lg font-bold text-white">Profile Visibility</h3>

        {{-- Public/Private master toggle --}}
        <form method="post" action="{{ route('profile.toggle-visibility') }}" x-data="{ isPublic: {{ $user->is_public ? 'true' : 'false' }} }" class="mb-5">
            @csrf @method('patch')
            <div class="flex items-center justify-between p-4 rounded-xl bg-white/5 border border-blue-500/20">
                <div>
                    <p class="font-semibold text-white flex items-center gap-2">
                        <i class="fas fa-globe text-blue-400"></i>
                        Public Profile
                        <span x-show="isPublic" class="text-xs px-2 py-0.5 bg-emerald-500/20 text-emerald-300 rounded-full border border-emerald-500/30">Live</span>
                        <span x-show="!isPublic" class="text-xs px-2 py-0.5 bg-gray-500/20 text-gray-400 rounded-full border border-gray-500/30">Private</span>
                    </p>
                    <p class="text-xs text-blue-300/60 mt-0.5">
                        <span x-show="isPublic">Anyone can find and view your profile</span>
                        <span x-show="!isPublic">Only you can see your profile</span>
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    @if($user->is_public)
                    <a href="{{ $user->getPublicUrl() }}" target="_blank"
                       class="text-xs text-emerald-400 hover:text-emerald-300 transition flex items-center gap-1">
                        <i class="fas fa-external-link-alt"></i> View
                    </a>
                    <span class="sr-only">Your Public Profile URL</span>
                    @endif
                    <button type="submit" @click="isPublic = !isPublic"
                            :class="isPublic ? 'bg-emerald-600' : 'bg-gray-600'"
                            class="relative inline-flex h-7 w-12 rounded-full transition-colors duration-200 focus:outline-none flex-shrink-0">
                        <span :class="isPublic ? 'translate-x-6' : 'translate-x-1'"
                              class="inline-block h-5 w-5 mt-1 rounded-full bg-white shadow transition-transform duration-200"></span>
                    </button>
                </div>
            </div>
        </form>

        {{-- Per-field visibility --}}
        <form method="post" action="{{ route('profile.visibility') }}">
            @csrf

            <p class="text-xs font-semibold text-blue-300/70 uppercase tracking-widest mb-3">What to show publicly</p>

            @php
                $visFields = [
                    ['key' => 'show_rank',             'icon' => 'fa-trophy',        'fab' => false, 'label' => 'Rank & Level'],
                    ['key' => 'show_badges',           'icon' => 'fa-crown',         'fab' => false, 'label' => 'Equipped Badges'],
                    ['key' => 'show_projects',         'icon' => 'fa-folder-open',   'fab' => false, 'label' => 'Projects'],
                    ['key' => 'show_skills',           'icon' => 'fa-network-wired', 'fab' => false, 'label' => 'Skill Tree Progress'],
                    ['key' => 'show_achievements',     'icon' => 'fa-award',         'fab' => false, 'label' => 'Achievements'],
                    ['key' => 'show_technical_skills', 'icon' => 'fa-code',          'fab' => false, 'label' => 'Technical Skills'],
                    ['key' => 'show_github',           'icon' => 'fa-github',        'fab' => true,  'label' => 'GitHub'],
                    ['key' => 'show_linkedin',         'icon' => 'fa-linkedin',      'fab' => true,  'label' => 'LinkedIn'],
                    ['key' => 'show_email',            'icon' => 'fa-envelope',      'fab' => false, 'label' => 'Email Address', 'default' => false],
                    ['key' => 'show_certifications',   'icon' => 'fa-certificate',   'fab' => false, 'label' => 'Certifications'],
                ];
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-5">
                @foreach($visFields as $field)
                @php $on = $vis[$field['key']] ?? ($field['default'] ?? true); @endphp
                <label class="flex items-center justify-between px-3 py-2.5 rounded-lg bg-white/5 border border-white/8 hover:border-white/20 cursor-pointer transition-colors">
                    <div class="flex items-center gap-2.5">
                        <i class="{{ $field['fab'] ? 'fab' : 'fas' }} {{ $field['icon'] }} text-sm text-purple-400 w-4 text-center"></i>
                        <span class="text-sm text-gray-200">{{ $field['label'] }}</span>
                    </div>
                    <div class="relative flex-shrink-0 ml-3">
                        <input type="checkbox"
                               name="visibility_settings[{{ $field['key'] }}]"
                               value="1"
                               {{ $on ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="w-9 h-5 bg-gray-600 peer-checked:bg-purple-600 rounded-full transition-colors duration-200"></div>
                        <div class="absolute left-0.5 top-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200 peer-checked:translate-x-4"></div>
                    </div>
                </label>
                @endforeach
            </div>

            <button type="submit"
                    class="btn-glow bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-500 hover:to-cyan-500 px-6 py-2.5 rounded-xl font-bold text-white text-sm shadow-lg transition-all">
                <i class="fas fa-save mr-2"></i> Save
            </button>
        </form>
    </div>

    {{-- Always private note --}}
    <p class="text-xs text-gray-500 flex items-center gap-1.5 px-1">
        <i class="fas fa-lock text-gray-600"></i>
        Phone, address, work experience, education and resume details are always private.
    </p>
</div>
