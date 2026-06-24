<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        $users = User::all();
        
        return response()->json([
            'success' => true,
            'data' => $users
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email',
                'password' => 'required|string|min:8',
                'password_hash' => 'nullable|string|max:255',
                'role' => 'required|in:admin,citizen',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:1000'
            ]);

            $validated['password'] = Hash::make($validated['password']);
            
            if (empty($validated['password_hash'])) {
                $validated['password_hash'] = $validated['password'];
            } else {
                $validated['password_hash'] = Hash::make($validated['password_hash']);
            }

            $user = User::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Pengguna dibuat.',
                'data' => $user
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error validasi.',
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function show(string $id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Pengguna tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $user
        ], 200);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Pengguna tidak ditemukan.'
            ], 404);
        }

        try {
            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|string|email|max:255|unique:users,email,' . $id,
                'password' => 'sometimes|string|min:8',
                'password_hash' => 'nullable|string|max:255',
                'role' => 'sometimes|in:admin,citizen',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:1000'
            ]);

            if (isset($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            }

            if (isset($validated['password_hash'])) {
                $validated['password_hash'] = Hash::make($validated['password_hash']);
            }

            $user->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Pengguna diperbarui.',
                'data' => $user
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error validasi.',
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Pengguna tidak ditemukan.'
            ], 404);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pengguna dihapus'
        ], 200);
    }
}
