<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$app->make(
    Illuminate\Contracts\Console\Kernel::class
)->bootstrap();

try {
    $response = Illuminate\Support\Facades\Http::withHeaders([
        'User-Agent' => 'FeedbackRise',
        'Accept' => 'application/vnd.github+json',
    ])->get('https://api.github.com');

    echo "Laravel HTTP berhasil!\n";
    echo "HTTP Status: " . $response->status() . "\n";

} catch (\Throwable $e) {
    echo "Laravel HTTP gagal!\n";
    echo "Error: " . $e->getMessage() . "\n";
}
