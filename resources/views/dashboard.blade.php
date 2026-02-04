@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">


        @foreach($projects as $project)
        <div
            class="bg-[#161b22] border border-[#30363d] rounded-2xl overflow-hidden hover:border-[#8b949e] transition-all duration-300 group shadow-lg">
            <div class="h-44 bg-[#010409] flex flex-col items-center justify-center relative border-b border-[#30363d]">
                <i
                    class="fa-solid fa-code-branch text-4xl text-[#30363d] group-hover:text-indigo-500/50 transition-colors"></i>
                <span class="absolute bottom-4 right-4 text-[10px] font-mono text-slate-600">
                    ID: {{ str_pad($project->id, 3, '0', STR_PAD_LEFT) }}
                </span>
            </div>

            <div class="p-6">
                <h3 class="font-bold text-[#f0f6fc] text-lg">{{ $project->name }}</h3>
                <p class="text-sm text-[#8b949e] mt-2 line-clamp-3 leading-relaxed">
                    {{ $project->description ?? 'No description available.' }}
                </p>

                <div class="mt-6 pt-4 border-t border-[#30363d] flex items-center justify-between">
                    <div class="flex gap-2">
                        <span class="w-3 h-3 rounded-full bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.5)]"></span>
                        <span class="text-[10px] font-bold text-[#8b949e] uppercase">
                            {{ $project->language ?? 'Unknown' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div x-data="{ open: false }" class="fixed bottom-8 right-8 z-[60]">
        <div x-show="open" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            x-cloak
            class="absolute bottom-16 right-0 w-56 bg-[#161b22] border border-[#30363d] rounded-xl shadow-2xl overflow-hidden">
            <button
                class="w-full px-5 py-4 text-left text-sm text-[#f0f6fc] hover:bg-[#21262d] flex items-center gap-3 border-b border-[#30363d]">
                <i class="fa-solid fa-folder-plus text-indigo-400"></i> Add Project
            </button>
            <button
                class="w-full px-5 py-4 text-left text-sm text-[#f0f6fc] hover:bg-[#21262d] flex items-center gap-3">
                <i class="fa-solid fa-file-invoice text-emerald-400"></i> Generate a Resume
            </button>
        </div>

        <button @click="open = !open"
            class="w-14 h-14 bg-[#238636] hover:bg-[#2ea043] text-white rounded-full shadow-2xl flex items-center justify-center transition-all transform active:scale-90"
            :class="open ? 'rotate-45 !bg-[#da3633]' : ''">
            <i class="fa-solid fa-plus text-xl"></i>
        </button>
    </div>
</div>
@endsection