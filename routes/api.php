<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\DynamoReportController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\TestS3Controller;
use App\Http\Middleware\AdminMiddleware;

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
    Route::put('/services/request/{id}/status', [ServiceController::class, 'updateServiceRequestStatus'])->middleware(['auth:api', AdminMiddleware::class]);
    Route::get('/services/requests', [ServiceController::class, 'indexAllServiceRequests'])->middleware(['auth:api', AdminMiddleware::class]);

    // Report endpoints (MySQL)
    Route::post('/reports', [ReportController::class, 'store'])->middleware('auth:api');
    Route::get('/reports', [ReportController::class, 'index'])->middleware('auth:api');
    Route::get('/reports/{id}', [ReportController::class, 'show'])->middleware('auth:api');
    Route::put('/reports/{id}', [ReportController::class, 'update'])->middleware('auth:api');

    // DynamoDB Report endpoints
    Route::post('/dynamo-reports', [DynamoReportController::class, 'store'])->middleware('auth:api');
    Route::get('/dynamo-reports', [DynamoReportController::class, 'index'])->middleware('auth:api');
    Route::get('/dynamo-reports/{id}', [DynamoReportController::class, 'show'])->middleware('auth:api');
    Route::put('/dynamo-reports/{id}', [DynamoReportController::class, 'update'])->middleware('auth:api');
    Route::delete('/dynamo-reports/{id}', [DynamoReportController::class, 'destroy'])->middleware('auth:api');

    // Notification endpoints
    Route::get('/notifications', [NotificationController::class, 'index'])->middleware('auth:api');
    Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->middleware('auth:api');
    Route::put('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->middleware('auth:api');

    // Dashboard endpoints (hanya admin)
    Route::get('/dashboard/stats', [DashboardController::class, 'getStats'])->middleware(['auth:api', AdminMiddleware::class]);
    Route::get('/dashboard/reports/summary', [DashboardController::class, 'getReportsSummary'])->middleware(['auth:api', AdminMiddleware::class]);

    // Upload endpoints
    Route::post('/upload/image', [UploadController::class, 'uploadImage'])->middleware('auth:api');
    Route::post('/upload/images', [UploadController::class, 'uploadMultipleImages'])->middleware('auth:api');
    Route::delete('/upload/image', [UploadController::class, 'deleteImage'])->middleware('auth:api');

    // Test S3 connection (admin only)
    Route::get('/test/s3', [TestS3Controller::class, 'testConnection'])->middleware(['auth:api', AdminMiddleware::class]);
});
