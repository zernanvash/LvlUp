@extends('layouts.app')

@section('title', 'Profile')
@section('page_title', auth()->user()->name)
@section('page_subtitle', auth()->user()->title ?? 'Developer')

@section('content')
@php
    $user = auth()->user();
    $vis = $user->visibility_settings ?? [];
    $activeTab = session('active_tab', 'overview');
@endphp

<div class="max-w-7xl mx-auto" x-data="{ tab: '{{ $activeTab }}' }">

    {{-- Tab Navigation --}}
    <div class="flex items-center gap-2 mb-6 overflow-x-auto pb-1">
        @php
            $tabs = [
                'overview'   => ['icon' => 'fa-user',         'label' => 'Overview'],
                'settings'   => ['icon' => 'fa-cog',          'label' => 'Profile Settings'],
                'visibility' => ['icon' => 'fa-eye',          'label' => 'Visibility'],
                'resume'     => ['icon' => 'fa-file-alt',     'label' => 'Resume Details'],
                'security'   => ['icon' => 'fa-shield-alt',   'label' => 'Security'],
            ];
        @endphp
        @foreach($tabs as $key => $t)
        <button
            @click="tab = '{{ $key }}'"
            :class="tab === '{{ $key }}'
                ? 'bg-gradient-to-r from-purple-600 to-pink-600 text-white shadow-lg shadow-purple-500/30 ring-2 ring-purple-400/50'
                : 'bg-white/5 text-purple-300 hover:bg-white/10 hover:text-white'"
            class="flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-sm transition-all whitespace-nowrap"
        >
            <i class="fas {{ $t['icon'] }} text-xs"></i>
            {{ $t['label'] }}
        </button>
        @endforeach

        @if($user->is_public)
        <a href="{{ $user->getPublicUrl() }}" target="_blank"
           class="ml-auto flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-sm bg-emerald-600/20 text-emerald-300 hover:bg-emerald-600/30 border border-emerald-500/30 transition-all whitespace-nowrap">
            <i class="fas fa-external-link-alt text-xs"></i> View Public Profile
        </a>
        @endif
    </div>

    {{-- Flash Messages --}}
    @if(session('status') === 'profile-updated')
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
         class="mb-4 px-4 py-3 rounded-xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 text-sm flex items-center gap-2">
        <i class="fas fa-check-circle"></i> Profile saved successfully.
    </div>
    @endif
    @if(session('status') === 'password-updated')
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
         class="mb-4 px-4 py-3 rounded-xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 text-sm flex items-center gap-2">
        <i class="fas fa-check-circle"></i> Password updated.
    </div>
    @endif
    @if(session('status') === 'visibility-updated')
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
         class="mb-4 px-4 py-3 rounded-xl bg-blue-500/20 border border-blue-500/40 text-blue-300 text-sm flex items-center gap-2">
        <i class="fas fa-eye"></i> Visibility settings updated.
    </div>
    @endif

    {{-- OVERVIEW TAB --}}
    <div x-show="tab === 'overview'" x-transition>
        @include('profile.partials.overview')
    </div>

    {{-- SETTINGS TAB --}}
    <div x-show="tab === 'settings'" x-transition>
        @include('profile.partials.update-profile-information-form')
    </div>

    {{-- VISIBILITY TAB --}}
    <div x-show="tab === 'visibility'" x-transition>
        @include('profile.partials.update-profile-visibility')
    </div>

    {{-- RESUME DETAILS TAB --}}
    <div x-show="tab === 'resume'" x-transition>
        @include('profile.partials.resume-details-form')
    </div>

    {{-- SECURITY TAB --}}
    <div x-show="tab === 'security'" x-transition class="space-y-6">
        <div class="glow-border rounded-2xl p-8 bg-gradient-to-br from-pink-900/40 to-pink-950/40 backdrop-blur">
            @include('profile.partials.update-password-form')
        </div>
        <div class="glow-border rounded-2xl p-8 bg-gradient-to-br from-red-900/40 to-red-950/40 backdrop-blur">
            @include('profile.partials.delete-user-form')
        </div>
    </div>

</div>
@endsection
