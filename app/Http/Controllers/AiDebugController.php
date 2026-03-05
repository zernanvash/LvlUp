<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiDebugController extends Controller
{
    public function test()
    {
        $start = microtime(true);

        try {
            $response = Http::timeout(15)->post(
                config('gemini.base_url') . 'gemini-2.5-flash:generateContent?key=' . config('gemini.api_key'),
                [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => 'Reply ONLY with: {"status":"ok"}']
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0,
                        'response_mime_type' => 'application/json'
                    ]
                ]
            );

            $duration = round((microtime(true) - $start) * 1000, 2);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'http_status' => $response->status(),
                    'body' => $response->body(),
                    'latency_ms' => $duration
                ], 500);
            }

            $rawText = $response['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (!$rawText) {
                return response()->json([
                    'success' => false,
                    'error' => 'Empty AI response',
                    'latency_ms' => $duration
                ], 500);
            }

            $decoded = json_decode($rawText, true);

            return response()->json([
                'success' => true,
                'model_working' => isset($decoded['status']) && $decoded['status'] === 'ok',
                'latency_ms' => $duration,
                'raw_response' => $rawText,
                'json_valid' => json_last_error() === JSON_ERROR_NONE,
                'json_error' => json_last_error_msg()
            ]);
        } catch (\Throwable $e) {

            Log::error('AI Debug Failure', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'exception' => $e->getMessage()
            ], 500);
        }
    }
}