<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$token = env('FONNTE_TOKEN');
$target = env('ADMIN_PHONE'); // use admin phone for testing
$message = 'Test from script';

echo "Sending to $target with token $token\n";

$response = \Illuminate\Support\Facades\Http::withoutVerifying()->withHeaders([
    'Authorization' => $token,
])->post('https://api.fonnte.com/send', [
    'target' => $target,
    'message' => $message,
    'countryCode' => '62',
]);

echo "Status: " . $response->status() . "\n";
echo "Body: " . $response->body() . "\n";
