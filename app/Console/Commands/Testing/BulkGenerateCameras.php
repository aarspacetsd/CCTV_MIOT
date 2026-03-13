<?php

namespace App\Console\Commands\Testing;

use App\Models\Camera;
use App\Models\User;
use App\Services\EmqxService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class BulkGenerateCameras extends Command
{
    /**
     * Nama dan signature dari command.
     * --clean: Flag opsional untuk menghapus kamera test yang sudah ada.
     */
    protected $signature = 'camera:bulk-generate
                            {user_id? : ID user yang akan memiliki kamera ini (wajib jika tidak --clean)}
                            {count=50 : Jumlah kamera yang ingin dibuat}
                            {--clean : Hapus semua kamera yang memiliki deskripsi "Generated for stress testing"}';

    /**
     * Deskripsi command.
     */
    protected $description = 'Generate kamera secara masal untuk testing dan membuat file credentials.txt';

    /**
     * Eksekusi command.
     */
    public function handle(EmqxService $emqx)
    {
        // Fitur 1: Hapus Kamera Test (Cleanup)
        if ($this->option('clean')) {
            $this->info("Sedang membersihkan data kamera test...");

            $query = Camera::where('description', 'Generated for stress testing');
            $countDeleted = $query->count();

            if ($countDeleted === 0) {
                $this->warn("Tidak ada kamera test yang ditemukan untuk dihapus.");
            } else {
                $query->delete();
                $this->info("✅ Berhasil menghapus {$countDeleted} kamera test.");

                // Sinkronisasi EMQX setelah penghapusan
                try {
                    $emqx->syncAll();
                    $this->info("✅ EMQX Sync Berhasil setelah pembersihan.");
                } catch (\Exception $e) {
                    $this->error("❌ EMQX Sync Gagal: " . $e->getMessage());
                }
            }

            // Jika user hanya ingin clean, kita stop di sini
            if (!$this->argument('user_id')) {
                return 0;
            }
        }

        // Fitur 2: Generate Kamera
        $userId = $this->argument('user_id');
        $count = (int) $this->argument('count');

        $user = User::find($userId);
        if (!$user) {
            $this->error("User dengan ID {$userId} tidak ditemukan! Masukkan ID User untuk membuat kamera.");
            return 1;
        }

        $this->info("Sedang membuat {$count} kamera untuk user: {$user->name}...");

        $credentials = [];
        $bar = $this->output->createProgressBar($count);
        $bar->start();

        for ($i = 1; $i <= $count; $i++) {
            $cameraName = "Cam-Test-" . str_pad($i, 3, '0', STR_PAD_LEFT);

            $camera = new Camera();
            $camera->name = $cameraName;
            $camera->description = "Generated for stress testing";
            $camera->user_id = $user->id;
            $camera->websocket_channel_id = 'camera-status-' . Str::random(16);

            // Pastikan device_id diisi jika tidak otomatis oleh model
            if (empty($camera->device_id)) {
                $camera->device_id = (string) Str::uuid();
            }

            /**
             * PERBAIKAN: Mengubah 'mqtt_user' menjadi 'mqtt_username'
             * agar sesuai dengan skema database di model Camera.
             */
            $camera->mqtt_username = "mqtt_" . Str::random(20);
            $camera->mqtt_password = Str::random(40);

            // Simpan ke database
            $camera->save();

            /**
             * PENTING: Ambil data dari properti model yang benar.
             */
            $credentials[] = "{$camera->device_id},{$camera->mqtt_username},{$camera->mqtt_password}";

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        // Trigger Sinkronisasi EMQX
        $this->info("Menyinkronkan ke EMQX...");
        try {
            $emqx->syncAll();
            $this->info("✅ EMQX Sync Berhasil.");
        } catch (\Exception $e) {
            $this->error("❌ EMQX Sync Gagal: " . $e->getMessage());
        }

        // Tulis ke file credentials.txt
        $filePath = base_path('credentials.txt');
        file_put_contents($filePath, implode("\n", $credentials));

        $this->info("✅ Berhasil membuat {$count} kamera!");
        $this->info("File kredensial tersimpan di: " . $filePath);

        return 0;
    }
}
