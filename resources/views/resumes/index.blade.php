<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Resumes') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold">Resume Builder</h3>
                        <a href="{{ route('resumes.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Create New Resume
                        </a>
                    </div>

                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($resumes->count() > 0)
                        <div class="space-y-4">
                            @foreach($resumes as $resume)
                                <div class="border rounded-lg p-4 hover:shadow-md transition">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-semibold text-lg">{{ $resume->job_title }}</h4>
                                            <p class="text-sm text-gray-600 mt-1">
                                                Created: {{ $resume->created_at->format('M d, Y') }}
                                            </p>
                                            @if($resume->match_score)
                                                <p class="text-sm text-gray-600">
                                                    Match Score: <span class="font-semibold text-blue-600">{{ $resume->match_score }}%</span>
                                                </p>
                                            @endif
                                        </div>
                                        <div class="flex gap-2">
                                            <a href="{{ route('resumes.show', $resume) }}" class="text-blue-600 hover:text-blue-800">
                                                View
                                            </a>
                                            @if($resume->pdf_path)
                                                <a href="{{ route('resumes.download', $resume) }}" class="text-green-600 hover:text-green-800">
                                                    Download PDF
                                                </a>
                                            @else
                                                <form action="{{ route('resumes.generate', $resume) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-purple-600 hover:text-purple-800">
                                                        Generate PDF
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $resumes->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <p class="text-gray-600 mb-4">You haven't created any resumes yet.</p>
                            <a href="{{ route('resumes.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Create Your First Resume
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
