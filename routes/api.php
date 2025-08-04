<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ImageUploadController;

// Route::get('/user', function (Request $request) {
//   return $request->user();
// })->middleware('auth:sanctum');


Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
  Route::post('/logout', [AuthController::class, 'logout']);
  // rute-rute API terproteksi lainnya
});
Route::post('/upload', [ImageUploadController::class, 'store']);
