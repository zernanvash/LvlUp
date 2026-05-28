<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class CloudinaryStorageService
{
    public function configured(): bool
    {
        $url = $this->cloudinaryUrl();

        return filled($url) && !str_contains((string) $url, 'api_key:api_secret@cloud_name');
    }

    public function uploadPdf(string $contents, string $folder, string $publicId, string $filename): array
    {
        $credentials = $this->credentials();
        $timestamp = time();
        $signature = sha1("folder={$folder}&public_id={$publicId}&timestamp={$timestamp}{$credentials['api_secret']}");

        $response = Http::attach('file', $contents, $filename)
            ->post("https://api.cloudinary.com/v1_1/{$credentials['cloud_name']}/raw/upload", [
                'api_key' => $credentials['api_key'],
                'timestamp' => $timestamp,
                'folder' => $folder,
                'public_id' => $publicId,
                'signature' => $signature,
            ]);

        if ($response->failed()) {
            throw new RuntimeException('Cloudinary PDF upload failed: ' . Str::limit($response->body(), 500));
        }

        return [
            'secure_url' => $response->json('secure_url'),
            'public_id' => $response->json('public_id'),
        ];
    }

    private function cloudinaryUrl(): ?string
    {
        return config('services.cloudinary.url') ?? env('CLOUDINARY_URL');
    }

    private function credentials(): array
    {
        $parsed = parse_url((string) $this->cloudinaryUrl());

        if (!$parsed || empty($parsed['host']) || empty($parsed['user']) || empty($parsed['pass'])) {
            throw new RuntimeException('CLOUDINARY_URL is missing or invalid.');
        }

        return [
            'cloud_name' => $parsed['host'],
            'api_key' => $parsed['user'],
            'api_secret' => $parsed['pass'],
        ];
    }
}
