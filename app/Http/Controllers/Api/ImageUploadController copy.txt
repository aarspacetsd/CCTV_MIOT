<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Camera;
use App\Models\ImageRecord; // Pastikan model ini di-import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator; // Import Validator

class ImageUploadController extends Controller
{
    /**
     * Menerima, menyimpan gambar, dan memperbarui status heartbeat kamera.
     */
    public function store(Request $request)
    {
        Log::info('--- [API UPLOAD START] ---');
        Log::info('Request received from IP: ' . $request->ip());

        // Validasi input
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string|exists:cameras,device_id', // Lebih baik 'string' daripada 'uuid' jika tidak pasti
            'api_key'   => 'required|string',
            'image'     => 'required|image|mimes:jpeg,jpg|max:2048', // Maksimal 2MB
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed.', $validator->errors()->toArray());
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }
        Log::info('Validation passed for device_id: ' . $request->device_id);

        // Cari kamera berdasarkan device_id
        $camera = Camera::where('device_id', $request->device_id)->first();

        // Validasi API Key (sudah mencakup pengecekan jika kamera ada)
        if (!$camera || !hash_equals($camera->api_key, $request->api_key)) {
            Log::error('Unauthorized access attempt for device_id: ' . $request->device_id);
            return response()->json(['message' => 'Unauthorized: Invalid device_id or api_key.'], 401);
        }
        Log::info('API Key validated successfully for camera: ' . $camera->name);

        // --- LOGIKA UTAMA YANG DIPERBAIKI ---
        // 1. Perbarui timestamp heartbeat SEKARANG, karena kita menerima data yang valid.
        // Ini akan secara otomatis membuat status kamera menjadi "Aktif".
        $camera->last_heartbeat_at = now();
        $camera->save();
        Log::info('Heartbeat timestamp updated for camera: ' . $camera->name);

        // 2. Hapus pengecekan 'is_active' yang lama.
        // Pengecekan tersebut tidak lagi diperlukan karena kita sudah memperbarui heartbeat.

        // 3. Lanjutkan proses penyimpanan file
        try {
            $dateFolder = now()->format('Y-m-d');
            $filename = now()->format('His') . '_' . uniqid() . '.' . $request->file('image')->getClientOriginalExtension();
            $directory = "camera_images/{$camera->device_id}/{$dateFolder}";

            $path = $request->file('image')->storeAs($directory, $filename, 'public');

            Log::info('SUCCESS: File stored at path: ' . $path);
        } catch (\Exception $e) {
            Log::error('!!! FILE STORAGE FAILED !!!', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Could not store image.'], 500);
        }

        // 4. Buat record di database
        try {
            $camera->imageRecords()->create([
                'path'        => $path,
                'captured_at' => now(),
            ]);
            Log::info('SUCCESS: Database record created.');
        } catch (\Exception $e) {
            Log::error('!!! DATABASE INSERT FAILED !!!', ['error' => $e->getMessage()]);
            // Opsional: Hapus file yang sudah terunggah jika DB gagal
            Storage::disk('public')->delete($path);
            Log::info('File ' . $path . ' deleted due to DB failure.');
            return response()->json(['message' => 'Could not save image record.'], 500);
        }

        Log::info('--- [API UPLOAD END] ---');
        return response()->json(['message' => 'Image uploaded successfully'], 201);
    }
}
