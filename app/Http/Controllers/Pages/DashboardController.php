<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Camera;
use App\Models\CameraGroup;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Mengupdate preferensi grup kamera pengguna melalui session.
     * Dipanggil melalui rute POST: /dashboard/groups
     */
    public function updateGroups(Request $request)
    {
        // Validasi input group
        $group = $request->input('group', 'Semua Kamera');

        // Simpan preferensi nama grup ke session
        session(['dashboard_camera_group' => $group]);

        // Redirect kembali ke halaman dashboard
        return redirect()->route('dashboard.index');
    }

    /**
     * Menampilkan halaman dashboard utama dengan filter grup.
     */
    public function index()
    {
        $user = Auth::user();

        // 1. Ambil preferensi grup saat ini dari session, default ke 'Semua Kamera'
        $selectedGroup = session('dashboard_camera_group', 'Semua Kamera');

        // 2. Ambil daftar nama grup milik user dari tabel master untuk dropdown filter
        $groups = CameraGroup::where('user_id', $user->id)
            ->pluck('name')
            ->toArray();

        // Tambahkan opsi 'Semua Kamera' di awal array
        array_unshift($groups, 'Semua Kamera');

        // 3. Query dasar untuk kamera milik pengguna dengan Eager Loading
        // Kita memuat relasi 'group' dan hanya 1 'imageRecords' terbaru untuk efisiensi
        $cameraQuery = Camera::where('user_id', $user->id)
            ->with(['group', 'imageRecords' => function($q) {
                $q->latest()->limit(1);
            }]);

        // 4. Terapkan filter berdasarkan nama grup yang dipilih
        if ($selectedGroup !== 'Semua Kamera') {
            $cameraQuery->whereHas('group', function($q) use ($selectedGroup) {
                $q->where('name', $selectedGroup);
            });
        }

        // 5. Mengambil daftar kamera (sudah terfilter)
        $cameras = $cameraQuery->latest()->get();

        // 6. Mengambil statistik (tetap berdasarkan total kamera pengguna secara global)
        $totalCameras = Camera::where('user_id', $user->id)->count();
        $activeCameras = Camera::where('user_id', $user->id)->where('is_active', true)->count();
        $totalUsers = User::count(); // Biasanya untuk dashboard admin

        // Menentukan label grup saat ini
        $currentGroup = $selectedGroup;

        return view('dashboard', compact(
            'totalCameras',
            'activeCameras',
            'totalUsers',
            'cameras',
            'groups',
            'currentGroup'
        ));
    }
}
