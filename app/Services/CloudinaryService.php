<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class CloudinaryService
{
    private ?Cloudinary $cloudinary = null;

    public function __construct()
    {
        $cloudName = config('services.cloudinary.cloud_name');
        $apiKey = config('services.cloudinary.api_key');
        $apiSecret = config('services.cloudinary.api_secret');

        if (empty($cloudName) || empty($apiKey) || empty($apiSecret)) {
            Log::warning('Cloudinary credentials are missing. Avatar upload will be skipped.');

            return;
        }

        try {
            $this->cloudinary = new Cloudinary([
                'cloud_name' => $cloudName,
                'api_key' => $apiKey,
                'api_secret' => $apiSecret,
                'secure' => true,
            ]);
        } catch (\Exception $e) {
            Log::error('Cloudinary initialization failed: '.$e->getMessage());
            $this->cloudinary = null;
        }
    }

    public function upload(UploadedFile $file, string $folder = 'avatars'): ?string
    {
        if ($this->cloudinary === null) {
            Log::warning('Cloudinary is not configured. Skipping avatar upload.');

            return null;
        }

        try {
            $result = $this->cloudinary->uploadApi()->upload(
                $file->getRealPath(),
                [
                    'folder' => $folder,
                    'public_id' => uniqid(),
                    'overwrite' => true,
                    'resource_type' => 'image',
                ]
            );

            return $result['secure_url'] ?? null;
        } catch (\Exception $e) {
            Log::error('Cloudinary upload failed: '.$e->getMessage());

            return null;
        }
    }

    public function delete(string $url): void
    {
        if ($this->cloudinary === null || empty($url)) {
            return;
        }

        $publicId = $this->extractPublicId($url);

        if ($publicId) {
            try {
                $this->cloudinary->uploadApi()->destroy($publicId);
            } catch (\Exception $e) {
                Log::error('Cloudinary delete failed: '.$e->getMessage());
            }
        }
    }

    private function extractPublicId(string $url): ?string
    {
        $parsed = parse_url($url);

        if (! isset($parsed['path'])) {
            return null;
        }

        $path = $parsed['path'];
        $filename = pathinfo($path, PATHINFO_FILENAME);
        $dirname = pathinfo($path, PATHINFO_DIRNAME);
        $folder = basename($dirname);

        return $folder !== '/' && $folder !== '.' && $folder !== '' ? $folder.'/'.$filename : $filename;
    }
}
