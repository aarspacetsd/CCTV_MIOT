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
   * Mengupdate preferensi grup kamera pengguna.
   * Dipanggil melalui rute POST: /dashboard/groups
   * * @param \Illuminate\Http\Request $request
   */
  public function updateGroups(Request $request)
  {
      $group = $request->input('group', 'all');

      // Simpan preferensi grup ke session
      session(['dashboard_camera_group' => $group]);

      // Redirect kembali ke halaman dashboard
      return redirect()->route('dashboard.index');
  }

  /**
   * Menampilkan halaman dashboard utama.
   */
  public function index()
  {
    $user = Auth::user();

    // Ambil preferensi grup saat ini dari session, default ke 'all'
    $selectedGroup = session('dashboard_camera_group', 'all');

    // PERBAIKAN DI SINI:
    // Tambahkan where('user_id', $user->id) agar hanya mengambil grup milik user tersebut
    $groups = Camera::where('user_id', $user->id)
        ->select('group_name')
        ->whereNotNull('group_name')
        ->where('group_name', '!=', '') // Tambahan: hindari nama grup kosong string
        ->distinct()
        ->pluck('group_name')
        ->toArray();

    // Tambahkan opsi 'Semua Kamera' di awal
    array_unshift($groups, 'Semua Kamera');

    // Query dasar untuk kamera pengguna
    $cameraQuery = $user->cameras();

    // Terapkan filter berdasarkan grup yang dipilih
    if ($selectedGroup !== 'Semua Kamera' && $selectedGroup !== 'all') {
        $cameraQuery->where('group_name', $selectedGroup);
    }

    // Mengambil statistik (tetap berdasarkan total kamera pengguna, tidak terfilter)
    $totalCameras = $user->cameras()->count();
    $activeCameras = $user->cameras()->where('is_active', true)->count();
    $totalUsers = User::count(); // Asumsi admin bisa melihat semua user

    // Mengambil daftar kamera untuk ditampilkan di grid (sudah terfilter)
    $cameras = $cameraQuery->latest()->get();

    // Jika filter grup kosong, pastikan kita tetap meneruskan 'all' atau 'Semua Kamera'
    $currentGroup = $selectedGroup === 'all' ? 'Semua Kamera' : $selectedGroup;


    return view('dashboard', compact(
      'totalCameras',
      'activeCameras',
      'totalUsers',
      'cameras',
      'groups',          // Daftar grup yang tersedia (sekarang sudah difilter per user)
      'currentGroup'     // Grup yang sedang aktif
    ));
  }
}
