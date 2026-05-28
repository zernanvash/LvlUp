<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\NvidiaResumeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResumeGenerationController extends Controller
{
    public function store(Request $request, NvidiaResumeService $service): JsonResponse
    {
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
    }
}
