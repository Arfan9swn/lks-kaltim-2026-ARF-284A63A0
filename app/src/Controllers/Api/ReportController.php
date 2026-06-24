<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ReportController extends Controller
{
    /**
     * Store a new report.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'category' => 'required|in:infrastructure,environment,social,other',
                'title' => 'required|string|max:255',
                'description' => 'required|string|max:2000',
                'location' => 'required|string|max:500',
                'image_url' => 'nullable|url|max:500'
            ]);

            $report = Report::create([
                'user_id' => Auth::id(),
                'category' => $validated['category'],
                'title' => $validated['title'],
                'description' => $validated['description'],
                'location' => $validated['location'],
                'image_url' => $validated['image_url'] ?? null,
            ]);

            $report->load('user');

            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil dikirim',
                'data' => $report
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
     * Display a listing of reports.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $query = Report::with('user');

        // If not admin, only show user's own reports
        if ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        }

        // Filter by category if provided
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $perPage = $request->get('per_page', 10);
        $reports = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Daftar laporan berhasil diambil',
            'data' => $reports->items(),
            'pagination' => [
                'total' => $reports->total(),
                'per_page' => $reports->perPage(),
                'current_page' => $reports->currentPage(),
                'last_page' => $reports->lastPage(),
                'from' => $reports->firstItem(),
                'to' => $reports->lastItem()
            ]
        ], 200);
    }

    /**
     * Display the specified report.
     */
    public function show(string $id): JsonResponse
    {
        $user = Auth::user();
        
        $query = Report::with('user');
        
        // If not admin, only allow access to own reports
        if ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        }

        $report = $query->find($id);

        if (!$report) {
            return response()->json([
                'success' => false,
                'message' => 'Laporan tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail laporan',
            'data' => $report
        ], 200);
    }

    /**
     * Update the specified report.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $user = Auth::user();
        
        $query = Report::where('user_id', $user->id);
        
        // If not admin, only allow update own reports
        if ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        }

        $report = $query->find($id);

        if (!$report) {
            return response()->json([
                'success' => false,
                'message' => 'Laporan tidak ditemukan'
            ], 404);
        }

        try {
            $validated = $request->validate([
                'category' => 'sometimes|in:infrastructure,environment,social,other',
                'title' => 'sometimes|string|max:255',
                'description' => 'sometimes|string|max:2000',
                'location' => 'sometimes|string|max:500',
                'image_url' => 'nullable|url|max:500',
                'status' => 'sometimes|in:open,in_progress,resolved'
            ]);

            $report->update($validated);
            $report->load('user');

            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil diperbarui',
                'data' => $report
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        }
    }
}