<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Camera;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log; // <-- 1. Import Log facade

class ImageUploadController extends Controller
{
  /**
   * Menerima dan menyimpan gambar dari perangkat ESP32-CAM.
   */
  public function store(Request $request)
  {
    // 2. Tambahkan log di setiap langkah
    Log::info('--- [API UPLOAD START] ---');
    Log::info('Request received from IP: ' . $request->ip());

    // Validasi input
    $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
      'device_id' => 'required|uuid',
      'api_key' => 'required|string',
      'image' => 'required|image|mimes:jpeg,jpg|max:2048',
    ]);

    if ($validator->fails()) {
      Log::error('Validation failed.', $validator->errors()->toArray());
      return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
    }
    Log::info('Validation passed.');

    // Cari kamera
    $camera = Camera::where('device_id', $request->device_id)->first();
    if ($camera) {
      Log::info('Camera found in DB: ' . $camera->name);
    } else {
      Log::error('Camera not found for device_id: ' . $request->device_id);
      return response()->json(['message' => 'Camera not found'], 404);
    }

    // Validasi API Key
    if (!hash_equals($camera->api_key, $request->api_key)) {
      Log::error('API Key mismatch for camera: ' . $camera->name);
      return response()->json(['message' => 'Unauthorized'], 401);
    }
    Log::info('API Key validated successfully.');

    // Cek status kamera
    if (!$camera->is_active) {
      Log::warning('Attempt to upload to inactive camera: ' . $camera->name);
      return response()->json(['message' => 'Inactive Camera'], 401);
    }
    Log::info('Camera is active.');

    // Proses penyimpanan file
    try {
      $dateFolder = now()->format('Y-m-d');
      $directory = "camera_images/{$camera->device_id}/{$dateFolder}";
      Log::info('Attempting to store file in directory: ' . $directory);

      $path = $request->file('image')->store($directory, 'public');

      Log::info('SUCCESS: File stored at path: ' . $path);
    } catch (\Exception $e) {
      Log::error('!!! FILE STORAGE FAILED !!!');
      Log::error($e->getMessage());
      return response()->json(['message' => 'Could not store image.'], 500);
    }

    // Buat record di database
    try {
      $camera->imageRecords()->create([
        'path' => $path,
        'captured_at' => now()
      ]);
      Log::info('SUCCESS: Database record created.');
    } catch (\Exception $e) {
      Log::error('!!! DATABASE INSERT FAILED !!!');
      Log::error($e->getMessage());
      // Opsional: Hapus file yang sudah terunggah jika DB gagal
      Storage::disk('public')->delete($path);
      Log::info('File ' . $path . ' deleted due to DB failure.');
      return response()->json(['message' => 'Could not save image record.'], 500);
    }

    Log::info('--- [API UPLOAD END] ---');
    return response()->json(['message' => 'Image uploaded successfully'], 201);
  }
}
