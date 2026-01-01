<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    /**
     * Endpoint API: POST /api/password/email
     * Menghasilkan 6 digit OTP untuk reset password di Android dan mengirimkannya ke email.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            // Memberikan respon sukses palsu demi keamanan
            return response()->json([
                'message' => 'Jika email terdaftar, kode OTP telah dikirim.',
            ], 200);
        }

        // --- LOGIKA OTP 6 DIGIT UNTUK ANDROID ---
        // 1. Generate 6 digit angka acak
        $otp = (string) random_int(100000, 999999);

        // 2. Simpan OTP ke database (di-hash agar aman)
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'email' => $request->email,
                'token' => Hash::make($otp),
                'created_at' => now()
            ]
        );

        // 3. Kirim email berisi kode OTP
        try {
            Mail::raw("Kode OTP untuk reset password Anda adalah: {$otp}. Kode ini berlaku selama 60 menit.", function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('Kode OTP Reset Password - CCTV MIOT');
            });
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengirim email. Silakan hubungi admin.',
                'error' => $e->getMessage()
            ], 500);
        }

        // otp_code telah dihapus dari JSON karena sekarang sudah dikirim via email
        return response()->json([
            'message' => 'Kode OTP telah dikirim ke email Anda.',
        ], 200);
    }

    /**
     * Endpoint API: POST /api/password/reset
     * Memproses reset password menggunakan OTP 6 digit.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => ['required', 'digits:6'], // Validasi harus 6 digit angka
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Laravel akan mencocokkan input 'token' (OTP) dengan Hash di database secara otomatis
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                // Hapus semua token akses (Sanctum) agar user harus login ulang di semua perangkat
                $user->tokens()->delete();
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            return response()->json([
                'message' => 'Password berhasil diubah. Silakan login kembali.',
            ], 200);
        }

        return response()->json([
            'message' => 'Gagal meriset password. Kode OTP mungkin salah atau sudah kedaluwarsa.',
            'errors' => ['token' => [__($status)]]
        ], 400);
    }
}
