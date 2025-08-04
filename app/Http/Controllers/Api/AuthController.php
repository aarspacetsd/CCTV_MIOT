<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
  public function login(Request $request)
  {
    $credentials = $request->validate([
      'email' => 'required|email',
      'password' => 'required',
    ]);

    if (!Auth::attempt($credentials)) {
      return response()->json(['message' => 'Email atau password salah.'], 401);
    }

    $user = User::where('email', $request->email)->first();

    // Hapus token lama jika ada, dan buat token baru
    $user->tokens()->delete();
    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
      'message' => 'Login berhasil',
      'access_token' => $token,
      'token_type' => 'Bearer',
    ]);
  }

  public function logout(Request $request)
  {
    $request->user()->currentAccessToken()->delete();

    return response()->json(['message' => 'Logout berhasil']);
  }
}
