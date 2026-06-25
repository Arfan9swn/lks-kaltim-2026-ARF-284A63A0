<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /*handle req*/
    public function handle(Request $request, Closure $next): mixed
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya admin yang dapat mengakses resource ini.'
            ], 403);
        }

        return $next($request);
    }
}
