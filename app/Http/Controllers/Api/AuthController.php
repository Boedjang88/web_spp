<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Resources\Api\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends BaseApiController
{
    /**
     * User Login & generate Sanctum Token
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->sendError('Kredensial login tidak valid.', [
                'email' => ['Email atau kata sandi yang Anda masukkan salah.']
            ], 401);
        }

        if (isset($user->is_active) && !$user->is_active) {
            return $this->sendError('Akun Anda telah dinonaktifkan oleh administrator.', [], 403);
        }

        // Generate Sanctum Token
        $token = $user->createToken('auth_token')->plainTextToken;

        \App\Models\ActivityLog::record('LOGIN_API', 'User ' . $user->name . ' (' . $user->role . ') berhasil login via REST API Sanctum.', $user->id);

        return $this->sendResponse([
            'user' => new UserResource($user),
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'Login berhasil.');
    }

    /**
     * User Registration (Admin / Petugas)
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'petugas',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        \App\Models\ActivityLog::record('REGISTER_API', 'User baru ' . $user->name . ' (' . $user->role . ') didaftarkan via REST API.', $user->id);

        return $this->sendResponse([
            'user' => new UserResource($user),
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'Registrasi user berhasil.', 201);
    }

    /**
     * Get Authenticated User Profile
     */
    public function me(Request $request): JsonResponse
    {
        return $this->sendResponse(
            new UserResource($request->user()),
            'Data profil berhasil diambil.'
        );
    }

    /**
     * User Logout & revoke current Sanctum Token
     */
    public function logout(Request $request): JsonResponse
    {
        \App\Models\ActivityLog::record('LOGOUT_API', 'User ' . $request->user()->name . ' mencabut token Sanctum.', $request->user()->id);

        // Revoke token yang sedang dipakai
        $request->user()->currentAccessToken()->delete();

        return $this->sendResponse(null, 'Logout berhasil, token telah dicabut.');
    }
}
