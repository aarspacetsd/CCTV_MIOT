<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\User;
use App\Jobs\CleanupUserImagesJob;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

// Perintah bawaan Laravel untuk inspirasi
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

/**
 * PENJADWALAN OTOMATIS (Laravel Scheduler)
 * * Alternatif Performa Tinggi:
 * Alih-alih menjalankan logika berat langsung di sini, kita melakukan 'Dispatch' ke Queue.
 * Cron Job (schedule:run) akan memicu ini setiap hari pukul 00:00.
 * Tugas berat akan dikerjakan oleh PHP Worker di background.
 */
Schedule::call(function () {
    // Ambil semua user yang punya kamera
    $users = User::has('cameras')->get();

    foreach ($users as $user) {
        // Masukkan ke antrean (Queue) per user agar tidak memberatkan satu proses
        CleanupUserImagesJob::dispatch($user);
    }
})->daily();
