<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GitHubController extends Controller
{
    public function getIssues(Request $request)
    {
        $maximum = config('services.github.max_issues', 500);
        $request->validate([
            'issue_url' => ['required', 'url'],
            'max_issues' => ['sometimes', 'integer', 'min:1', 'max:' . $maximum],
            'state' => ['sometimes', 'in:open,closed,all'],
        ]);

        $url = rtrim(trim($request->issue_url), '/');
        if (!preg_match('#^https://github\.com/([A-Za-z0-9-]+)/([A-Za-z0-9_.-]+)/issues$#i', $url, $matches)) {
            return response()->json(['message' => 'Gunakan URL https://github.com/owner/repository/issues.'], 422);
        }

        $token = trim((string) config('services.github.token'));
        if ($token === '') {
            return response()->json(['message' => 'GITHUB_TOKEN belum diisi di .env. Isi token lalu jalankan php artisan config:clear.'], 503);
        }

        $owner = $matches[1];
        $repo = $matches[2];
        $limit = (int) $request->input('max_issues', min($maximum, max(1, config('services.github.default_limit', 20))));
        $state = $request->input('state', config('services.github.state', 'open'));
        $issues = collect();
        $page = 0;
        $hasNext = true;
        $rateLimit = [];
        $timeout = config('services.ai.timeout', 240);
        @set_time_limit($timeout + 120);

        // Limit total issue setelah Pull Request disaring. Batas halaman mencegah
        // request tanpa akhir pada repository yang didominasi Pull Request.
        try {
            while ($issues->count() < $limit && $hasNext && $page < 20) {
                $page++;
                $response = Http::withToken($token)->acceptJson()->withHeaders([
                    'Accept' => 'application/vnd.github+json',
                    'User-Agent' => 'FeedbackRise',
                    'X-GitHub-Api-Version' => '2026-03-10',
                ])->connectTimeout(10)->timeout(30)->get(
                    "https://api.github.com/repos/{$owner}/{$repo}/issues",
                    ['state' => $state, 'per_page' => 100, 'page' => $page, 'sort' => 'created', 'direction' => 'desc']
                );

                $rateLimit = [
                    'remaining' => $response->header('X-RateLimit-Remaining'),
                    'reset' => $response->header('X-RateLimit-Reset'),
                ];

                if ($response->failed()) {
                    $status = $response->status();
                    $message = $status === 401
                        ? 'Token GitHub tidak valid atau kedaluwarsa. Periksa GITHUB_TOKEN.'
                        : ($status === 403 || $status === 429
                            ? 'GitHub menolak request. Periksa kuota, izin token, dan waktu reset.'
                            : 'Gagal mengambil issue dari GitHub. Periksa URL repository.');
                    return response()->json([
                        'message' => $message,
                        'service' => 'github',
                        'status' => $status,
                        'github_rate_limit' => $rateLimit,
                    ], $status === 429 || ($status === 403 && $rateLimit['remaining'] === '0') ? 429 : 502);
                }

                foreach ($response->json() as $issue) {
                    if (isset($issue['pull_request'])) {
                        continue;
                    }
                    $issues->push([
                        'number' => $issue['number'],
                        'title' => $issue['title'],
                        'body' => mb_substr($issue['body'] ?? '', 0, 2000),
                        'state' => $issue['state'],
                        'url' => $issue['html_url'],
                    ]);
                    if ($issues->count() >= $limit) {
                        break;
                    }
                }
                $hasNext = preg_match('/;\s*rel="next"/', $response->header('Link') ?? '') === 1;
            }
        } catch (ConnectionException $exception) {
            return response()->json(['message' => 'Tidak dapat terhubung ke GitHub. Periksa koneksi internet.', 'service' => 'github'], 502);
        }

        if ($issues->isEmpty()) {
            return response()->json(['message' => 'Tidak ditemukan issue biasa pada halaman yang diperiksa. Coba pilihan Open + Closed.', 'github_pages' => $page], 422);
        }

        try {
            $aiResponse = Http::acceptJson()->connectTimeout(5)->timeout($timeout)->post(
                rtrim(config('services.ai.url'), '/') . '/analyze',
                ['issues' => $issues->values()->all()]
            );
        } catch (ConnectionException $exception) {
            return response()->json(['message' => 'AI Service tidak terhubung atau terlalu lama merespons. Pastikan Uvicorn berjalan di port 8001 dan coba kurangi jumlah issue.', 'service' => 'ai'], 502);
        }

        if ($aiResponse->failed()) {
            return response()->json([
                'message' => $aiResponse->json('detail') ?? 'AI Service gagal memproses issue. Lihat terminal Uvicorn.',
                'service' => 'ai',
                'status' => $aiResponse->status(),
            ], $aiResponse->status() === 429 ? 429 : 502);
        }

        $analysis = $aiResponse->json();
        if (!is_array($analysis) || !isset($analysis['recommendations'])) {
            return response()->json(['message' => 'Respons AI Service tidak sesuai. Pastikan main.py terbaru sudah dijalankan ulang.', 'service' => 'ai'], 502);
        }

        return response()->json([
            'repository' => "{$owner}/{$repo}",
            'requested_limit' => $limit,
            'total_issues' => $issues->count(),
            'state' => $state,
            'selection' => 'Issue terbaru berdasarkan tanggal dibuat; Pull Request dan komentar tidak dianalisis.',
            'limit_reached' => $issues->count() >= $limit,
            'page_scan_limit_reached' => $page >= 20 && $hasNext && $issues->count() < $limit,
            'github_pages' => $page,
            'github_rate_limit' => $rateLimit,
            'issues' => $issues->values(),
            'analysis' => $analysis,
        ]);
    }
}
