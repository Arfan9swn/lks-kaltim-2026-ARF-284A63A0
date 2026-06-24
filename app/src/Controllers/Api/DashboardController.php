<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Report;
use App\Models\ServiceRequest;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function getStats(): JsonResponse
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya admin yang dapat mengakses dashboard.'
            ], 403);
        }

        // statistika
        $totalUsers = User::count();
        $totalCitizens = User::where('role', 'citizen')->count();
        $totalAdmins = User::where('role', 'admin')->count();
        $newUsersThisMonth = User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // statiska laporan
        $totalReports = Report::count();
        $openReports = Report::where('status', 'open')->count();
        $inProgressReports = Report::where('status', 'in_progress')->count();
        $resolvedReports = Report::where('status', 'resolved')->count();
        $reportsThisMonth = Report::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // statistik layanan
        $totalServiceRequests = ServiceRequest::count();
        $pendingServiceRequests = ServiceRequest::where('status', 'pending')->count();
        $processingServiceRequests = ServiceRequest::where('status', 'processing')->count();
        $doneServiceRequests = ServiceRequest::where('status', 'done')->count();
        $rejectedServiceRequests = ServiceRequest::where('status', 'rejected')->count();
        $serviceRequestsThisMonth = ServiceRequest::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // statisika notif
        $totalNotifications = Notification::count();
        $unreadNotifications = Notification::where('is_read', false)->count();

        return response()->json([
            'success' => true,
            'message' => 'Statistik dashboard berhasil diambil',
            'data' => [
                'users' => [
                    'total' => $totalUsers,
                    'citizens' => $totalCitizens,
                    'admins' => $totalAdmins,
                    'new_this_month' => $newUsersThisMonth
                ],
                'reports' => [
                    'total' => $totalReports,
                    'open' => $openReports,
                    'in_progress' => $inProgressReports,
                    'resolved' => $resolvedReports,
                    'this_month' => $reportsThisMonth
                ],
                'service_requests' => [
                    'total' => $totalServiceRequests,
                    'pending' => $pendingServiceRequests,
                    'processing' => $processingServiceRequests,
                    'done' => $doneServiceRequests,
                    'rejected' => $rejectedServiceRequests,
                    'this_month' => $serviceRequestsThisMonth
                ],
                'notifications' => [
                    'total' => $totalNotifications,
                    'unread' => $unreadNotifications
                ]
            ]
        ], 200);
    }

    public function getReportsSummary(): JsonResponse
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya admin yang dapat mengakses dashboard.'
            ], 403);
        }

        $summary = Report::select('category', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->groupBy('category')
            ->get()
            ->map(function ($item) {
                $categoryLabels = [
                    'infrastructure' => 'Infrastruktur',
                    'environment' => 'Lingkungan',
                    'social' => 'Sosial',
                    'other' => 'Lainnya'
                ];

                return [
                    'category' => $item->category,
                    'category_label' => $categoryLabels[$item->category] ?? $item->category,
                    'total' => $item->total
                ];
            });

        $detailedSummary = [];
        $categories = ['infrastructure', 'environment', 'social', 'other'];
        $statusLabels = [
            'open' => 'Terbuka',
            'in_progress' => 'Sedang Diproses',
            'resolved' => 'Selesai'
        ];

        foreach ($categories as $category) {
            $categoryData = [
                'category' => $category,
                'category_label' => $categoryLabels[$category] ?? $category,
                'total' => 0,
                'status_breakdown' => []
            ];

            foreach (['open', 'in_progress', 'resolved'] as $status) {
                $count = Report::where('category', $category)
                    ->where('status', $status)
                    ->count();
                
                $categoryData['status_breakdown'][] = [
                    'status' => $status,
                    'status_label' => $statusLabels[$status],
                    'count' => $count
                ];
                
                $categoryData['total'] += $count;
            }

            $detailedSummary[] = $categoryData;
        }

        return response()->json([
            'success' => true,
            'message' => 'Rekapitulasi laporan per kategori berhasil diambil',
            'data' => [
                'summary' => $summary,
                'detailed' => $detailedSummary,
                'total_reports' => Report::count()
            ]
        ], 200);
    }
}