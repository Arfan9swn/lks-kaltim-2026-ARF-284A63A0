<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ServiceController;

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
});