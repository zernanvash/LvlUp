@props(['skills', 'style' => 'tags'])

@if($style === 'tags')
    <div class="flex flex-wrap gap-2">
        @foreach($skills as $skill)
            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                {{ $skill->name }}
            </span>
        @endforeach
    </div>
@elseif($style === 'list')
    <ul class="list-disc list-inside space-y-1">
        @foreach($skills as $skill)
            <li class="text-gray-700">{{ $skill->name }}</li>
        @endforeach
    </ul>
@elseif($style === 'inline')
    <div class="text-gray-700">
        @foreach($skills as $index => $skill)
            {{ $skill->name }}@if(!$loop->last), @endif
        @endforeach
    </div>
@endif
