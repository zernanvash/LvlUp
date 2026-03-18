@props(['projects', 'showSkills' => true])

<div class="space-y-4">
    @foreach($projects as $project)
        <div class="project-item">
            <h3 class="font-semibold text-lg">{{ $project->name }}</h3>
            <p class="text-gray-700 mt-1">{{ $project->description }}</p>
            
            @if($showSkills && $project->skills->count() > 0)
                <div class="flex flex-wrap gap-2 mt-2">
                    @foreach($project->skills as $skill)
                        <span class="text-xs bg-gray-200 text-gray-700 px-2 py-1 rounded">
                            {{ $skill->name }}
                        </span>
                    @endforeach
                </div>
            @endif
        </div>
    @endforeach
</div>
