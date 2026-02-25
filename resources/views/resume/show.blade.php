<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Resume — {{ $resume->job_title }}
            </h2>
            <div class="flex gap-3">
                @if($resume->pdf_path)
                    <a href="{{ route('resume.download', $resume) }}"
                        class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold py-2 px-4 rounded-lg flex items-center gap-2 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Download PDF
                    </a>
                @endif
                <a href="{{ route('resume.index') }}"
                    class="text-sm text-gray-500 hover:text-gray-700 py-2 px-4 border rounded-lg transition">
                    ← All Resumes
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- Match Score Banner --}}
            @if($resume->match_score)
            <div class="bg-white rounded-lg shadow-sm p-4 flex items-center gap-4">
                <div class="flex-shrink-0 w-16 h-16 rounded-full flex items-center justify-center text-lg font-bold
                    {{ $resume->match_score >= 75 ? 'bg-green-100 text-green-700' : ($resume->match_score >= 50 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                    {{ $resume->match_score }}%
                </div>
                <div>
                    <p class="font-semibold text-gray-800">Keyword Match Score</p>
                    <p class="text-sm text-gray-500">
                        @if($resume->match_score >= 75)
                            Great match! Your resume covers most of the target keywords.
                        @elseif($resume->match_score >= 50)
                            Decent match. Consider adding more of the target keywords.
                        @else
                            Low match. The AI may not have had enough content to work with.
                        @endif
                    </p>
                </div>
            </div>
            @endif

            {{-- Resume Preview --}}
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="p-8 font-sans max-w-2xl mx-auto">

                    {{-- Header --}}
                    <div class="text-center border-b pb-6 mb-6">
                        <h1 class="text-2xl font-bold text-gray-900">{{ auth()->user()->name }}</h1>
                        <p class="text-gray-500 text-sm mt-1">{{ auth()->user()->email }}</p>
                        <p class="text-purple-600 font-semibold mt-2">{{ $resume->job_title }}</p>
                    </div>

                    {{-- Summary --}}
                    @if($resumeData['summary'])
                    <div class="mb-6">
                        <h2 class="text-xs font-bold uppercase tracking-widest text-purple-600 mb-2">Professional Summary</h2>
                        <p class="text-gray-700 text-sm leading-relaxed">{{ $resumeData['summary'] }}</p>
                    </div>
                    @endif

                    {{-- Skills --}}
                    @if($resumeData['skills'])
                    <div class="mb-6">
                        <h2 class="text-xs font-bold uppercase tracking-widest text-purple-600 mb-2">Skills</h2>
                        <div class="flex flex-wrap gap-2">
                            @foreach(explode(',', $resumeData['skills']) as $skill)
                                <span class="bg-purple-50 text-purple-700 text-xs px-2 py-1 rounded-full border border-purple-200">
                                    {{ trim($skill) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Projects --}}
                    @if($resumeData['projects'])
                    <div class="mb-6">
                        <h2 class="text-xs font-bold uppercase tracking-widest text-purple-600 mb-3">Projects</h2>
                        <div class="space-y-3 text-sm text-gray-700">
                            {!! nl2br(e($resumeData['projects'])) !!}
                        </div>
                    </div>
                    @endif

                    {{-- Experience --}}
                    @if($resumeData['experience'])
                    <div class="mb-6">
                        <h2 class="text-xs font-bold uppercase tracking-widest text-purple-600 mb-2">Experience</h2>
                        <div class="text-sm text-gray-700 space-y-2">
                            {!! nl2br(e($resumeData['experience'])) !!}
                        </div>
                    </div>
                    @endif

                    {{-- Education --}}
                    @if($resumeData['education'])
                    <div class="mb-6">
                        <h2 class="text-xs font-bold uppercase tracking-widest text-purple-600 mb-2">Education</h2>
                        <div class="text-sm text-gray-700">
                            {!! nl2br(e($resumeData['education'])) !!}
                        </div>
                    </div>
                    @endif

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
