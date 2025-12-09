<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    /**
     * Handle an incoming API registration request.
     * Endpoint: POST /api/register
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Tetapkan role 'user' (asumsi role sudah ada)
        // Pastikan Anda telah menjalankan seeder roles jika ini adalah instalasi baru
        $user->assignRole('user');

        // Buat token otentikasi Sanctum untuk klien Android
        $token = $user->createToken("auth_token")->plainTextToken;

        // Kembalikan respons JSON dengan data user dan token
        return response()->json([
            'message' => 'User registered successfully',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => $token,
            'token_type' => 'Bearer',
        ], 201); // Kode status 201 Created
    }

    /**
     * Handle an incoming API login request.
     * Endpoint: POST /api/login
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Email atau password salah.'], 401);
        }

        $user = Auth::user();

        // Hapus token lama user ini, dan buat token baru untuk sesi baru
        // Ini memastikan hanya satu token yang aktif per perangkat/sesi.
        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Handle an API logout request.
     * Endpoint: POST /api/logout (Memerlukan otentikasi)
     */
    public function logout(Request $request)
    {
        // Hapus token yang sedang digunakan oleh klien saat ini
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout berhasil']);
    }
}
