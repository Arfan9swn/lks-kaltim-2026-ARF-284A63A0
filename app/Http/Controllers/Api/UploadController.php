<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\S3Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UploadController extends Controller
{
    protected S3Service $s3Service;

    public function __construct(S3Service $s3Service)
    {
        $this->s3Service = $s3Service;
    }

    /**
     * Upload an image to S3.
     */
    public function uploadImage(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $image = $request->file('image');
            $url = $this->s3Service->upload($image, 'reports');

            if (!$url) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengupload gambar ke S3'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Gambar berhasil diupload',
                'data' => [
                    'url' => $url
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat upload',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload multiple images to S3.
     */
    public function uploadMultipleImages(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'images' => 'required|array|min:1|max:5',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max each
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $images = $request->file('images');
            $urls = $this->s3Service->uploadMultiple($images, 'reports');

            if (empty($urls)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengupload gambar ke S3'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Gambar berhasil diupload',
                'data' => [
                    'urls' => $urls
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat upload',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete an image from S3.
     */
    public function deleteImage(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required|url|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $url = $request->input('url');
            $deleted = $this->s3Service->delete($url);

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus gambar atau gambar tidak ditemukan'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Gambar berhasil dihapus'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}