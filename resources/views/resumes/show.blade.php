<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Resume Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-2xl font-semibold">{{ $resume->job_title }}</h3>
                            <p class="text-sm text-gray-600 mt-1">
                                Created: {{ $resume->created_at->format('M d, Y') }}
                            </p>
                            @if($resume->match_score)
                                <p class="text-sm text-gray-600">
                                    Match Score: <span class="font-semibold text-blue-600">{{ $resume->match_score }}%</span>
                                </p>
                            @endif
                            @if($resume->template)
                                <p class="text-sm text-gray-600">
                                    Template: <span class="font-semibold capitalize">{{ $resume->template }}</span>
                                </p>
                            @endif
                        </div>
                        <div class="flex gap-2">
                            @if($resume->pdf_path)
                                <a href="{{ route('resumes.download', $resume) }}" 
                                   class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                    Download PDF
                                </a>
                            @else
                                <form action="{{ route('resumes.generate', $resume) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                                        Generate PDF
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('resumes.index') }}" 
                               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Back to List
                            </a>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h4 class="font-semibold text-lg mb-2">Job Description</h4>
                        <div class="bg-gray-50 p-4 rounded border">
                            <p class="whitespace-pre-wrap">{{ $resume->job_description }}</p>
                        </div>
                    </div>

                    @if($resume->target_keywords)
                        <div class="mb-6">
                            <h4 class="font-semibold text-lg mb-2">Target Keywords</h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach(explode(', ', $resume->target_keywords) as $keyword)
                                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                                        {{ $keyword }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="mb-6">
                        <h4 class="font-semibold text-lg mb-2">Selected Projects ({{ $projects->count() }})</h4>
                        @if($projects->count() > 0)
                            <div class="space-y-3">
                                @foreach($projects as $project)
                                    <div class="border rounded-lg p-4 hover:shadow-md transition">
                                        <h5 class="font-semibold">{{ $project->name }}</h5>
                                        <p class="text-sm text-gray-600 mt-1">{{ $project->description }}</p>
                                        @if($project->skills->count() > 0)
                                            <div class="flex flex-wrap gap-2 mt-2">
                                                @foreach($project->skills as $skill)
                                                    <span class="bg-gray-200 text-gray-700 px-2 py-1 rounded text-xs">
                                                        {{ $skill->name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-600">No projects selected.</p>
                        @endif
                    </div>

                    @if($skills->count() > 0)
                        <div class="mb-6">
                            <h4 class="font-semibold text-lg mb-2">Selected Skills ({{ $skills->count() }})</h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach($skills as $skill)
                                    <span class="bg-blue-500 text-white px-3 py-1 rounded-full text-sm">
                                        {{ $skill->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
