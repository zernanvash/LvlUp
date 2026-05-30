<?php

namespace App\Mcp;

use Illuminate\Support\Facades\Auth;
use Laravel\Boost\Mcp\Tool;

class ResumeMcpTool extends Tool
{
    /**
     * Handle the MCP request and return the authenticated user's resume data.
     */
    public function handle(): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();

        return response()->json([
            'resume_job_title' => $user->resume_job_title,
            'resume_summary' => $user->resume_summary,
            'work_experience' => $user->work_experience,
            'education' => $user->education,
            'certifications' => $user->certifications,
            'languages' => $user->languages,
        ]);
    }
}
