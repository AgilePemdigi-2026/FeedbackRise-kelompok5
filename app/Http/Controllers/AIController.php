<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AIController extends Controller
{
    public function analyze(Request $request)
    {
        $request->validate([
            'issues' => ['required', 'array', 'min:1', 'max:' . config('services.github.max_issues', 500)],
            'issues.*' => ['required', 'string', 'max:10000'],
        ]);

        @set_time_limit(config('services.ai.timeout', 240) + 30);
        try {
            $response = Http::acceptJson()->connectTimeout(5)->timeout(config('services.ai.timeout', 240))->post(
                rtrim(config('services.ai.url'), '/') . '/analyze',
                ['issues' => $request->issues]
            );
        } catch (ConnectionException $exception) {
            return response()->json(['message' => 'AI Service tidak terhubung atau terlalu lama merespons. Pastikan Uvicorn berjalan di port 8001.'], 502);
        }

        if ($response->failed()) {
            return response()->json(['message' => $response->json('detail') ?? 'AI Service gagal memproses issue.'], $response->status() === 429 ? 429 : 502);
        }
        return response()->json($response->json());
    }
}
