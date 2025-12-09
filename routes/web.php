<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;

// Dashboard
use App\Http\Controllers\Pages\UserDashboardController;
use App\Http\Controllers\Pages\DashboardController;
use App\Http\Controllers\Pages\CameraGroupController;
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

// Route 'dashboard' utama
Route::get('/dashboard', function () {
    if (auth()->check()) {
        if (auth()->user()->hasRole('admin')) {
            return redirect()->route('dashboard.index');
        }
        return redirect()->route('user.dashboard');
    }
    return redirect()->route('login');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/', function () {
  if (auth()->check()) {
    if (auth()->user()->hasRole('admin')) {
      return redirect()->route('dashboard.index');
    }
    return redirect()->route('user.dashboard');
  }
  return view('auth.login');
});

// --- API internal ---
Route::middleware(['auth'])->prefix('api')->group(function () {
    Route::get('/camera-statuses', [ApiController::class, 'getCameraStatuses'])->name('api.statuses');
});


// --- Route Group Utama untuk Dashboard ---
Route::middleware(['auth', 'verified'])->prefix('dashboard')->group(function () {

  // Rute untuk Admin Dashboard
  Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
  Route::post('/groups', [DashboardController::class, 'updateGroups'])->name('dashboard.groups');

  // Grup untuk URL /dashboard/admin/*
  Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('cameras', ManajemenKameraController::class);
    Route::get('cameras/{camera}/qrcode', [ManajemenKameraController::class, 'downloadQrCode'])
      ->name('cameras.qrcode');
    Route::get('/notifications', [NotifikasiPeringatanController::class, 'index'])->name('notifications.index');

    // Routes untuk manajemen grup kamera (Admin)
    Route::prefix('camera-groups')->name('camera-groups.')->group(function () {
      Route::get('/', [CameraGroupController::class, 'index'])->name('index');
      Route::post('/', [CameraGroupController::class, 'store'])->name('store');
      Route::put('/{groupName}', [CameraGroupController::class, 'update'])->name('update');
      Route::delete('/{groupName}', [CameraGroupController::class, 'destroy'])->name('destroy');
      Route::post('/assign', [CameraGroupController::class, 'assignCamera'])->name('assign');
      Route::post('/remove', [CameraGroupController::class, 'removeCamera'])->name('remove');
    });
  });

  // Grup untuk URL /dashboard/ml/*
  Route::prefix('ml')->name('ml.')->group(function () {
    Route::get('/detection-log', [LogDeteksiMlController::class, 'index'])->name('ml.detection-log.index');
  });

  // Grup untuk URL /dashboard/log/*
  Route::prefix('log')->name('log.')->group(function () {
    Route::get('/activities', [LogAktifitasController::class, 'index'])->name('activities.index');
    Route::get('/history', [RiwayatRekamanController::class, 'index'])->name('history.index');
    Route::get('/history/kamera/{camera}/{date?}/{hour?}/{minute?}/{chunk?}', [RiwayatRekamanController::class, 'showExplorer'])
      ->name('history.explorer')
      ->where([
        'date'   => '[0-9]{4}-[0-9]{2}-[0-9]{2}',
        'hour'   => '[0-9]{2}',
        'minute' => '[0-9]{2}',
        'chunk'  => '[0-9]+',
      ]);
    Route::delete('/history/kamera/{camera}/hapus-folder', [RiwayatRekamanController::class, 'destroyFolder'])
      ->name('history.destroy.folder');
  });

  // Grup untuk URL /dashboard/settings/*
  Route::prefix('settings')->name('settings.')->group(function () {
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
  });

  // Grup untuk semua fitur khusus pengguna (USER BIASA)
  Route::prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
    Route::post('/groups', [UserDashboardController::class, 'updateGroups'])->name('dashboard.groups');

    // ✅ TAMBAHAN: Routes manajemen grup kamera khusus User
    // URL: /dashboard/user/camera-groups
    Route::prefix('camera-groups')->name('camera-groups.')->group(function () {
        Route::get('/', [CameraGroupController::class, 'index'])->name('index');
        Route::post('/', [CameraGroupController::class, 'store'])->name('store');
        Route::put('/{groupName}', [CameraGroupController::class, 'update'])->name('update');
        Route::delete('/{groupName}', [CameraGroupController::class, 'destroy'])->name('destroy');
        Route::post('/assign', [CameraGroupController::class, 'assignCamera'])->name('assign');
        Route::post('/remove', [CameraGroupController::class, 'removeCamera'])->name('remove');
    });

    // Route untuk menautkan kamera
    Route::prefix('cameras')->name('cameras.')->group(function () {
      Route::get('/link', [UserCameraLinkController::class, 'create'])->name('link.create');
      Route::post('/link', [UserCameraLinkController::class, 'store'])->name('link.store');
    });

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
  Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('password.update');
  Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
