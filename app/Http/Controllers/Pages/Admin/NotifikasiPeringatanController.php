<?php

namespace App\Http\Controllers\Pages\Admin;

use App\Http\Controllers\Controller;
use App\Models\Camera;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifikasiPeringatanController extends Controller
{
  /**
   * Menampilkan daftar notifikasi dan peringatan.
   */
  public function index()
  {
    // Logika untuk mendeteksi kamera offline.
    // Ambil kamera milik user yang tidak aktif atau tidak memiliki rekaman gambar
    // dalam 10 menit terakhir.
    $offlineCameras = Auth::user()->cameras()
      ->where(function ($query) {
        $query->where('is_active', false)
          ->orWhereDoesntHave('imageRecords', function ($subQuery) {
            $subQuery->where('created_at', '>=', now()->subMinutes(10));
          });
      })
      ->get();

    // Anda bisa menambahkan logika notifikasi lain di sini di masa depan,
    // seperti notifikasi dari LogDeteksiML atau ActivityLog.

    return view('content.pages.admin.Notifikasi_Peringatan', compact('offlineCameras'));
  }
}
