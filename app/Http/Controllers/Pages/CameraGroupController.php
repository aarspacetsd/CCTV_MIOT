<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Camera;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CameraGroupController extends Controller
{
    /**
     * Tampilkan halaman manajemen grup
     */
    public function index()
    {
        $user = Auth::user();

        // Ambil semua grup yang ada (termasuk yang kosong dari session jika perlu)
        $groups = Camera::where('user_id', $user->id)
            ->whereNotNull('group_name')
            ->select('group_name')
            ->distinct()
            ->pluck('group_name');

        // Ambil kamera yang belum di-grup
        $ungroupedCameras = $user->cameras()
            ->whereNull('group_name')
            ->get();

        // Ambil semua kamera dengan grupnya
        $groupedCameras = $user->cameras()
            ->whereNotNull('group_name')
            ->get()
            ->groupBy('group_name');

        // Ambil daftar grup kosong dari session (jika ada)
        $emptyGroups = session('empty_groups', []);

        return view('CameraGroups', compact(
            'groups',
            'ungroupedCameras',
            'groupedCameras',
            'emptyGroups'
        ));
    }

    /**
     * Buat grup baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'group_name' => 'required|string|max:255',
            'camera_ids' => 'nullable|array', // Opsional: bisa pilih kamera saat buat grup
            'camera_ids.*' => 'exists:cameras,id',
        ]);

        $groupName = $request->group_name;

        // Cek apakah grup sudah ada
        $exists = Camera::where('user_id', Auth::id())
            ->where('group_name', $groupName)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Grup dengan nama tersebut sudah ada!');
        }

        // Jika ada kamera yang dipilih, assign ke grup baru
        if ($request->has('camera_ids') && !empty($request->camera_ids)) {
            Camera::where('user_id', Auth::id())
                ->whereIn('id', $request->camera_ids)
                ->update(['group_name' => $groupName]);

            $cameraCount = count($request->camera_ids);
            return back()->with('success', "Grup \"$groupName\" berhasil dibuat dengan $cameraCount kamera!");
        }

        // Jika tidak ada kamera yang dipilih, simpan nama grup ke session
        // Tambahkan grup ke daftar grup kosong
        $emptyGroups = session('empty_groups', []);
        if (!in_array($groupName, $emptyGroups)) {
            $emptyGroups[] = $groupName;
            session(['empty_groups' => $emptyGroups]);
        }

        return back()->with('info', "Grup \"$groupName\" sudah siap! Silakan tambahkan kamera ke grup ini dari area 'Kamera Tanpa Grup'.");
    }

    /**
     * Update nama grup
     */
    public function update(Request $request, $oldGroupName)
    {
        $request->validate([
            'new_group_name' => 'required|string|max:255',
        ]);

        $newGroupName = $request->new_group_name;

        // Cek apakah nama baru sudah digunakan
        $exists = Camera::where('user_id', Auth::id())
            ->where('group_name', $newGroupName)
            ->where('group_name', '!=', $oldGroupName)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Nama grup "' . $newGroupName . '" sudah digunakan!');
        }

        // Update semua kamera di grup lama ke nama grup baru
        Camera::where('user_id', Auth::id())
            ->where('group_name', $oldGroupName)
            ->update(['group_name' => $newGroupName]);

        return back()->with('success', 'Nama grup berhasil diubah dari "' . $oldGroupName . '" menjadi "' . $newGroupName . '"');
    }

    /**
     * Assign kamera ke grup
     */
    public function assignCamera(Request $request)
    {
        $request->validate([
            'camera_id' => 'required|exists:cameras,id',
            'group_name' => 'required|string|max:255',
        ]);

        $camera = Camera::where('id', $request->camera_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $camera->update(['group_name' => $request->group_name]);

        // Hapus grup dari empty_groups jika ada
        $emptyGroups = session('empty_groups', []);
        if (($key = array_search($request->group_name, $emptyGroups)) !== false) {
            unset($emptyGroups[$key]);
            session(['empty_groups' => array_values($emptyGroups)]);
        }

        return back()->with('success', 'Kamera "' . $camera->name . '" berhasil ditambahkan ke grup "' . $request->group_name . '"');
    }

    /**
     * Pindahkan kamera dari grup
     */
    public function removeCamera(Request $request)
    {
        $request->validate([
            'camera_id' => 'required|exists:cameras,id',
        ]);

        $camera = Camera::where('id', $request->camera_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $oldGroup = $camera->group_name;
        $camera->update(['group_name' => null]);

        // Jika grup menjadi kosong, tambahkan ke empty_groups
        $remainingCameras = Camera::where('user_id', Auth::id())
            ->where('group_name', $oldGroup)
            ->count();

        if ($remainingCameras === 0) {
            $emptyGroups = session('empty_groups', []);
            if (!in_array($oldGroup, $emptyGroups)) {
                $emptyGroups[] = $oldGroup;
                session(['empty_groups' => $emptyGroups]);
            }
        }

        return back()->with('success', 'Kamera "' . $camera->name . '" berhasil dihapus dari grup "' . $oldGroup . '"');
    }

    /**
     * Hapus grup (set semua kamera di grup menjadi ungrouped)
     */
    public function destroy($groupName)
    {
        // Hapus dari database
        $count = Camera::where('user_id', Auth::id())
            ->where('group_name', $groupName)
            ->update(['group_name' => null]);

        // Hapus dari empty groups session
        $emptyGroups = session('empty_groups', []);
        if (($key = array_search($groupName, $emptyGroups)) !== false) {
            unset($emptyGroups[$key]);
            session(['empty_groups' => array_values($emptyGroups)]);
        }

        if ($count > 0) {
            return back()->with('success', 'Grup "' . $groupName . '" berhasil dihapus. ' . $count . ' kamera dikembalikan ke status tanpa grup.');
        } else {
            return back()->with('success', 'Grup kosong "' . $groupName . '" berhasil dihapus.');
        }
    }
}
