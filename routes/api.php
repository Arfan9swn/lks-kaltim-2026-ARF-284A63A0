<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\NotificationController;

Route::prefix('v1')->group(function () {
    // Auth endpoints
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('auth:api');
    Route::get('/auth/profile', [AuthController::class, 'profile'])->middleware('auth:api');
    Route::post('/auth/refresh', [AuthController::class, 'refresh'])->middleware('auth:api');

    // Service endpoints
    Route::get('/services', [ServiceController::class, 'indexServiceTypes']);
    Route::post('/services/request', [ServiceController::class, 'storeServiceRequest'])->middleware('auth:api');
    Route::get('/services/request/{id}', [ServiceController::class, 'showServiceRequest'])->middleware('auth:api');
    Route::put('/services/request/{id}/status', [ServiceController::class, 'updateServiceRequestStatus'])->middleware('auth:api');
    Route::get('/services/requests', [ServiceController::class, 'indexAllServiceRequests'])->middleware('auth:api');

    // Report endpoints
    Route::post('/reports', [ReportController::class, 'store'])->middleware('auth:api');
    Route::get('/reports', [ReportController::class, 'index'])->middleware('auth:api');
    Route::get('/reports/{id}', [ReportController::class, 'show'])->middleware('auth:api');
    Route::put('/reports/{id}', [ReportController::class, 'update'])->middleware('auth:api');

    // Notification endpoints
    Route::get('/notifications', [NotificationController::class, 'index'])->middleware('auth:api');
    Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->middleware('auth:api');
    Route::put('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->middleware('auth:api');
});