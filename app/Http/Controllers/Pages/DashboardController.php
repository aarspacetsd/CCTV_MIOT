<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Camera;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
  /**
   * Menampilkan halaman dashboard utama.
   */
  public function index()
  {
    $user = Auth::user();

    // Mengambil statistik
    $totalCameras = $user->cameras()->count();
    $activeCameras = $user->cameras()->where('is_active', true)->count();
    $totalUsers = User::count(); // Asumsi admin bisa melihat semua user

    // Mengambil daftar kamera untuk ditampilkan di grid
    $cameras = $user->cameras()->latest()->take(6)->get(); // Ambil 6 kamera terbaru

    return view('dashboard', compact(
      'totalCameras',
      'activeCameras',
      'totalUsers',
      'cameras'
    ));
  }
}
