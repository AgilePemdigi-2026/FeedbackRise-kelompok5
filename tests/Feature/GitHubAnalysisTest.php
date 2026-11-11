<?php

namespace Tests\Feature;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GitHubAnalysisTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'services.github.token' => 'test-token',
            'services.github.default_limit' => 20,
            'services.github.max_issues' => 500,
            'services.ai.url' => 'http://ai.test',
        ]);
    }

    public function test_token_pagination_and_total_limit_exclude_pull_requests()
    {
        Http::fake(function (Request $request) {
            if (str_starts_with($request->url(), 'https://api.github.com/')) {
                $this->assertTrue($request->hasHeader('Authorization', 'Bearer test-token'));
                if ((int) $request['page'] === 1) {
                    return Http::response([
                        ['pull_request' => [], 'number' => 90],
                        ['number' => 7, 'title' => 'Mode gelap', 'body' => '', 'state' => 'open', 'html_url' => 'https://github.com/test/repo/issues/7'],
                    ], 200, ['Link' => '<https://api.github.com/repos/test/repo/issues?page=2>; rel="next"']);
                }
                return Http::response([
                    ['number' => 8, 'title' => 'Dark mode', 'body' => '', 'state' => 'open', 'html_url' => 'https://github.com/test/repo/issues/8'],
                    ['number' => 9, 'title' => 'Other', 'body' => '', 'state' => 'open', 'html_url' => 'https://github.com/test/repo/issues/9'],
                ]);
            }
            $this->assertSame('http://ai.test/analyze', $request->url());
            $this->assertSame([7, 8], array_column($request['issues'], 'number'));
            return Http::response(['overall_summary' => 'Example', 'recommendations' => []]);
        });

        $this->postJson('/api/github/issues', [
            'issue_url' => 'https://github.com/test/repo/issues', 'max_issues' => 2,
        ])->assertOk()->assertJsonPath('total_issues', 2)->assertJsonPath('github_pages', 2);
        Http::assertSentCount(3);
    }

    public function test_limit_above_server_maximum_is_rejected_before_network_request()
    {
        Http::fake();
        $this->postJson('/api/github/issues', [
            'issue_url' => 'https://github.com/test/repo/issues', 'max_issues' => 501,
        ])->assertStatus(422)->assertJsonValidationErrors('max_issues');
        Http::assertNothingSent();
    }

    public function test_missing_token_produces_actionable_error()
    {
        config(['services.github.token' => '']);
        Http::fake();
        $this->postJson('/api/github/issues', [
            'issue_url' => 'https://github.com/test/repo/issues',
        ])->assertStatus(503);
        Http::assertNothingSent();
    }

    public function test_github_quota_error_does_not_call_ai()
    {
        Http::fake(['*' => Http::response(['message' => 'rate limit'], 403, ['X-RateLimit-Remaining' => '0'])]);
        $this->postJson('/api/github/issues', [
            'issue_url' => 'https://github.com/test/repo/issues',
        ])->assertStatus(429)->assertJsonPath('service', 'github');
        Http::assertSentCount(1);
    }

    public function test_no_issues_does_not_call_ai()
    {
        Http::fake(['*' => Http::response([])]);
        $this->postJson('/api/github/issues', [
            'issue_url' => 'https://github.com/test/repo/issues',
        ])->assertStatus(422);
        Http::assertSentCount(1);
    }
}
