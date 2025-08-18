<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
  /**
   * Menampilkan halaman dashboard untuk pengguna biasa.
   */
  public function index()
  {
    $user = Auth::user();

    // Mengambil statistik khusus untuk pengguna yang login
    $totalCameras = $user->cameras()->count();
    $activeCameras = $user->cameras()->where('is_active', true)->count();

    // Mengambil daftar kamera milik pengguna untuk ditampilkan di grid,
    // beserta gambar terbarunya untuk efisiensi.
    $cameras = $user->cameras()
      ->with(['imageRecords' => function ($query) {
        $query->latest('captured_at')->limit(1);
      }])
      ->latest()
      ->take(9) // Ambil 9 kamera terbaru milik pengguna
      ->get();

    return view('user-dashboard', compact(
      'totalCameras',
      'activeCameras',
      'cameras'
    ));
  }
}
