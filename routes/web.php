<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

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

// --- Route Group Utama untuk Dashboard ---
Route::middleware(['auth', 'verified'])->prefix('dashboard')->group(function () {

  // Rute untuk Admin Dashboard
  Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');

  // Grup untuk URL /dashboard/admin/*
  Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('cameras', ManajemenKameraController::class);
    Route::get('/notifications', [NotifikasiPeringatanController::class, 'index'])->name('notifications.index');
  });

  // Grup untuk URL /dashboard/ml/*
  Route::prefix('ml')->name('ml.')->group(function () {
    Route::get('/detection-log', [LogDeteksiMlController::class, 'index'])->name('detection-log.index');
  });

  // Grup untuk URL /dashboard/log/*
  Route::prefix('log')->name('log.')->group(function () {
    Route::get('/history', [RiwayatRekamanController::class, 'index'])->name('history.index');
    Route::get('/activities', [LogAktifitasController::class, 'index'])->name('activities.index');
    Route::prefix('cameras/{camera}/history')->name('history.')->group(function () {
      Route::get('/', [RiwayatRekamanController::class, 'showFolders'])->name('folders');
      Route::get('/folders-data', [RiwayatRekamanController::class, 'getFoldersData'])->name('folders.data');
      Route::get('/{date}', [RiwayatRekamanController::class, 'showImages'])->name('images');
      Route::get('/{date}/data', [RiwayatRekamanController::class, 'getImagesData'])->name('data');
      Route::delete('/{date}', [RiwayatRekamanController::class, 'destroyFolder'])->name('destroyFolder');
    });
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

Route::middleware('auth')->group(function () {
  Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
  Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
  Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Route otentikasi dari Laravel Breeze
require __DIR__ . '/auth.php';
