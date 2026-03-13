<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Camera;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class EmqxWebSocketController extends Controller
{
    /**
     * Menangani data gambar yang dikirim perangkat via WebSocket.
     * Topik: ws/camera/{device_id}/image
     */
    public function handleImage(Request $request)
    {
        $topic = $request->topic;
        $payload = $request->payload; // Base64 string dari gambar

        // Ekstrak device_id dari topik
        preg_match('/ws\/camera\/(.+)\/image/', $topic, $matches);
        $deviceId = $matches[1] ?? null;

        if (!$deviceId || empty($payload)) {
            return response()->json(['message' => 'Data tidak lengkap'], 400);
        }

        $camera = Camera::where('device_id', $deviceId)->first();

        if (!$camera) {
            return response()->json(['message' => 'Kamera tidak ditemukan'], 404);
        }

        try {
            // 1. Decode gambar Base64
            $imageData = base64_decode($payload);
            $fileName = microtime(true) . '.jpg';
            $path = "camera/{$deviceId}/" . $fileName;

            // 2. Simpan ke MinIO (Disk S3)
            Storage::disk('s3')->put($path, $imageData);

            // 3. Catat ke Database
            $camera->imageRecords()->create([
                'path' => $path,
                'captured_at' => now()
            ]);

            // 4. Update Heartbeat
            $camera->update(['last_heartbeat_at' => now()]);

            Log::info("WS_IMAGE_UPLOAD_SUCCESS dari {$camera->name}");

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error("WS_IMAGE_UPLOAD_FAILED: " . $e->getMessage());
            return response()->json(['status' => 'error', 'msg' => $e->getMessage()], 500);
        }
    }

    /**
     * Menangani data telemetri (sensor/status)
     */
    public function handleTelemetry(Request $request)
    {
        preg_match('/ws\/camera\/(.+)\/telemetry/', $request->topic, $matches);
        $deviceId = $matches[1] ?? null;

        $camera = Camera::where('device_id', $deviceId)->first();
        if ($camera) {
            $camera->update(['last_heartbeat_at' => now()]);
            Log::info("WS_TELEMETRY_RECEIVED dari {$camera->name}");
            return response()->json(['status' => 'success']);
        }
        return response()->json(['status' => 'not_found'], 404);
    }
}
