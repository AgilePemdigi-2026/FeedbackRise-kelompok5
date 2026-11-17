<?php

require __DIR__ . '/vendor/autoload.php';

$client = new \GuzzleHttp\Client([
    'timeout' => 15,
]);

try {
    $response = $client->get('https://api.github.com', [
        'headers' => [
            'User-Agent' => 'FeedbackRise',
            'Accept' => 'application/vnd.github+json',
        ],
    ]);

    echo "Guzzle berhasil terhubung!\n";
    echo "HTTP Status: " . $response->getStatusCode() . "\n";

} catch (\GuzzleHttp\Exception\GuzzleException $e) {
    echo "Guzzle gagal terhubung!\n";
    echo "Error: " . $e->getMessage() . "\n";
}
