<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\ImageRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CleanupUserImagesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Jumlah percobaan jika job gagal.
     */
    public $tries = 3;

    /**
     * Waktu tunggu (detik) sebelum mencoba ulang jika gagal.
     */
    public $backoff = 60;

    /**
     * Batas waktu eksekusi job (detik).
     */
    public $timeout = 600;

    protected $user;

    /**
     * Buat instance job baru.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Eksekusi job (Dijalankan oleh PHP Worker).
     */
    public function handle(): void
    {
        // 1. Tentukan batas waktu berdasarkan setting user (default 30 hari)
        $days = $this->user->retention_days ?? 30;
        $thresholdDate = Carbon::now()->subDays($days);

        // 2. Ambil semua ID kamera milik user ini
        $cameraIds = $this->user->cameras()->pluck('id');

        if ($cameraIds->isEmpty()) {
            return;
        }

        // 3. Cari record gambar lama yang dimiliki kamera user ini
        $oldRecords = ImageRecord::whereIn('camera_id', $cameraIds)
                                 ->where('captured_at', '<', $thresholdDate);

        $count = $oldRecords->count();

        if ($count > 0) {
            Log::info("START_CLEANUP: Memproses {$count} gambar untuk user {$this->user->name} (Retensi: {$days} hari)");

            // 4. Gunakan chunkById untuk menjaga stabilitas RAM
            $oldRecords->chunkById(100, function ($records) {
                foreach ($records as $record) {
                    try {
                        // Hapus file fisik di MinIO (Disk S3)
                        if (Storage::disk('s3')->exists($record->path)) {
                            Storage::disk('s3')->delete($record->path);
                        }

                        // Hapus record di database
                        $record->delete();
                    } catch (\Exception $e) {
                        Log::error("Cleanup Error [Image ID: {$record->id}]: " . $e->getMessage());
                    }
                }
            });

            Log::info("END_CLEANUP: Berhasil membersihkan gambar user {$this->user->name}");
        }
    }

    /**
     * Menangani kegagalan permanen pada job.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("CRITICAL_CLEANUP_FAILED: Job gagal permanen untuk User ID {$this->user->id}. Pesan: " . $exception->getMessage());
    }
}
