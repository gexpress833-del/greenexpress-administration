<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;
use Illuminate\Http\UploadedFile;

class CloudinaryService
{
    private Cloudinary $cloudinary;

    public function __construct()
    {
        $config = Configuration::instance([
            'cloud' => [
                'cloud_name' => config('services.cloudinary.cloud_name'),
                'api_key' => config('services.cloudinary.api_key'),
                'api_secret' => config('services.cloudinary.api_secret'),
            ],
            'url' => [
                'secure' => true,
            ],
        ]);

        $this->cloudinary = new Cloudinary($config);
    }

    public function upload(UploadedFile $file, string $folder = 'avatars'): string
    {
        $result = $this->cloudinary->uploadApi()->upload(
            $file->getRealPath(),
            [
                'folder' => $folder,
                'public_id' => uniqid(),
                'overwrite' => true,
                'resource_type' => 'image',
            ]
        );

        return $result['secure_url'];
    }

    public function delete(string $url): void
    {
        $publicId = $this->extractPublicId($url);

        if ($publicId) {
            $this->cloudinary->uploadApi()->destroy($publicId);
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

        // Extract folder + public_id
        $folder = basename($dirname);

        return $folder !== '/' && $folder !== '.' ? $folder.'/'.$filename : $filename;
    }
}
