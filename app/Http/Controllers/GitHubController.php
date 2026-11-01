<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GitHubController extends Controller
{
    public function getIssues(Request $request)
    {
        $request->validate([
            'issue_url' => ['required', 'url'],
        ]);

        $url = rtrim($request->issue_url, '/');

        /*
         * Mengambil owner dan repository dari URL:
         * https://github.com/owner/repository/issues
         */
        if (!preg_match(
            '#github\.com/([^/]+)/([^/]+)/issues$#',
            $url,
            $matches
        )) {
            return response()->json([
                'message' => 'URL GitHub Issues tidak valid.'
            ], 400);
        }

        $owner = $matches[1];
        $repo = $matches[2];

        /*
         * Mengambil SELURUH Issues dari GitHub
         * menggunakan cursor-based pagination.
         */
        $allIssues = collect();

        $perPage = 100;
        $after = null;

        do {
            $query = [
                'state' => 'all',
                'per_page' => $perPage,
            ];

            /*
             * Request berikutnya menggunakan cursor
             * "after" yang diberikan oleh GitHub.
             */
            if ($after) {
                $query['after'] = $after;
            }

            $response = Http::withToken(env('GITHUB_TOKEN'))
                ->withHeaders([
                    'Accept' => 'application/vnd.github+json',
                    'User-Agent' => 'FeedbackRise',
                    'X-GitHub-Api-Version' => '2026-03-10',
                ])
                ->get(
                    "https://api.github.com/repos/{$owner}/{$repo}/issues",
                    $query
                );

            if ($response->failed()) {
                return response()->json([
                    'message' => 'Gagal mengambil Issues dari GitHub.',
                    'status' => $response->status(),
                    'error' => $response->json(),
                ], 500);
            }

            $pageIssues = collect($response->json());

            /*
             * Tambahkan Issue dari halaman saat ini
             * ke seluruh Issue yang sudah dikumpulkan.
             */
            $allIssues = $allIssues->merge($pageIssues);

            /*
             * Ambil cursor "after" dari Link header
             * untuk request berikutnya.
             */
            $after = null;

            $linkHeader = $response->header('Link');

            if ($linkHeader) {
                if (preg_match(
                    '/<([^>]+)>;\s*rel="next"/',
                    $linkHeader,
                    $matches
                )) {
                    $nextUrl = $matches[1];

                    $parsedUrl = parse_url($nextUrl);

                    if (isset($parsedUrl['query'])) {
                        parse_str(
                            $parsedUrl['query'],
                            $nextQuery
                        );

                        $after = $nextQuery['after'] ?? null;
                    }
                }
            }

        } while ($after);


        /*
         * Mengambil Issue biasa, bukan Pull Request.
         */
        $issues = $allIssues
            ->filter(function ($issue) {
                return !isset($issue['pull_request']);
            })
            ->map(function ($issue) {
                return [
                    'number' => $issue['number'],
                    'title' => $issue['title'],
                    'body' => $issue['body'],
                    'state' => $issue['state'],
                    'url' => $issue['html_url'],
                ];
            })
            ->values();


        /*
         * Menggabungkan title dan body setiap Issue
         * untuk dikirim ke AI Service.
         */
        $issueTexts = $issues
            ->map(function ($issue) {
                return trim(
                    $issue['title'] . "\n" .
                    ($issue['body'] ?? '')
                );
            })
            ->filter()
            ->values()
            ->toArray();


        /*
         * Mengirim SELURUH Issue ke Python AI Service.
         */
        $aiResponse = Http::post(
            env('AI_SERVICE_URL') . '/analyze',
            [
                'issues' => $issueTexts,
            ]
        );

        if ($aiResponse->failed()) {
            return response()->json([
                'message' => 'Gagal melakukan analisis AI.',
                'status' => $aiResponse->status(),
                'error' => $aiResponse->body(),
            ], 500);
        }


        /*
         * Mengembalikan seluruh Issue dan hasil analisis AI.
         */
        return response()->json([
            'repository' => "{$owner}/{$repo}",
            'total_issues' => $issues->count(),
            'issues' => $issues,
            'analysis' => $aiResponse->json(),
        ]);
    }
}