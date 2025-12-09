<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\DB; // Tambahkan import DB

class ForgotPasswordController extends Controller
{
    /**
     * Endpoint API: POST /api/password/email
     * Mengirimkan tautan reset password ke email pengguna (DIUBAH: Mengembalikan token secara langsung).
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            // Mengembalikan respons yang aman (tidak memberitahu apakah email terdaftar)
            return response()->json([
                'message' => 'Password reset link sent successfully. Check your email.',
            ], 200);
        }

        // --- LOGIKA TANPA EMAIL/SMTP ---
        // 1. Generate Token Baru
        $token = Str::random(60);

        // 2. Simpan Token ke database `password_reset_tokens`
        // Pastikan Anda memiliki tabel password_reset_tokens (dibuat oleh migration Laravel)
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'email' => $request->email,
                'token' => Hash::make($token), // Hash token di DB (default Laravel)
                'created_at' => now()
            ]
        );
        // ---------------------------------

        return response()->json([
            'message' => 'Password reset token generated.',
            'reset_token' => $token // MENGEMBALIKAN TOKEN DI SINI (Untuk Debugging/Pengujian Tanpa SMTP)
        ], 200);
    }

    /**
     * Endpoint API: POST /api/password/reset
     * Memproses reset password menggunakan token dan kredensial baru.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Memproses reset password menggunakan token
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                // Opsional: Hapus semua token Sanctum lama agar user harus login ulang
                $user->tokens()->delete();
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            return response()->json([
                'message' => 'Password has been reset successfully. Please log in.',
            ], 200);
        }

        // Jika gagal (misalnya token tidak valid atau kedaluwarsa)
        return response()->json([
            'message' => 'Password reset failed.',
            'errors' => ['email' => [__($status)]]
        ], 400);
    }
}
