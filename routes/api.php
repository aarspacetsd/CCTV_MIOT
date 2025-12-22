<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileApiController; // <-- Import Controller untuk Profile API
use App\Http\Controllers\Api\ImageUploadController;
use App\Http\Controllers\Api\LatestImageController;
use App\Http\Controllers\Api\HeartbeatController; // <-- Tambahkan ini
use App\Http\Controllers\Api\ImageHistoryController;
use App\Http\Controllers\Api\ForgotPasswordController;
use App\Http\Controllers\Api\UserCameraGroupApiController;
use App\Http\Controllers\Api\UserCameraApiController;


// Route::get('/user', function (Request $request) {
//   return $request->user();
// })->middleware('auth:sanctum');

// --- Route Otentikasi Publik ---
//udah jadi
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
// Password Reset API (Langkah 1: Mengirim Email)
Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail']);

// Password Reset API (Langkah 2: Reset Password)
Route::post('/password/reset', [ForgotPasswordController::class, 'reset']);

// --- Route Terproteksi (auth:sanctum) ---
Route::middleware('auth:sanctum')->group(function () {

  // 1. Auth & Logout
  Route::post('/logout', [AuthController::class, 'logout']);

  // 2. Profile API (Menggunakan ProfileApiController)
  Route::get('/profile', [ProfileApiController::class, 'show']); // Ambil data profil
  Route::patch('/profile', [ProfileApiController::class, 'updateProfile']); // Update nama & email
  Route::put('/password', [ProfileApiController::class, 'updatePassword']); // Update password
  Route::delete('/profile', [ProfileApiController::class, 'destroy']); // Hapus akun
  Route::get('/images/{camera}/history', [ImageHistoryController::class, 'historyExplorer']);

  // Endpoint lain (biarkan seperti adanya jika sudah benar)
// image rename masih error 401
  Route::put('/images/{imageRecord}/rename', [ImageUploadController::class, 'rename']);

  Route::get('/cameras/{camera}/latest-image', LatestImageController::class);
// belum di uji api nya untuk user add,manage kamera
  Route::prefix('user/cameras')->group(function () {
        Route::get('/', [UserCameraApiController::class, 'index']);      // List Kamera
        Route::post('/', [UserCameraApiController::class, 'store']);     // Link Kamera Baru
        Route::get('/{id}', [UserCameraApiController::class, 'show']);   // Detail Kamera
        Route::put('/{id}', [UserCameraApiController::class, 'update']); // Edit Kamera
        Route::delete('/{id}', [UserCameraApiController::class, 'destroy']); // Unlink Kamera
    });
  // === API GROUPING KAMERA === belum uji
  Route::prefix('user/camera-groups')->group(function () {

        // 1. Ambil semua grup (Master) dan kamera ungrouped
        // GET /api/user/camera-groups
        Route::get('/', [UserCameraGroupApiController::class, 'index']);

        // 2. Buat grup baru
        // POST /api/user/camera-groups
        Route::post('/', [UserCameraGroupApiController::class, 'store']);

        // 3. Perbarui nama grup menggunakan nama lama (tanpa ID grup)
        // POST /api/user/camera-groups/update
        Route::post('/update', [UserCameraGroupApiController::class, 'update']);

        // 4. Hapus grup menggunakan nama grup
        // POST /api/user/camera-groups/delete
        Route::post('/delete', [UserCameraGroupApiController::class, 'destroy']);

        // 5. Masukkan kamera ke grup berdasarkan Nama Grup
        // POST /api/user/camera-groups/assign
        Route::post('/assign', [UserCameraGroupApiController::class, 'assignCamera']);

        // 6. Keluarkan kamera dari grup manapun (Set ke Ungrouped)
        // POST /api/user/camera-groups/remove
        Route::post('/remove', [UserCameraGroupApiController::class, 'removeCamera']);

    });
});
  Route::put('/images/{imageRecord}/rename', [ImageHistoryController::class, 'rename']);


// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//   return $request->user();
// }); // <--- Dihapus/Dikomendasikan karena digantikan GET /profile
  Route::post('/camera/upload', [ImageUploadController::class, 'store']);

// Endpoint untuk menerima sinyal heartbeat dari perangkat
Route::post('/heartbeat', HeartbeatController::class);
