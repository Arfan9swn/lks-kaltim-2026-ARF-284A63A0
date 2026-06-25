<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return response()->json([
        'message' => 'This is an API application. Please use /api/v1/auth/login endpoint.',
        'login_endpoint' => url('/api/v1/auth/login')
    ]);
})->name('login');
