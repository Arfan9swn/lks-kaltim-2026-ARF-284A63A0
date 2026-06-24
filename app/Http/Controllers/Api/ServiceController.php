<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Models\ServiceType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ServiceController extends Controller
{
    /**
     * Display a listing of all service types.
     */
    public function indexServiceTypes(): JsonResponse
    {
        $serviceTypes = ServiceType::all();

        return response()->json([
            'success' => true,
            'message' => 'Daftar layanan berhasil diambil',
            'data' => $serviceTypes
        ], 200);
    }

    /**
     * Store a new service request.
     */
    public function storeServiceRequest(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'service_type_id' => 'required|exists:service_types,id',
                'description' => 'required|string|max:2000',
                'attachment_url' => 'nullable|url|max:500'
            ]);

            $serviceRequest = ServiceRequest::create([
                'user_id' => Auth::id(),
                'service_type_id' => $validated['service_type_id'],
                'description' => $validated['description'],
                'attachment_url' => $validated['attachment_url'] ?? null,
            ]);

            $serviceRequest->load(['serviceType', 'user']);

            return response()->json([
                'success' => true,
                'message' => 'Permintaan layanan berhasil dibuat',
                'data' => $serviceRequest
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Display the specified service request.
     */
    public function showServiceRequest(string $id): JsonResponse
    {
        $serviceRequest = ServiceRequest::with(['serviceType', 'user'])
            ->where('user_id', Auth::id())
            ->find($id);

        if (!$serviceRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Permintaan layanan tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail permintaan layanan',
            'data' => $serviceRequest
        ], 200);
    }

    /**
     * Update the status of a service request (admin only).
     */
    public function updateServiceRequestStatus(Request $request, string $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya admin yang dapat mengubah status.'
            ], 403);
        }

        $serviceRequest = ServiceRequest::find($id);

        if (!$serviceRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Permintaan layanan tidak ditemukan'
            ], 404);
        }

        try {
            $validated = $request->validate([
                'status' => 'required|in:pending,processing,done,rejected'
            ]);

            $serviceRequest->update([
                'status' => $validated['status']
            ]);

            $serviceRequest->load(['serviceType', 'user']);

            return response()->json([
                'success' => true,
                'message' => 'Status permintaan layanan berhasil diperbarui',
                'data' => $serviceRequest
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Display all service requests (admin only).
     */
    public function indexAllServiceRequests(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya admin yang dapat melihat semua permintaan.'
            ], 403);
        }

        $query = ServiceRequest::with(['serviceType', 'user']);

        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by user_id if provided
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $serviceRequests = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar semua permintaan layanan',
            'data' => $serviceRequests
        ], 200);
    }
}