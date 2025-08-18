<?php

namespace App\Http\Controllers\Pages\User;

use App\Http\Controllers\Controller;
use App\Models\Camera;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserCameraLinkController extends Controller
{
  public function create()
  {
    return view('content.pages.User.Link-Camera', ['view' => 'link-camera']);
  }

  /**
   * Menyimpan (menyinkronkan) kamera ke akun pengguna.
   */
  public function store(Request $request)
  {
    $request->validate([
      'device_id' => 'required|uuid|exists:cameras,device_id',
    ], [
      'device_id.exists' => 'Device ID tidak ditemukan atau tidak valid.',
    ]);

    $user = Auth::user();
    $deviceId = $request->input('device_id');

    // Cari kamera berdasarkan Device ID
    $camera = Camera::where('device_id', $deviceId)->first();

    // Cek kepemilikan sebelumnya
    if ($camera->user_id !== null && !$camera->user->hasRole('admin')) {
      return back()->withErrors(['device_id' => 'Perangkat ini sudah terhubung dengan pengguna lain.']);
    }

    // FIX: Gunakan metode update yang lebih eksplisit untuk memastikan penyimpanan.
    $camera->update([
      'user_id' => $user->id
    ]);

    return redirect()->route('user.my-cameras.index')->with('success', 'Kamera berhasil ditambahkan ke akun Anda!');
  }
}
