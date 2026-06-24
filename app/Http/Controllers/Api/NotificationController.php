<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of user's notifications.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        $query = Notification::where('user_id', $user->id);

        // Filter by read status if provided
        if ($request->has('is_read')) {
            $query->where('is_read', $request->boolean('is_read'));
        }

        // Filter by type if provided
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $notifications = $query->orderBy('created_at', 'desc')->get();

        // Count unread notifications
        $unreadCount = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'success' => true,
            'message' => 'Daftar notifikasi berhasil diambil',
            'unread_count' => $unreadCount,
            'data' => $notifications
        ], 200);
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(string $id): JsonResponse
    {
        $user = Auth::user();
        
        $notification = Notification::where('user_id', $user->id)
            ->where('id', $id)
            ->first();

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notifikasi tidak ditemukan'
            ], 404);
        }

        $notification->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi berhasil ditandai sebagai dibaca',
            'data' => $notification
        ], 200);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): JsonResponse
    {
        $user = Auth::user();
        
        Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Semua notifikasi berhasil ditandai sebagai dibaca'
        ], 200);
    }
}