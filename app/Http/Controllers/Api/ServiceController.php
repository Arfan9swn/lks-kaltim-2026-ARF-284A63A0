<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Models\ServiceType;
use App\Models\Notification;
use App\Services\S3Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ServiceController extends Controller
{
    protected S3Service $s3Service;

    public function __construct(S3Service $s3Service)
    {
        $this->s3Service = $s3Service;
    }

    /*display a listing of all service types.*/
    public function indexServiceTypes(): JsonResponse
    {
        $serviceTypes = ServiceType::all();

        return response()->json([
            'success' => true,
            'message' => 'Daftar layanan berhasil diambil',
            'data' => $serviceTypes
        ], 200);
    }

    public function storeServiceRequest(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'service_type_id' => 'required|exists:service_types,id',
                'description' => 'required|string|max:2000',
                'attachment_url' => 'nullable|url|max:500',
                'attachment' => 'nullable|file|mimes:jpeg,png,jpg,pdf,doc,docx|max:10240' // 10MB max
            ]);

            $attachmentUrl = $validated['attachment_url'] ?? null;

            if ($request->hasFile('attachment')) {
                $attachmentUrl = $this->s3Service->upload($request->file('attachment'), 'attachments');
            }

            $serviceRequest = ServiceRequest::create([
                'user_id' => Auth::id(),
                'service_type_id' => $validated['service_type_id'],
                'description' => $validated['description'],
                'attachment_url' => $attachmentUrl,
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


            //supposedly notifis
            $statusLabels = [
                'pending' => 'Menunggu',
                'processing' => 'Sedang Diproses',
                'done' => 'Selesai',
                'rejected' => 'Ditolak'
            ];

            Notification::create([
                'user_id' => $serviceRequest->user_id,
                'message' => "Permintaan layanan {$serviceRequest->serviceType->name} telah diperbarui menjadi status: {$statusLabels[$validated['status']]}",
                'type' => 'service_request',
                'reference_id' => $serviceRequest->id,
                'reference_type' => 'service_request'
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

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $perPage = $request->get('per_page', 10);
        $serviceRequests = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Daftar semua permintaan layanan',
            'data' => $serviceRequests->items(),
            'pagination' => [
                'total' => $serviceRequests->total(),
                'per_page' => $serviceRequests->perPage(),
                'current_page' => $serviceRequests->currentPage(),
                'last_page' => $serviceRequests->lastPage(),
                'from' => $serviceRequests->firstItem(),
                'to' => $serviceRequests->lastItem()
            ]
        ], 200);
    }
}