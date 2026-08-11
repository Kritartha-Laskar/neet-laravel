<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthApiController extends Controller
{
    // ──────────────────────────────────────────────────────────────
    // POST /api/auth/register
    // ──────────────────────────────────────────────────────────────
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:191',
            'user_name' => 'required|string|max:191|unique:users,user_name',
            'gmail'     => 'required|email|max:191|unique:users,gmail',
            'phone_no'  => 'nullable|string|max:20',
            'password'  => 'required|string|min:6|confirmed',
        ]);

        $token = Str::random(60);

        $user = User::create([
            'name'      => $validated['name'],
            'user_name' => $validated['user_name'],
            'gmail'     => $validated['gmail'],
            'phone_no'  => $validated['phone_no'] ?? null,
            'password'  => Hash::make($validated['password']),
            'user_type' => 'user',
            'status'    => 'active',
            'api_token' => $token,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Registration successful.',
            'data'    => [
                'user'  => [
                    'id'        => $user->id,
                    'name'      => $user->name,
                    'user_name' => $user->user_name,
                    'gmail'     => $user->gmail,
                    'phone_no'  => $user->phone_no,
                    'user_type' => $user->user_type,
                ],
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ], 201);
    }

    // ──────────────────────────────────────────────────────────────
    // POST /api/auth/login
    // ──────────────────────────────────────────────────────────────
    public function login(Request $request)
    {
        $request->validate([
            'user_name' => 'required|string',
            'password'  => 'required|string',
        ]);

        $user = User::where('user_name', $request->user_name)
                    ->whereNull('deleted_at')
                    ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid username or password.',
            ], 401);
        }

        if ($user->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Your account is inactive. Please contact admin.',
            ], 403);
        }

        // Generate and save a new API token
        $token = Str::random(60);
        $user->update(['api_token' => $token]);

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'data'    => [
                'user'  => [
                    'id'        => $user->id,
                    'name'      => $user->name,
                    'user_name' => $user->user_name,
                    'gmail'     => $user->gmail,
                    'phone_no'  => $user->phone_no,
                    'user_type' => $user->user_type,
                ],
                'token'      => $token,
                'token_type' => 'Bearer',
            ],
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    // POST /api/auth/logout   (requires Bearer token)
    // ──────────────────────────────────────────────────────────────
    public function logout(Request $request)
    {
        // Clear the API token
        $request->user()->update(['api_token' => null]);

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.',
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    // GET /api/auth/me   (requires Bearer token)
    // ──────────────────────────────────────────────────────────────
    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'data'    => $request->user(),
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    // POST /api/auth/change-password   (requires Bearer token)
    // ──────────────────────────────────────────────────────────────
    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password'     => 'required|string|min:6|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'The current password you entered is incorrect.',
            ], 400);
        }

        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully.',
        ]);
    }
}
