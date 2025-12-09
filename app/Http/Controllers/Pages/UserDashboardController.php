<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Camera;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
  /**
   * Mengupdate preferensi grup kamera pengguna.
   * Pastikan Anda memiliki route POST yang mengarah ke method ini,
   * misalnya: Route::post('/user-dashboard/groups', [UserDashboardController::class, 'updateGroups']);
   */
  public function updateGroups(Request $request)
  {
      $group = $request->input('group', 'all');

      // Simpan preferensi grup ke session
      // Kita gunakan key yang sama atau berbeda dengan admin tergantung kebutuhan
      session(['user_dashboard_camera_group' => $group]);

      return redirect()->back();
  }

  /**
   * Menampilkan halaman dashboard untuk pengguna biasa.
   */
  public function index()
  {
    $user = Auth::user();

    // 1. Ambil preferensi grup dari session (Default: 'all')
    $selectedGroup = session('user_dashboard_camera_group', 'all');

    // 2. Ambil daftar grup UNIK milik user ini saja untuk dropdown filter
    $groups = Camera::where('user_id', $user->id)
        ->select('group_name')
        ->whereNotNull('group_name')
        ->where('group_name', '!=', '')
        ->distinct()
        ->pluck('group_name')
        ->toArray();

    // Tambahkan opsi 'Semua Kamera' di awal array
    array_unshift($groups, 'Semua Kamera');

    // 3. Query dasar kamera milik user
    $cameraQuery = $user->cameras()
      ->with(['imageRecords' => function ($query) {
        $query->latest('captured_at')->limit(1); // Eager load gambar terakhir
      }]);

    // 4. Terapkan filter berdasarkan grup yang dipilih
    if ($selectedGroup !== 'Semua Kamera' && $selectedGroup !== 'all') {
        $cameraQuery->where('group_name', $selectedGroup);
    }

    // 5. Ambil data kamera (Hapus limit(9) agar user bisa melihat semua kamera dalam grup)
    $cameras = $cameraQuery->latest()->get();

    // Statistik (Tetap hitung total keseluruhan tanpa filter grup)
    $totalCameras = $user->cameras()->count();
    $activeCameras = $user->cameras()->where('is_active', true)->count();

    // Tentukan label grup saat ini untuk view
    $currentGroup = $selectedGroup === 'all' ? 'Semua Kamera' : $selectedGroup;

    return view('user-dashboard', compact(
      'totalCameras',
      'activeCameras',
      'cameras',
      'groups',       // Data untuk dropdown filter
      'currentGroup'  // Data untuk judul/badge filter aktif
    ));
  }
}
