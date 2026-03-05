<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;
use Illuminate\Http\UploadedFile;

class CloudinaryService
{
    protected Cloudinary $cloudinary;

    public function __construct()
    {
        Configuration::instance([
            'cloud' => [
                'cloud_name' => config('cloudinary.cloud_name'),
                'api_key'    => config('cloudinary.api_key'),
                'api_secret' => config('cloudinary.api_secret'),
            ],
            'url' => [
                'secure' => true,
            ],
        ]);

        $this->cloudinary = new Cloudinary();
    }

    /**
     * Upload a profile photo.
     * Returns the secure Cloudinary URL.
     */
    public function uploadProfilePhoto(UploadedFile $file, int $userId): string
    {
        $result = $this->cloudinary->uploadApi()->upload(
            $file->getRealPath(),
            [
                'folder'         => 'lvlup/profiles',
                'public_id'      => 'user_' . $userId,
                'overwrite'      => true,
                'transformation' => [
                    'width'   => 400,
                    'height'  => 400,
                    'crop'    => 'fill',
                    'gravity' => 'face',
                    'quality' => 'auto',
                    'fetch_format' => 'auto',
                ],
            ]
        );

        return $result['secure_url'];
    }

    /**
     * Upload a project thumbnail.
     * Returns the secure Cloudinary URL.
     */
    public function uploadProjectThumbnail(UploadedFile $file, int $projectId): string
    {
        $result = $this->cloudinary->uploadApi()->upload(
            $file->getRealPath(),
            [
                'folder'         => 'lvlup/projects',
                'public_id'      => 'project_' . $projectId,
                'overwrite'      => true,
                'transformation' => [
                    'width'   => 800,
                    'height'  => 450,
                    'crop'    => 'fill',
                    'quality' => 'auto',
                    'fetch_format' => 'auto',
                ],
            ]
        );

        return $result['secure_url'];
    }

    /**
     * Upload a resume PDF.
     * Returns the secure Cloudinary URL.
     */
    public function uploadResumePdf(string $pdfContent, int $resumeId, string $jobTitle): string
    {
        // Write to a temp file so Cloudinary can read it
        $tempPath = sys_get_temp_dir() . '/resume_' . $resumeId . '.pdf';
        file_put_contents($tempPath, $pdfContent);

        $result = $this->cloudinary->uploadApi()->upload(
            $tempPath,
            [
                'folder'        => 'lvlup/resumes',
                'public_id'     => 'resume_' . $resumeId . '_' . str()->slug($jobTitle),
                'overwrite'     => true,
                'resource_type' => 'raw',  // required for PDFs
            ]
        );

        // Clean up temp file
        @unlink($tempPath);

        return $result['secure_url'];
    }

    /**
     * Upload a certificate (image or PDF).
     * Returns the secure Cloudinary URL.
     */
    public function uploadCertificate(UploadedFile $file, int $userId, string $name): string
    {
        $isPdf = $file->getMimeType() === 'application/pdf';

        $result = $this->cloudinary->uploadApi()->upload(
            $file->getRealPath(),
            [
                'folder'        => 'lvlup/certificates',
                'public_id'     => 'cert_' . $userId . '_' . str()->slug($name) . '_' . time(),
                'overwrite'     => false,
                'resource_type' => $isPdf ? 'raw' : 'image',
                'transformation' => $isPdf ? null : [
                    [
                        'quality' => 'auto',
                        'fetch_format' => 'auto',
                    ]
                ],
            ]
        );

        return $result['secure_url'];
    }

    /**
     * Delete a file from Cloudinary by its URL.
     */
    public function deleteByUrl(string $url, string $resourceType = 'image'): void
    {
        try {
            // Extract public_id from URL
            // e.g. https://res.cloudinary.com/cloud/image/upload/v123/lvlup/profiles/user_1.jpg
            $pattern = '/\/upload\/(?:v\d+\/)?(.+?)(?:\.\w+)?$/';
            if (preg_match($pattern, $url, $matches)) {
                $publicId = $matches[1];
                $this->cloudinary->uploadApi()->destroy($publicId, [
                    'resource_type' => $resourceType,
                ]);
            }
        } catch (\Throwable $e) {
            // Non-fatal — log but don't crash
            \Log::warning('Cloudinary delete failed: ' . $e->getMessage());
        }
    }
}
