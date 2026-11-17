<?php

return [
    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],
    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],
    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'github' => [
        'token' => env('GITHUB_TOKEN'),
        'default_limit' => (int) env('GITHUB_ISSUE_LIMIT', 20),
        'max_issues' => min(500, max(1, (int) env('GITHUB_MAX_ISSUES', 500))),
        'state' => env('GITHUB_ISSUE_STATE', 'open'),
    ],
    'ai' => [
        'url' => env('AI_SERVICE_URL', 'http://127.0.0.1:8001'),
        'timeout' => 240,
    ],
];
