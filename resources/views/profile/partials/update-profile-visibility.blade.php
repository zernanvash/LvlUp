<div class="space-y-6">
    <div class="glow-border rounded-2xl p-8 bg-gradient-to-br from-blue-900/40 to-blue-950/40 backdrop-blur">
        <h2 class="font-display text-xl font-bold text-white mb-1 flex items-center gap-2">
            <i class="fas fa-eye text-blue-400"></i> Visibility Settings
        </h2>
        <p class="text-blue-300/70 text-sm mb-6">Control what appears on your public profile.</p>

        {{-- Public/Private master toggle (its own form) --}}
        <form method="post" action="{{ route('profile.toggle-visibility') }}" x-data="{ isPublic: {{ $user->is_public ? 'true' : 'false' }} }" class="mb-6">
            @csrf @method('patch')
            <div class="flex items-center justify-between p-4 rounded-xl bg-white/5 border border-blue-500/20">
                <div>
                    <p class="font-semibold text-white flex items-center gap-2">
                        <i class="fas fa-globe text-blue-400"></i> Public Profile
                        <span x-show="isPublic" class="text-xs px-2 py-0.5 bg-emerald-500/20 text-emerald-300 rounded-full border border-emerald-500/30">Live</span>
                        <span x-show="!isPublic" class="text-xs px-2 py-0.5 bg-gray-500/20 text-gray-400 rounded-full border border-gray-500/30">Private</span>
                    </p>
                    <p class="text-sm text-blue-300/70 mt-0.5" x-show="isPublic">Anyone can view your public profile</p>
                    <p class="text-sm text-blue-300/70 mt-0.5" x-show="!isPublic">Only you can see your profile</p>
                </div>
                <button type="submit" @click="isPublic = !isPublic"
                        :class="isPublic ? 'bg-emerald-600' : 'bg-gray-600'"
                        class="relative inline-flex h-7 w-12 rounded-full transition-colors duration-200 focus:outline-none">
                    <span :class="isPublic ? 'translate-x-6' : 'translate-x-1'"
                          class="inline-block h-5 w-5 mt-1 rounded-full bg-white shadow transition-transform duration-200"></span>
                </button>
            </div>
        </form>

        {{-- Per-field visibility (dedicated route) --}}
        <form method="post" action="{{ route('profile.visibility') }}">
            @csrf @method('patch')

            <p class="text-sm font-semibold text-blue-300 mb-3 flex items-center gap-2">
                <i class="fas fa-sliders-h"></i> What to show on your public profile
            </p>

            @php
                $visFields = [
                    ['key' => 'show_email',           'icon' => 'fa-envelope',     'fab' => false, 'color' => 'purple', 'label' => 'Email Address',      'desc' => 'Show your email publicly'],
                    ['key' => 'show_linkedin',         'icon' => 'fa-linkedin',     'fab' => true,  'color' => 'blue',   'label' => 'LinkedIn Link',       'desc' => 'Show LinkedIn on profile'],
                    ['key' => 'show_github',           'icon' => 'fa-github',       'fab' => true,  'color' => 'gray',   'label' => 'GitHub Link',         'desc' => 'Show GitHub on profile'],
                    ['key' => 'show_badges',           'icon' => 'fa-crown',        'fab' => false, 'color' => 'amber',  'label' => 'Equipped Badges',     'desc' => 'Show your equipped badges'],
                    ['key' => 'show_rank',             'icon' => 'fa-trophy',       'fab' => false, 'color' => 'yellow', 'label' => 'Rank & Level',        'desc' => 'Show XP, level, and rank'],
                    ['key' => 'show_skills',           'icon' => 'fa-network-wired','fab' => false, 'color' => 'pink',   'label' => 'Skill Tree Progress', 'desc' => 'Show unlocked skill nodes'],
                    ['key' => 'show_achievements',     'icon' => 'fa-award',        'fab' => false, 'color' => 'green',  'label' => 'Achievements',        'desc' => 'Show earned badges count'],
                    ['key' => 'show_projects',         'icon' => 'fa-folder-open',  'fab' => false, 'color' => 'indigo', 'label' => 'Projects',            'desc' => 'Show featured projects'],
                    ['key' => 'show_technical_skills', 'icon' => 'fa-code',         'fab' => false, 'color' => 'cyan',   'label' => 'Technical Skills',    'desc' => 'Show your skills list'],
                    ['key' => 'show_certifications',   'icon' => 'fa-certificate',  'fab' => false, 'color' => 'orange', 'label' => 'Certifications',      'desc' => 'Show your certifications'],
                ];
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-6">
                @foreach($visFields as $field)
                @php $on = $vis[$field['key']] ?? true; @endphp
                <label class="flex items-center justify-between p-3 rounded-xl bg-white/5 border border-white/10 hover:border-{{ $field['color'] }}-500/40 cursor-pointer transition-colors group">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-{{ $field['color'] }}-500/20 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="{{ $field['fab'] ? 'fab' : 'fas' }} {{ $field['icon'] }} text-{{ $field['color'] }}-400 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-white">{{ $field['label'] }}</p>
                            <p class="text-xs text-gray-400">{{ $field['desc'] }}</p>
                        </div>
                    </div>
                    <div class="relative ml-3 flex-shrink-0">
                        <input type="checkbox"
                               name="visibility_settings[{{ $field['key'] }}]"
                               value="1"
                               {{ $on ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="w-10 h-6 bg-gray-600 peer-checked:bg-{{ $field['color'] }}-600 rounded-full transition-colors duration-200"></div>
                        <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200 peer-checked:translate-x-4"></div>
                    </div>
                </label>
                @endforeach
            </div>

            <div class="flex items-center gap-4">
                <button type="submit"
                        class="btn-glow bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-500 hover:to-cyan-500 px-8 py-3 rounded-xl font-bold text-white shadow-lg transition-all">
                    <i class="fas fa-save mr-2"></i> Save Visibility
                </button>
                @if($user->is_public)
                <a href="{{ $user->getPublicUrl() }}" target="_blank"
                   class="flex items-center gap-2 px-5 py-3 rounded-xl text-sm font-semibold bg-emerald-600/20 text-emerald-300 hover:bg-emerald-600/30 border border-emerald-500/30 transition-all">
                    <i class="fas fa-external-link-alt"></i> Preview
                </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Always private info --}}
    <div class="glow-border rounded-2xl p-5 bg-gradient-to-br from-gray-900/40 to-gray-950/40 backdrop-blur">
        <h3 class="text-sm font-bold text-white mb-2 flex items-center gap-2">
            <i class="fas fa-lock text-gray-400"></i> Always Private
        </h3>
        <div class="flex flex-wrap gap-2">
            @foreach(['Phone Number', 'Home Address', 'City / Country', 'Work Experience', 'Education', 'Resume Summary'] as $hidden)
            <span class="px-2.5 py-1 bg-gray-700/40 border border-gray-600/30 rounded-full text-xs text-gray-400">
                <i class="fas fa-lock mr-1 text-gray-500 text-xs"></i>{{ $hidden }}
            </span>
            @endforeach
        </div>
    </div>
</div>
