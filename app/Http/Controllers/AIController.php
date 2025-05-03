<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;

use Illuminate\Http\Request;

class AIController extends Controller
{
    public function askOpenAI(Request $request)
    {
        $question = $request->question;
        if (!$question) {
            return response()->json(['error' => 'Prompt required.'], 422);
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.openrouter.api_key'),
            'Content-Type' => 'application/json',
            'HTTP-Referer' => 'http://asib-hasan.com',
            'X-Title' => 'Your App Name',
        ])->post('https://openrouter.ai/api/v1/chat/completions', [
            'model' => 'qwen/qwen3-0.6b-04-28:free',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => 'Only give me the main response ' . $question,
                ]
            ]
        ]);

        if ($response->successful()) {
            return response()->json([
                'message' => $response->json()['choices'][0]['message']['content']
            ]);
        }

        return response()->json([
            'error' => 'OpenRouter API failed',
            'details' => $response->json()
        ], $response->status());
    }
}
