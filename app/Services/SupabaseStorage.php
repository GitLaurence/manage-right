<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class SupabaseStorage
{
    private string $baseUrl;
    private string $serviceRoleKey;
    private string $bucket = 'selfies';

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.supabase.url'), '/');
        $this->serviceRoleKey = config('services.supabase.service_role_key', '');
    }

    public function upload(string $path, string $fileContents, string $mimeType = 'image/jpeg'): string
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->serviceRoleKey}",
            'Content-Type' => $mimeType,
            'x-upsert' => 'true',
        ])->withBody($fileContents, $mimeType)
            ->post("{$this->baseUrl}/storage/v1/object/{$this->bucket}/{$path}");

        if ($response->failed()) {
            throw new RuntimeException("Supabase Storage upload failed: {$response->body()}");
        }

        return $path;
    }

    public function signedUrl(string $path, int $expiresIn = 3600): string
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->serviceRoleKey}",
        ])->post("{$this->baseUrl}/storage/v1/object/sign/{$this->bucket}/{$path}", [
            'expiresIn' => $expiresIn,
        ]);

        if ($response->failed()) {
            return '';
        }

        return "{$this->baseUrl}/storage/v1{$response->json('signedURL')}";
    }

    public function ensureBucketExists(): void
    {
        Http::withHeaders([
            'Authorization' => "Bearer {$this->serviceRoleKey}",
        ])->post("{$this->baseUrl}/storage/v1/bucket", [
            'id' => $this->bucket,
            'name' => $this->bucket,
            'public' => false,
        ]);
    }
}
