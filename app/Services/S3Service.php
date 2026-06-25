<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class S3Service
{
    /**
     * Upload a file to S3.
     *
     * @param UploadedFile $file
     * @param string $directory
     * @return string|null
     */
    public function upload(UploadedFile $file, string $directory = 'images'): ?string
    {
        try {
            // Validate file
            if (!$file->isValid()) {
                \Log::error('S3 Upload Error: File is not valid');
                return null;
            }

            $filename = $this->generateFilename($file->getClientOriginalExtension());
            $path = $directory . '/' . $filename;
            
            \Log::info('Attempting to upload to S3', [
                'path' => $path,
                'size' => $file->getSize(),
                'mime' => $file->getMimeType()
            ]);
            
            // Use Storage facade directly
            $result = Storage::disk('s3')->put($path, file_get_contents($file->getRealPath()), 'public');
            
            \Log::info('S3 Upload Result', ['result' => $result]);
            
            if ($result) {
                $url = Storage::disk('s3')->url($path);
                \Log::info('S3 Upload Success', ['url' => $url]);
                return $url;
            }
            
            \Log::error('S3 Upload Failed: put() returned false');
            return null;
        } catch (\Exception $e) {
            \Log::error('S3 Upload Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Upload multiple files to S3.
     *
     * @param array $files
     * @param string $directory
     * @return array
     */
    public function uploadMultiple(array $files, string $directory = 'images'): array
    {
        $urls = [];
        
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $url = $this->upload($file, $directory);
                if ($url) {
                    $urls[] = $url;
                }
            }
        }
        
        return $urls;
    }

    /**
     * Delete a file from S3.
     *
     * @param string $url
     * @return bool
     */
    public function delete(string $url): bool
    {
        try {
            $path = $this->extractPathFromUrl($url);
            
            if ($path && Storage::disk('s3')->exists($path)) {
                return Storage::disk('s3')->delete($path);
            }
            
            return false;
        } catch (\Exception $e) {
            \Log::error('S3 Delete Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate a unique filename.
     *
     * @param string $extension
     * @return string
     */
    private function generateFilename(string $extension): string
    {
        return Str::random(40) . '.' . $extension;
    }

    /**
     * Extract file path from S3 URL.
     *
     * @param string $url
     * @return string|null
     */
    private function extractPathFromUrl(string $url): ?string
    {
        $bucket = config('filesystems.disks.s3.bucket');
        $region = config('filesystems.disks.s3.region');
        
        // Handle different S3 URL formats
        $patterns = [
            // https://bucket.s3.region.amazonaws.com/path/to/file
            '#https://' . $bucket . '\.s3\.' . $region . '\.amazonaws\.com/(.+)#',
            // https://s3.region.amazonaws.com/bucket/path/to/file
            '#https://s3\.' . $region . '\.amazonaws\.com/' . $bucket . '/(.+)#',
            // Custom domain URLs
            '#https://[^/]+/(.+)#',
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return $matches[1];
            }
        }
        
        return null;
    }
}
