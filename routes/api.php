<?php

use App\Services\NvidiaResumeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/resume/generate', function (Request $request, NvidiaResumeService $service) {
    $validated = $request->validate([
        'profile' => ['required', 'array'],
        'target_role' => ['nullable', 'string', 'max:255'],
    ]);

    return response()->json([
        'success' => true,
        'resume' => $service->generateResumeJson(
            $validated['profile'],
            $validated['target_role'] ?? null
        ),
    ]);
})->middleware('throttle:10,1');
