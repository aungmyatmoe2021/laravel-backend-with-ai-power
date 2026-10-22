<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Cloudinary\Cloudinary;
use Exception;
use Illuminate\Support\Facades\Log;

class CloudinaryFileUploadService
{
    public static function upload(UploadedFile $file, $folder = 'products')
    {
        $cloudinary = new Cloudinary(env('CLOUDINARY_URL'));
        
        $result = $cloudinary->uploadApi()->upload($file->getRealPath(), [
            'folder' => $folder,
            'resource_type' => 'auto',
        ]);
        
        return $result['secure_url'];
    }

    public static function destroy($imageUrlOrPublicId)
    {
        try {
            if (empty($imageUrlOrPublicId)) {
                return false;
            }

            $publicId = self::getPublicId($imageUrlOrPublicId);

            $cloudinary = new Cloudinary(env('CLOUDINARY_URL'));
            $result = $cloudinary->uploadApi()->destroy($publicId);

            return ($result['result'] ?? '') === 'ok';
        } catch (Exception $e) {
            Log::error('Cloudinary destroy error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Extract public_id from Cloudinary secure_url or path
     * Example: https://res.cloudinary.com/.../upload/v12345/products/sample.jpg -> products/sample
     */
    public static function getPublicId(string $urlOrPublicId): string
    {
        if (!filter_var($urlOrPublicId, FILTER_VALIDATE_URL)) {
            return preg_replace('/\.[^.]+$/', '', $urlOrPublicId);
        }

        $path = parse_url($urlOrPublicId, PHP_URL_PATH);
        $pathWithoutExt = preg_replace('/\.[^.]+$/', '', $path);
        $uploadPos = strpos($pathWithoutExt, '/upload/');

        if ($uploadPos !== false) {
            $afterUpload = substr($pathWithoutExt, $uploadPos + strlen('/upload/'));
            // Strip transformations and version prefix (e.g. v1729638123/ or w_200/v123/)
            return preg_replace('#^(?:[^/]+/)*v\d+/#', '', $afterUpload);
        }

        return basename($pathWithoutExt);
    }
}