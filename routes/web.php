<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;

// Dashboard
use App\Http\Controllers\Pages\UserDashboardController;
use App\Http\Controllers\Pages\DashboardController;
// --- Controllers Modul Admin (Pages/Admin/) ---
use App\Http\Controllers\Pages\Admin\ManajemenKameraController;
use App\Http\Controllers\Pages\Admin\NotifikasiPeringatanController;
// --- Controllers Modul Report (Pages/Log/) ---
use App\Http\Controllers\Pages\Log\LogAktifitasController;
use App\Http\Controllers\Pages\Log\RiwayatRekamanController;
// --- Controllers Modul Invoice/Bill (Pages/ML/) ---
use App\Http\Controllers\Pages\ML\LogDeteksiMlController;
// --- Controllers Modul Setting (Pages/Setting/) ---
use App\Http\Controllers\Pages\Setting\UserController;
use App\Http\Controllers\Pages\Setting\RoleController;
// --- Controllers Modul User (Pages/User/) ---
use App\Http\Controllers\Pages\User\UserCameraLinkController;
use App\Http\Controllers\Pages\User\UserManajemenKameraController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
  if (auth()->check()) {
    // Arahkan ke dashboard admin jika rolenya admin, jika tidak ke user dashboard
    if (auth()->user()->hasRole('admin')) {
      return redirect()->route('dashboard.index');
    }
    return redirect()->route('user.dashboard');
  }
  return view('auth.login');
});

// --- [BARU] Grup untuk API internal yang dipanggil dari frontend (misal: dashboard) ---
Route::middleware(['auth'])->prefix('api')->group(function () {
    Route::get('/camera-statuses', [ApiController::class, 'getCameraStatuses'])->name('api.statuses');
});


// --- Route Group Utama untuk Dashboard ---
Route::middleware(['auth', 'verified'])->prefix('dashboard')->group(function () {

  // Rute untuk Admin Dashboard
  Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');

  // Grup untuk URL /dashboard/admin/*
  Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('cameras', ManajemenKameraController::class);
    Route::get('cameras/{camera}/qrcode', [ManajemenKameraController::class, 'downloadQrCode'])
      ->name('cameras.qrcode');
    Route::get('/notifications', [NotifikasiPeringatanController::class, 'index'])->name('notifications.index');
  });

  // Grup untuk URL /dashboard/ml/*
  Route::prefix('ml')->name('ml.')->group(function () {
    Route::get('/detection-log', [LogDeteksiMlController::class, 'index'])->name('detection-log.index');
  });

  // Grup untuk URL /dashboard/log/*
  Route::prefix('log')->name('log.')->group(function () {
    // Rute ini tidak berhubungan dengan riwayat, jadi biarkan saja
    Route::get('/activities', [LogAktifitasController::class, 'index'])->name('activities.index');

    // --- MULAI MODIFIKASI RIWAYAT REKAMAN ---

    // 1. Rute untuk halaman "Pilih Kamera" (URL: /log/history)
    // Ini tetap sama, hanya mengarah ke controller.
    Route::get('/history', [RiwayatRekamanController::class, 'index'])->name('history.index');

    // 2. Rute Explorer Dinamis yang BARU (Menggantikan 5 rute lama)
    // URL-nya akan menjadi: /log/history/kamera/{id}/{date?}/{hour?}/{minute?}
    Route::get('/history/kamera/{camera}/{date?}/{hour?}/{minute?}/{chunk?}', [RiwayatRekamanController::class, 'showExplorer'])
      ->name('history.explorer')
      ->where([
        'date'   => '[0-9]{4}-[0-9]{2}-[0-9]{2}',
        'hour'   => '[0-9]{2}',
        'minute' => '[0-9]{2}',
        'chunk'  => '[0-9]+', // Validasi untuk chunk (harus angka)
      ]);

    // 3. Rute untuk menghapus folder berdasarkan tanggal yang BARU
    // URL-nya akan menjadi: /log/history/kamera/{id}/hapus-folder
    Route::delete('/history/kamera/{camera}/hapus-folder', [RiwayatRekamanController::class, 'destroyFolder'])
      ->name('history.destroy.folder'); // Nama baru untuk rute hapus

    // 4. CATATAN: Semua rute lama di dalam `prefix('cameras/{camera}/history')`
    // sudah tidak diperlukan lagi dan bisa dihapus dengan aman.
    // Rute-rute seperti `folders`, `folders.data`, `images`, dll.,
    // semuanya sudah ditangani oleh satu rute 'history.explorer' di atas.

    // --- SELESAI MODIFIKASI ---
  });

  // Grup untuk URL /dashboard/settings/*
  Route::prefix('settings')->name('settings.')->group(function () {
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
  });

  // --- [BARU] Grup untuk semua fitur khusus pengguna ---
  Route::prefix('user')->name('user.')->group(function () {
    // Route untuk dashboard pengguna
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');

    // Route untuk menautkan kamera
    Route::prefix('cameras')->name('cameras.')->group(function () {
      Route::get('/link', [UserCameraLinkController::class, 'create'])->name('link.create');
      Route::post('/link', [UserCameraLinkController::class, 'store'])->name('link.store');
    });

    // Route baru untuk manajemen kamera milik pengguna
    Route::get('/my-cameras/data', [UserManajemenKameraController::class, 'getData'])->name('my-cameras.data');
    Route::resource('my-cameras', UserManajemenKameraController::class)->except(['create', 'store', 'show']);
  });
});
Route::get('/test-reverb', function () {
  return view('test-reverb');
});

Route::middleware('auth')->group(function () {
  Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
  Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
  Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Route otentikasi dari Laravel Breeze
require __DIR__ . '/auth.php';
