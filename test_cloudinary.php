<?php
use Illuminate\Support\Facades\Http;

$file = tempnam(sys_get_temp_dir(), 'test');
file_put_contents($file, 'dummy image content');

$cloudinaryUrl = config('services.cloudinary.url') ?? env('CLOUDINARY_URL');
$parsed    = parse_url($cloudinaryUrl);
$cloudName = $parsed['host'];
$apiKey    = $parsed['user'];
$apiSecret = $parsed['pass'];

$endpoint = "https://api.cloudinary.com/v1_1/{$cloudName}/image/upload";

$timestamp = time();
$signature = sha1("folder=projects&timestamp={$timestamp}{$apiSecret}");

$response = Http::attach(
    'file',
    file_get_contents($file),
    'test.png'
)->post($endpoint, [
    'api_key'   => $apiKey,
    'timestamp' => $timestamp,
    'signature' => $signature,
    'folder'    => 'projects',
]);

echo "Status: " . $response->status() . "\n";
echo "Response: " . $response->body() . "\n";
unlink($file);
