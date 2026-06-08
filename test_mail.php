<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    \Illuminate\Support\Facades\Mail::raw('Test', function($msg) { 
        $msg->to('admin@tabqur.com')->subject('Test'); 
    });
    echo "Sent\n";
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
