<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Camera;
use App\Models\ImageRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB; // Diperlukan untuk DB::raw()
use Illuminate\Support\Facades\Log; // Diperlukan untuk rename
use Carbon\Carbon; // Diperlukan untuk format tanggal
use Illuminate\Support\Str; // Diperlukan untuk rename

class ImageHistoryController extends Controller
{
    /**
     * Mengambil riwayat rekaman gambar (image records) berdasarkan filter waktu.
     * Endpoint: GET /api/images/{camera}/history
     * Query Params: date=YYYY-MM-DD, hour=HH, minute=MM, chunk=N
     */
    public function historyExplorer(Request $request, Camera $camera)
    {
        // 1. Otorisasi
        if ($request->user()->id !== $camera->user_id) {
            return response()->json(['message' => 'Forbidden: You do not own this camera.'], 403);
        }

        $date = $request->query('date');
        $hour = $request->query('hour');
        $minute = $request->query('minute');
        $chunk = $request->query('chunk');
        $imagesPerChunk = 30; // Konstanta untuk chunking

        $query = $camera->imageRecords();
        $level = 'date'; // Default level

        // 2. Query Berdasarkan Filter
        if ($date) {
            try {
                $date = Carbon::parse($date)->format('Y-m-d');
            } catch (\Exception $e) {
                return response()->json(['message' => 'Invalid date format.'], 400);
            }
            $query->whereDate('captured_at', $date);

            if ($hour) {
                $query->where(DB::raw('HOUR(captured_at)'), $hour);

                if ($minute) {
                    $query->where(DB::raw('MINUTE(captured_at)'), $minute);
                    $level = 'gallery'; // Mencapai level gambar detail atau chunking
                } else {
                    $level = 'minute'; // Filter sampai jam
                }
            } else {
                $level = 'hour'; // Filter sampai tanggal
            }
        }

        // 3. Pemrosesan dan Pengembalian Data Berdasarkan Level
        $response = [
            'level' => $level,
            'camera_id' => $camera->id,
            'filter' => ['date' => $date, 'hour' => $hour, 'minute' => $minute, 'chunk' => $chunk],
            'items' => [],
            'pagination' => null,
        ];

        switch ($level) {
            case 'gallery':
                $minuteQuery = clone $query;
                $totalImagesInMinute = $minuteQuery->count();

                if ($totalImagesInMinute > $imagesPerChunk && !$chunk) {
                    $level = 'chunk';
                } else {
                    $skip = $chunk ? ($chunk - 1) * $imagesPerChunk : 0;
                    $images = $minuteQuery
                        ->reorder('captured_at', 'asc') // Hapus order lama, terapkan order untuk gallery
                        ->skip($skip)->take($imagesPerChunk)->get()->map(function ($image) {
                        return [
                            'id' => $image->id,
                            'file_name' => $image->name ?? basename($image->path),
                            'url' => Storage::url($image->path),
                            'captured_at' => Carbon::parse($image->captured_at)->toDateTimeString(),
                        ];
                    });
                    $response['items'] = $images;
                    break;
                }
                // Fallthrough ke 'chunk' jika diperlukan

            case 'chunk':
                // Memproses data menjadi kelompok chunk (setelah difilter sampai menit)
                $totalImagesInMinute = $query->count();
                $numberOfChunks = ceil($totalImagesInMinute / $imagesPerChunk);
                $chunks = [];
                for ($i = 1; $i <= $numberOfChunks; $i++) {
                    $startRange = ($i - 1) * $imagesPerChunk + 1;
                    $endRange = min($i * $imagesPerChunk, $totalImagesInMinute);
                    $chunks[] = [
                        'type' => 'chunk',
                        'name' => "Rekaman $startRange - $endRange",
                        'count' => ($endRange - $startRange) + 1,
                        'chunk_number' => $i,
                    ];
                }
                $response['level'] = 'chunk';
                $response['items'] = $chunks;
                break;

            case 'minute':
                // Mengambil daftar menit yang tersedia
                $minutes = $query->select(
                        DB::raw('MINUTE(captured_at) as minute'),
                        DB::raw('count(*) as count')
                    )
                    ->reorder() // WAJIB: Hapus order lama sebelum GROUP BY
                    ->groupBy('minute')
                    ->orderBy('minute', 'desc')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'type' => 'minute',
                            'name' => 'Menit ' . str_pad($item->minute, 2, '0', STR_PAD_LEFT),
                            'count' => $item->count,
                            'minute_raw' => str_pad($item->minute, 2, '0', STR_PAD_LEFT),
                        ];
                    });
                $response['items'] = $minutes;
                break;

            case 'hour':
                // Mengambil daftar jam yang tersedia
                $hours = $query->select(
                        DB::raw('HOUR(captured_at) as hour'),
                        DB::raw('count(*) as count')
                    )
                    ->reorder() // WAJIB: Hapus order lama sebelum GROUP BY
                    ->groupBy('hour')
                    ->orderBy('hour', 'desc')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'type' => 'hour',
                            'name' => 'Jam ' . str_pad($item->hour, 2, '0', STR_PAD_LEFT) . ':00',
                            'count' => $item->count,
                            'hour_raw' => str_pad($item->hour, 2, '0', STR_PAD_LEFT),
                        ];
                    });
                $response['items'] = $hours;
                break;

            case 'date':
                // Default: Mengambil daftar tanggal (dengan pagination)
                $dates = $query->select(
                        DB::raw('DATE(captured_at) as date'),
                        DB::raw('count(*) as count')
                    )
                    ->reorder() // WAJIB: Hapus order lama sebelum GROUP BY
                    ->groupBy('date')
                    ->orderBy('date', 'desc')
                    ->paginate(30);

                $dates->getCollection()->transform(function ($item) {
                    return [
                        'type' => 'date',
                        'name' => Carbon::parse($item->date)->translatedFormat('l, j F Y'),
                        'count' => $item->count,
                        'date_raw' => $item->date,
                    ];
                });

                $response['items'] = $dates->items();
                $response['pagination'] = [
                    'total' => $dates->total(),
                    'per_page' => $dates->perPage(),
                    'current_page' => $dates->currentPage(),
                    'last_page' => $dates->lastPage(),
                    'next_page_url' => $dates->nextPageUrl(),
                ];
                break;
        }

        // Kembalikan respons JSON yang terstruktur
        return response()->json($response, 200);
    }


    /**
     * Mengganti nama file gambar yang sudah diunggah.
     * Endpoint: PUT /api/images/{id}/rename
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id (ID ImageRecord)
     */
    public function rename(Request $request, $id)
    {
        // 1. Pastikan User terotentikasi (seharusnya ditangani oleh middleware, tapi ini untuk cek)
        if (is_null($request->user())) {
            // Karena middleware Sanctum tidak bekerja dengan benar untuk PUT/PATCH di env Anda,
            // baris ini harusnya menangkap masalah 401.
            return response()->json(['message' => 'Unauthenticated: User token is invalid or missing.'], 401);
        }

        // PERBAIKAN: Fetch Model secara manual setelah otentikasi dipastikan.
        $imageRecord = ImageRecord::findOrFail($id);

        // 2. Otorisasi Awal dan Pengecekan Integritas
        $imageRecord->loadMissing('camera');

        if (is_null($imageRecord->camera)) {
             Log::error('RENAME FAILED: ImageRecord ID ' . $imageRecord->id . ' has no associated Camera (camera_id is null/invalid).');
             return response()->json(['message' => 'Record error: Associated camera not found. This record is likely corrupted.'], 409); // 409 Conflict
        }

        // 3. Otorisasi: Pastikan user yang login memiliki akses ke kamera pemilik record
        if ($request->user()->id !== $imageRecord->camera->user_id) {
             return response()->json(['message' => 'Forbidden: You do not own this image.'], 403);
        }

        // 4. Validasi Input
        $request->validate([
            'new_name' => 'required|string|max:255',
        ]);

        $oldPath = $imageRecord->path;
        $newName = $request->new_name;

        // Ambil data path untuk proses rename
        $extension = pathinfo($oldPath, PATHINFO_EXTENSION);
        $oldDirectory = pathinfo($oldPath, PATHINFO_DIRNAME);

        // --- LOGIKA RENAME SPESIFIK ---
        $sanitizedNewName = Str::slug($newName, '_');
        $newFilename = $sanitizedNewName . '_' . $imageRecord->id . '.' . $extension;
        $newPath = $oldDirectory . '/' . $newFilename;

        Log::info("Attempting rename from: {$oldPath} to: {$newPath}");

        // 5. Pindahkan/Ganti Nama File Fisik
        try {
            if (!Storage::disk('public')->exists($oldPath)) {
                 Log::error("File not found at: {$oldPath}");
                 return response()->json(['message' => 'Original file not found on disk.'], 404);
            }

            // Memindahkan file yang secara efektif mengganti namanya
            Storage::disk('public')->move($oldPath, $newPath);
            Log::info('SUCCESS: File renamed on disk.');

        } catch (\Exception $e) {
            Log::error('!!! FILE RENAME FAILED !!!', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Could not rename file.'], 500);
        }

        // 6. Perbarui Record Database
        try {
            $imageRecord->path = $newPath;
            $imageRecord->save();
            Log::info('SUCCESS: Database path updated.');

            return response()->json([
                'message' => 'Image renamed successfully',
                'new_path' => Storage::url($newPath),
                'record_id' => $imageRecord->id
            ], 200);

        } catch (\Exception $e) {
            Log::error('!!! DB UPDATE FAILED AFTER RENAME !!!', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'File renamed, but failed to update database.'], 500);
        }
    }
}
