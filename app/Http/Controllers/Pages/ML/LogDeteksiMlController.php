<?php

namespace App\Http\Controllers\Pages\ML;

use App\Http\Controllers\Controller;
use App\Models\ImageRecord;
use Illuminate\Http\Request;

class LogDeteksiMlController extends Controller
{
  /**
   * Menampilkan halaman log deteksi dari machine learning.
   */
  public function index()
  {
    // Ambil data rekaman gambar yang memiliki hasil deteksi.
    // Untuk saat ini, kita akan mengambil semua gambar sebagai contoh.
    // Nantinya, Anda bisa menambahkan ->whereNotNull('detection_result')
    $detectionLogs = ImageRecord::with('camera') // Eager load relasi kamera
      ->latest('captured_at') // Urutkan dari yang terbaru
      ->paginate(15); // Tampilkan 15 deteksi per halaman

    return view('content.pages.ML.Log_Deteksi_ML', compact('detectionLogs'));
  }
}
