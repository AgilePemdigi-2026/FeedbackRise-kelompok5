<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AIController extends Controller
{
    public function analyze(Request $request)
    {
        $request->validate([
            'issues' => ['required', 'array', 'min:1'],
            'issues.*' => ['required', 'string'],
        ]);

        $response = Http::post(
            env('AI_SERVICE_URL') . '/analyze',
            [
                'issues' => $request->issues,
            ]
        );

        if ($response->failed()) {
            return response()->json([
                'message' => 'Gagal menghubungi AI Service.',
                'error' => $response->body(),
            ], 500);
        }

        return response()->json(
            $response->json()
        );
    }
}