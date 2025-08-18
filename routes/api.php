<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ImageUploadController;
use App\Http\Controllers\Api\LatestImageController;


// Route::get('/user', function (Request $request) {
//   return $request->user();
// })->middleware('auth:sanctum');


Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
  Route::post('/logout', [AuthController::class, 'logout']);
  // rute-rute API terproteksi lainnya
});
Route::post('/upload', [ImageUploadController::class, 'store']);
Route::get('/cameras/{camera}/latest-image', LatestImageController::class);

use App\Http\Controllers\Api\HeartbeatController; // <-- Tambahkan ini

// ...

// Endpoint untuk menerima sinyal heartbeat dari perangkat
Route::post('/heartbeat', HeartbeatController::class);
