<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Camera;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserCameraGroupApiController extends Controller
{
    /**
     * Get All Groups Data
     * Mengembalikan daftar grup beserta kameranya, dan daftar kamera tanpa grup.
     * Cocok untuk tampilan ExpandableListView atau RecyclerView di Android.
     */
    public function index()
    {
        $user = auth()->user();

        // 1. Ambil kamera yang SUDAH punya grup, dikelompokkan by group_name
        $groupedCameras = $user->cameras()
            ->whereNotNull('group_name')
            ->where('group_name', '!=', '')
            ->get()
            ->groupBy('group_name');

        // Format data grup agar lebih rapi untuk JSON
        $groupsData = [];
        foreach ($groupedCameras as $groupName => $cameras) {
            $groupsData[] = [
                'group_name' => $groupName,
                'camera_count' => $cameras->count(),
                'cameras' => $cameras->values() // Reset array keys
            ];
        }

        // 2. Ambil kamera yang BELUM punya grup (Ungrouped)
        $ungroupedCameras = $user->cameras()
            ->whereNull('group_name')
            ->orWhere('group_name', '')
            ->get();

        // 3. Ambil daftar grup kosong (jika Anda menyimpan ini di DB terpisah, ambil dari sana)
        // Karena di versi web pakai session, untuk API biasanya kita hanya return grup yang ada isinya,
        // KECUALI jika Anda membuat tabel khusus 'camera_groups'.
        // Di sini kita asumsikan grup dinamis berdasarkan kolom 'group_name' di tabel cameras.

        return response()->json([
            'success' => true,
            'data' => [
                'groups' => $groupsData,
                'ungrouped_cameras' => $ungroupedCameras
            ]
        ], 200);
    }

    /**
     * Create New Group
     * Sebenarnya hanya validasi nama, karena grup di sistem ini based on column value.
     * Tapi kita bisa langsung assign kamera saat create.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group_name' => 'required|string|max:255',
            'camera_ids' => 'nullable|array', // Opsional: langsung masukkan kamera
            'camera_ids.*' => 'exists:cameras,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        $groupName = $request->group_name;
        $user = auth()->user();

        // Cek apakah grup sudah ada di kamera milik user ini
        $exists = $user->cameras()->where('group_name', $groupName)->exists();

        // Logika: Jika grup belum ada, dan tidak ada kamera yang dipilih,
        // secara teknis grup "belum terbentuk" di DB karena tidak ada tabel master grup.
        // Namun untuk API, kita bisa return success saja agar UI Android menganggap grup dibuat.
        // Jika ada kamera yang dipilih, kita update kamera tersebut.

        if ($request->has('camera_ids') && count($request->camera_ids) > 0) {
            $user->cameras()
                ->whereIn('id', $request->camera_ids)
                ->update(['group_name' => $groupName]);
        }

        return response()->json([
            'success' => true,
            'message' => "Grup '$groupName' berhasil dibuat/diperbarui.",
            'data' => [
                'group_name' => $groupName,
                'assigned_count' => $request->has('camera_ids') ? count($request->camera_ids) : 0
            ]
        ], 200);
    }

    /**
     * Rename Group
     */
    public function update(Request $request)
    {
        // Kita pakai POST/PUT ke endpoint ini dengan parameter old_name dan new_name
        $validator = Validator::make($request->all(), [
            'old_group_name' => 'required|string',
            'new_group_name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $oldName = $request->old_group_name;
        $newName = $request->new_group_name;
        $user = auth()->user();

        // Cek apakah old group ada
        $count = $user->cameras()->where('group_name', $oldName)->count();
        if ($count === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Grup lama tidak ditemukan atau kosong.'
            ], 404);
        }

        // Cek apakah new group name sudah dipakai di grup LAIN
        $exists = $user->cameras()
            ->where('group_name', $newName)
            ->where('group_name', '!=', $oldName)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => "Nama grup '$newName' sudah digunakan."
            ], 422);
        }

        // Lakukan Update Massal
        $user->cameras()
            ->where('group_name', $oldName)
            ->update(['group_name' => $newName]);

        return response()->json([
            'success' => true,
            'message' => "Grup berhasil diubah namanya menjadi '$newName'."
        ], 200);
    }

    /**
     * Delete Group (Ungroup All Cameras in Group)
     */
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $groupName = $request->group_name;
        $user = auth()->user();

        // Set group_name menjadi NULL untuk semua kamera di grup ini
        $affected = $user->cameras()
            ->where('group_name', $groupName)
            ->update(['group_name' => null]);

        if ($affected === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Grup tidak ditemukan atau sudah kosong.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => "Grup '$groupName' berhasil dihapus. $affected kamera sekarang tanpa grup."
        ], 200);
    }

    /**
     * Assign Camera to Group (Move Camera)
     */
    public function assignCamera(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'camera_id' => 'required|exists:cameras,id',
            'group_name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $camera = auth()->user()->cameras()->find($request->camera_id);

        if (!$camera) {
            return response()->json([
                'success' => false,
                'message' => 'Kamera tidak ditemukan atau bukan milik Anda.'
            ], 404);
        }

        $camera->update(['group_name' => $request->group_name]);

        return response()->json([
            'success' => true,
            'message' => "Kamera berhasil dipindahkan ke grup '{$request->group_name}'.",
            'data' => $camera
        ], 200);
    }

    /**
     * Remove Camera from Group (Set to Ungrouped)
     */
    public function removeCamera(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'camera_id' => 'required|exists:cameras,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $camera = auth()->user()->cameras()->find($request->camera_id);

        if (!$camera) {
            return response()->json([
                'success' => false,
                'message' => 'Kamera tidak ditemukan atau bukan milik Anda.'
            ], 404);
        }

        $oldGroup = $camera->group_name;
        $camera->update(['group_name' => null]);

        return response()->json([
            'success' => true,
            'message' => "Kamera berhasil dihapus dari grup '$oldGroup'.",
            'data' => $camera
        ], 200);
    }
}
