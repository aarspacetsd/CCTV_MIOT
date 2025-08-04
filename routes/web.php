<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Dashboard
use App\Http\Controllers\Pages\DashboardController;
// --- Controllers Modul Admin (Pages/Admin/) ---
use App\Http\Controllers\Pages\Admin\ManajemenKameraController;
use App\Http\Controllers\Pages\Admin\NotifikasiPeringatanController;
// --- Controllers Modul Report (Pages/Log/) ---
use App\Http\Controllers\Pages\Log\LogAktifitasController;
use App\Http\Controllers\Pages\Log\RiwayatRekamanController;
use App\Http\Controllers\Pages\Log\RiwayatRekamanDetailController;
// --- Controllers Modul Invoice/Bill (Pages/ML/) ---
use App\Http\Controllers\Pages\ML\LogDeteksiMlController;
// --- Controllers Modul Setting (Pages/Setting/) ---
use App\Http\Controllers\Pages\Setting\UserController;
use App\Http\Controllers\Pages\Setting\MenuController;
use App\Http\Controllers\Pages\Setting\HospitalSettingController;
use App\Http\Controllers\Pages\Setting\RoleController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
  if (auth()->check()) {
    return redirect()->route('dashboard.index');
  }
  return view('auth.login');
});

// --- Route Group Utama untuk Dashboard ---
Route::middleware(['auth', 'verified'])->prefix('dashboard')->group(function () {

  Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');

  // Grup untuk URL /dashboard/admin/*
  Route::prefix('admin')->group(function () {
    Route::resource('cameras', ManajemenKameraController::class);
    Route::get('/notifications', [NotifikasiPeringatanController::class, 'index'])->name('notifications.index');
  });

  // Grup untuk URL /dashboard/ml/*
  Route::prefix('ml')->name('ml.')->group(function () {
    // Route untuk Log Deteksi ML bisa ditambahkan di sini
    Route::get('/detection-log', [LogDeteksiMlController::class, 'index'])->name('detection-log.index');
  });

  // Grup untuk URL /dashboard/log/*
  Route::prefix('log')->name('log.')->group(function () {

    // Route baru untuk halaman utama Riwayat Rekaman (daftar kamera)
    Route::get('/history', [RiwayatRekamanController::class, 'index'])->name('history.index');
    Route::get('/activities', [LogAktifitasController::class, 'index'])->name('activities.index');

    // Grup untuk riwayat spesifik per kamera
    Route::prefix('cameras/{camera}/history')->name('history.')->group(function () {
      // Route untuk menampilkan daftar folder per tanggal
      Route::get('/', [RiwayatRekamanController::class, 'showFolders'])->name('folders');

      // Route untuk menampilkan daftar gambar pada tanggal tertentu
      Route::get('/{date}', [RiwayatRekamanController::class, 'showImages'])->name('images');
    });
  });

  // Grup untuk URL /dashboard/settings/*
  Route::prefix('settings')->group(function () {
    // Route untuk Manajemen Pengguna & Role bisa ditambahkan di sini
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
  });
}); // <-- Ini adalah penutup yang hilang untuk grup 'dashboard'

Route::middleware('auth')->group(function () {
  Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
  Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
  Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Route otentikasi dari Laravel Breeze
require __DIR__ . '/auth.php';

// Route ini sebaiknya berada di file routes/api.php
Route::get('/api/test', function () {
  return response()->json(['message' => 'API Test Berhasil!']);
});
