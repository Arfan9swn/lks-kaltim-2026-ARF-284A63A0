<?php

namespace App\Http\Middleware;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    protected function successResponse(string $message, $data = null, int $statusCode = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);
    }

    protected function errorResponse(string $message, int $statusCode = 400, $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }

    protected function validationErrorResponse($errors): JsonResponse
    {
        return $this->errorResponse('Validasi gagal', 422, $errors);
    }

    protected function notFoundResponse(string $message = 'Data tidak ditemukan'): JsonResponse
    {
        return $this->errorResponse($message, 404);
    }

    protected function unauthorizedResponse(string $message = 'Tidak terautentikasi'): JsonResponse
    {
        return $this->errorResponse($message, 401);
    }

    protected function forbiddenResponse(string $message = 'Akses ditolak'): JsonResponse
    {
        return $this->errorResponse($message, 403);
    }

    protected function paginatedResponse(string $message, $data, int $total, int $perPage, int $currentPage, int $lastPage): JsonResponse
    {
        return $this->successResponse($message, [
            'items' => $data,
            'pagination' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $currentPage,
                'last_page' => $lastPage,
                'from' => ($currentPage - 1) * $perPage + 1,
                'to' => min($currentPage * $perPage, $total)
            ]
        ]);
    }
}
