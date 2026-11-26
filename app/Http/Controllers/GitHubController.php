<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GitHubController extends Controller
{
    public function getIssues(Request $request)
    {
        // Firebase authentication token
        $firebaseToken = $request->bearerToken();

        if (!$firebaseToken) {
            return response()->json([
                'message' => 'Token autentikasi tidak ditemukan. Silakan login kembali.'
            ], 401);
        }

        // Maximum issue configuration
        $maximum = config('services.github.max_issues', 500);

        $request->validate([
            'issue_url' => ['required', 'url'],
            'max_issues' => [
                'sometimes',
                'integer',
                'min:1',
                'max:' . $maximum
            ],
            'state' => ['sometimes', 'in:open,closed,all'],
        ]);

        // Validate GitHub repository URL
        $url = rtrim(trim($request->issue_url), '/');

        if (!preg_match(
            '#^https://github\.com/([A-Za-z0-9-]+)/([A-Za-z0-9_.-]+)/issues$#i',
            $url,
            $matches
        )) {
            return response()->json([
                'message' => 'Gunakan URL https://github.com/owner/repository/issues.'
            ], 422);
        }

        // GitHub token
        $token = trim((string) config('services.github.token'));

        if ($token === '') {
            return response()->json([
                'message' => 'GITHUB_TOKEN belum diisi di .env.'
            ], 503);
        }

        $owner = $matches[1];
        $repo = $matches[2];

        $repository = "{$owner}/{$repo}";

        // Analysis limit
        $limit = (int) $request->input(
            'max_issues',
            min(
                $maximum,
                max(1, config('services.github.default_limit', 20))
            )
        );

        $state = $request->input(
            'state',
            config('services.github.state', 'open')
        );

        $issues = collect();

        $page = 0;
        $hasNext = true;
        $rateLimit = [];

        $timeout = config('services.ai.timeout', 240);

        @set_time_limit($timeout + 120);

        // =================================================
        // FETCH GITHUB ISSUES
        // =================================================

        try {
            while (
                $issues->count() < $limit &&
                $hasNext &&
                $page < 20
            ) {
                $page++;

                $response = Http::withToken($token)
                    ->acceptJson()
                    ->withHeaders([
                        'Accept' => 'application/vnd.github+json',
                        'User-Agent' => 'FeedbackRise',
                        'X-GitHub-Api-Version' => '2026-03-10',
                    ])
                    ->connectTimeout(10)
                    ->timeout(30)
                    ->get(
                        "https://api.github.com/repos/{$owner}/{$repo}/issues",
                        [
                            'state' => $state,
                            'per_page' => 100,
                            'page' => $page,
                            'sort' => 'created',
                            'direction' => 'desc'
                        ]
                    );

                // GitHub rate limit information
                $rateLimit = [
                    'remaining' => $response->header('X-RateLimit-Remaining'),
                    'reset' => $response->header('X-RateLimit-Reset'),
                ];

                if ($response->failed()) {
                    $status = $response->status();

                    $message = $status === 401
                        ? 'Token GitHub tidak valid atau kedaluwarsa.'
                        : (
                            $status === 403 || $status === 429
                            ? 'GitHub menolak request. Periksa kuota dan izin token.'
                            : 'Gagal mengambil issue dari GitHub.'
                        );

                    return response()->json([
                        'message' => $message,
                        'service' => 'github',
                        'status' => $status,
                        'github_rate_limit' => $rateLimit,
                    ], (
                        $status === 429 ||
                        ($status === 403 && $rateLimit['remaining'] === '0')
                    ) ? 429 : 502);
                }

                // Collect issues (exclude pull requests)
                foreach ($response->json() as $issue) {
                    if (isset($issue['pull_request'])) {
                        continue;
                    }

                    $issues->push([
                        'number' => $issue['number'],
                        'title' => $issue['title'],
                        'body' => mb_substr(
                            $issue['body'] ?? '',
                            0,
                            2000
                        ),
                        'state' => $issue['state'],
                        'url' => $issue['html_url'],
                    ]);

                    if ($issues->count() >= $limit) {
                        break;
                    }
                }

                // Check next page from GitHub Link header
                $hasNext = preg_match(
                    '/;\s*rel="next"/',
                    $response->header('Link') ?? ''
                ) === 1;
            }

        } catch (ConnectionException $exception) {
            return response()->json([
                'message' => 'Tidak dapat terhubung ke GitHub.',
                'service' => 'github'
            ], 502);
        }

        // =================================================
        // CHECK EMPTY ISSUES
        // =================================================

        if ($issues->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ditemukan issue biasa pada halaman yang diperiksa.',
                'github_pages' => $page
            ], 422);
        }

        // =================================================
        // SEND ISSUES TO AI SERVICE
        // =================================================

        try {
            $aiResponse = Http::withToken($firebaseToken)
                ->acceptJson()
                ->connectTimeout(5)
                ->timeout($timeout)
                ->post(
                    rtrim(config('services.ai.url'), '/') . '/analyze',
                    [
                        'repository' => $repository,
                        'issues' => $issues->values()->all()
                    ]
                );

        } catch (ConnectionException $exception) {
            return response()->json([
                'message' => 'AI Service tidak terhubung atau terlalu lama merespons.',
                'service' => 'ai'
            ], 502);
        }

        // =================================================
        // CHECK AI RESPONSE
        // =================================================

        if ($aiResponse->failed()) {
            return response()->json([
                'message' => $aiResponse->json('detail')
                    ?? 'AI Service gagal memproses issue.',
                'service' => 'ai',
                'status' => $aiResponse->status(),
            ], $aiResponse->status() === 429 ? 429 : 502);
        }

        $analysis = $aiResponse->json();

        if (
            !is_array($analysis) ||
            !isset($analysis['recommendations'])
        ) {
            return response()->json([
                'message' => 'Respons AI Service tidak sesuai.'
            ], 502);
        }

        // =================================================
        // RESPONSE
        // =================================================

        return response()->json([
            'repository' => $repository,
            'requested_limit' => $limit,
            'total_issues' => $issues->count(),
            'state' => $state,

            'selection' => 'Issue terbaru berdasarkan tanggal dibuat; Pull Request dan komentar tidak dianalisis.',

            'limit_reached' => $issues->count() >= $limit,

            'page_scan_limit_reached' => (
                $page >= 20 &&
                $hasNext &&
                $issues->count() < $limit
            ),

            'github_pages' => $page,

            'github_rate_limit' => $rateLimit,

            'issues' => $issues->values(),

            'analysis' => $analysis,
        ]);
    }
}
