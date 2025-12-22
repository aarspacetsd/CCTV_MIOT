<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Camera;
use App\Models\CameraGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    /**
     * Mengupdate preferensi grup kamera pengguna.
     * Route: Route::post('/user-dashboard/groups', [UserDashboardController::class, 'updateGroups']);
     */
    public function updateGroups(Request $request)
    {
        // Default ke 'Semua Kamera' agar konsisten dengan filter dropdown
        $group = $request->input('group', 'Semua Kamera');

        // Simpan preferensi grup ke session khusus user dashboard
        session(['user_dashboard_camera_group' => $group]);

        return redirect()->back();
    }

    /**
     * Menampilkan halaman dashboard untuk pengguna biasa.
     */
    public function index()
    {
        $user = Auth::user();

        // 1. Ambil preferensi grup dari session (Default: 'Semua Kamera')
        $selectedGroup = session('user_dashboard_camera_group', 'Semua Kamera');

        // 2. Ambil daftar nama grup milik user dari tabel master (CameraGroup)
        $groups = CameraGroup::where('user_id', $user->id)
            ->pluck('name')
            ->toArray();

        // Tambahkan opsi 'Semua Kamera' di awal array
        array_unshift($groups, 'Semua Kamera');

        // 3. Query dasar kamera milik user dengan Eager Loading
        // Memuat relasi 'group' dan 'imageRecords' terbaru untuk efisiensi performa
        $cameraQuery = Camera::where('user_id', $user->id)
            ->with(['group', 'imageRecords' => function ($query) {
                $query->latest('captured_at')->limit(1);
            }]);

        // 4. Terapkan filter berdasarkan relasi group jika bukan "Semua Kamera"
        if ($selectedGroup !== 'Semua Kamera') {
            $cameraQuery->whereHas('group', function($q) use ($selectedGroup) {
                $q->where('name', $selectedGroup);
            });
        }

        // 5. Ambil semua data kamera yang sesuai filter
        $cameras = $cameraQuery->latest()->get();

        // Statistik (Tetap hitung total keseluruhan milik user tanpa filter grup)
        $totalCameras = Camera::where('user_id', $user->id)->count();
        $activeCameras = Camera::where('user_id', $user->id)->where('is_active', true)->count();

        // Tentukan label grup saat ini untuk dikirim ke view
        $currentGroup = $selectedGroup;

        return view('user-dashboard', compact(
            'totalCameras',
            'activeCameras',
            'cameras',
            'groups',       // Data untuk dropdown filter (dari tabel master)
            'currentGroup'  // Grup yang sedang aktif di session
        ));
    }
}
