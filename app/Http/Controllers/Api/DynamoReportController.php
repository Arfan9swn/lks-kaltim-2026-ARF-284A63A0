<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DynamoReport;
use App\Services\S3Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class DynamoReportController extends Controller
{
    protected S3Service $s3Service;

    public function __construct(S3Service $s3Service)
    {
        $this->s3Service = $s3Service;
    }

    /**
     * Store a new report in DynamoDB.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'category' => 'required|array|min:1',
                'category.*' => 'in:infrastructure,environment,social,other',
                'title' => 'required|string|max:255',
                'description' => 'required|string|max:2000',
                'location' => 'required|string|max:500',
                'image_url' => 'nullable|url|max:500',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
                'status' => 'required|array|min:1',
                'status.*' => 'in:open,in_progress,resolved'
            ]);

            $imageUrl = $validated['image_url'] ?? '';

            // If image file is uploaded, upload to S3
            if ($request->hasFile('image')) {
                $imageUrl = $this->s3Service->upload($request->file('image'), 'reports') ?? '';
            }

            $dynamoReport = new DynamoReport();
            
            $result = $dynamoReport->create([
                'id' => (string) uniqid(),
                'user_id' => (string) Auth::id(),
                'category' => $validated['category'],
                'title' => $validated['title'],
                'description' => $validated['description'],
                'location' => $validated['location'],
                'image_url' => $imageUrl,
                'status' => $validated['status'],
            ]);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan laporan',
                    'error' => $result['error']
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil dikirim',
                'data' => $result['data']
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
     * Display a listing of reports from DynamoDB.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $dynamoReport = new DynamoReport();
        
        $reports = $dynamoReport->getAll();

        if ($user->role !== 'admin') {
            $reports = array_filter($reports, function ($report) use ($user) {
                return $report['user_id'] == $user->id;
            });
        }

        if ($request->has('category')) {
            $reports = array_filter($reports, function ($report) use ($request) {
                return in_array($request->category, (array) $report['category']);
            });
        }

        if ($request->has('status')) {
            $reports = array_filter($reports, function ($report) use ($request) {
                return in_array($request->status, (array) $report['status']);
            });
        }

        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);
        $total = count($reports);
        $lastPage = ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;
        $items = array_slice($reports, $offset, $perPage);

        return response()->json([
            'success' => true,
            'message' => 'Daftar laporan berhasil diambil',
            'data' => array_values($items),
            'pagination' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => (int) $page,
                'last_page' => (int) $lastPage,
                'from' => $total > 0 ? $offset + 1 : 0,
                'to' => min($offset + $perPage, $total)
            ]
        ], 200);
    }

    public function show(string $id): JsonResponse
    {
        $user = Auth::user();
        $dynamoReport = new DynamoReport();
        
        $report = $dynamoReport->find($id);

        if (!$report) {
            return response()->json([
                'success' => false,
                'message' => 'Laporan tidak ditemukan'
            ], 404);
        }

        if ($user->role !== 'admin' && $report['user_id'] != $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail laporan',
            'data' => $report
        ], 200);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $user = Auth::user();
        $dynamoReport = new DynamoReport();
        
        $report = $dynamoReport->find($id);

        if (!$report) {
            return response()->json([
                'success' => false,
                'message' => 'Laporan tidak ditemukan'
            ], 404);
        }

        if ($user->role !== 'admin' && $report['user_id'] != $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak'
            ], 403);
        }

        try {
            $validated = $request->validate([
                'category' => 'sometimes|array|min:1',
                'category.*' => 'in:infrastructure,environment,social,other',
                'title' => 'sometimes|string|max:255',
                'description' => 'sometimes|string|max:2000',
                'location' => 'sometimes|string|max:500',
                'image_url' => 'nullable|url|max:500',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
                'status' => 'sometimes|array|min:1',
                'status.*' => 'in:open,in_progress,resolved'
            ]);

            $updateData = $validated;

            // If new image file is uploaded, upload to S3
            if ($request->hasFile('image')) {
                // Get current report to delete old image
                $currentReport = $dynamoReport->find($id);
                if ($currentReport && !empty($currentReport['image_url'])) {
                    $this->s3Service->delete($currentReport['image_url']);
                }
                $updateData['image_url'] = $this->s3Service->upload($request->file('image'), 'reports') ?? '';
            }

            $result = $dynamoReport->update($id, $updateData);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui laporan',
                    'error' => $result['error']
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil diperbarui',
                'data' => $result['data']
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        $user = Auth::user();
        $dynamoReport = new DynamoReport();
        
        $report = $dynamoReport->find($id);

        if (!$report) {
            return response()->json([
                'success' => false,
                'message' => 'Laporan tidak ditemukan'
            ], 404);
        }

        if ($user->role !== 'admin' && $report['user_id'] != $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak'
            ], 403);
        }

        $result = $dynamoReport->delete($id);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus laporan',
                'error' => $result['error']
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Laporan berhasil dihapus'
        ], 200);
    }
}
