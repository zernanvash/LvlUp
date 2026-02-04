@extends('layouts.app')

@section('title', 'Profile')

@section('header_right')
    <button class="bg-slate-100 hover:bg-indigo-50 text-indigo-600 px-4 py-2 rounded-xl text-sm font-bold flex items-center gap-2 transition">
        <i class="fa-solid fa-pencil"></i> Edit Bio
    </button>
@endsection

@section('content')
<div class="max-w-4xl mx-auto p-8">
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="h-32 bg-indigo-600 w-full"></div>
        <div class="px-10 pb-10">
            <div class="flex flex-col md:flex-row items-end -mt-16 gap-6 mb-8">
                <div class="w-32 h-32 rounded-3xl bg-white p-2 shadow-lg">
                    <img src="https://ui-avatars.com/api/?name=User&size=128" class="w-full h-full rounded-2xl object-cover">
                </div>
                <div class="flex-1 pb-2">
                    <h2 class="text-3xl font-black text-slate-800">John Doe</h2>
                    <p class="text-indigo-600 font-bold uppercase text-xs tracking-widest">Master Architect • Level 42</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                <div class="md:col-span-2">
                    <h4 class="text-xs font-black uppercase text-slate-400 mb-4 tracking-tighter">Bio</h4>
                    <p class="text-slate-600 leading-relaxed text-lg italic">
                        "Building digital worlds one commit at a time. Focused on high-performance backend systems and interactive UI/UX."
                    </p>
                </div>
                <div class="bg-slate-50 rounded-2xl p-6">
                    <h4 class="text-[10px] font-black uppercase text-slate-400 mb-4">Top Badges</h4>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3 bg-white p-2 rounded-lg border border-slate-100">
                            <span class="text-xl">🥇</span>
                            <span class="text-xs font-bold text-slate-700">Early Adopter</span>
                        </div>
                        <div class="flex items-center gap-3 bg-white p-2 rounded-lg border border-slate-100">
                            <span class="text-xl">🚀</span>
                            <span class="text-xs font-bold text-slate-700">Fast Mover</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection