@props(['user', 'style' => 'default'])

@if($style === 'centered')
    <div class="text-center mb-6">
        <h1 class="text-3xl font-bold">{{ $user->name }}</h1>
        @if($user->title)
            <p class="text-lg text-gray-600 mt-2">{{ $user->title }}</p>
        @endif
        <p class="text-sm text-gray-500 mt-1">{{ $user->email }}</p>
    </div>
@elseif($style === 'sidebar')
    <div class="mb-6">
        <h1 class="text-4xl font-bold mb-2">{{ $user->name }}</h1>
        @if($user->title)
            <p class="text-lg mb-3">{{ $user->title }}</p>
        @endif
        <p class="text-sm">{{ $user->email }}</p>
    </div>
@else
    <div class="mb-6">
        <h1 class="text-3xl font-bold">{{ $user->name }}</h1>
        @if($user->title)
            <p class="text-lg text-gray-600 mt-1">{{ $user->title }}</p>
        @endif
        <p class="text-sm text-gray-500 mt-1">{{ $user->email }}</p>
    </div>
@endif
