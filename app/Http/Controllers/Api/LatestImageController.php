<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Camera;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LatestImageController extends Controller
{
  /**
   * Mengambil URL gambar terbaru untuk kamera tertentu.
   */
  public function __invoke(Camera $camera)
  {
    // Ambil satu rekaman gambar terbaru untuk kamera ini
    $latestImage = $camera->imageRecords()->latest('captured_at')->first();

    if ($latestImage) {
      return response()->json([
        'success' => true,
        'image_url' => Storage::url($latestImage->path),
        'captured_at' => $latestImage->captured_at->format('H:i:s') . ' WIB',
      ]);
    }

    // Jika tidak ada gambar, kirim respons yang sesuai
    return response()->json([
      'success' => false,
      'image_url' => 'https://placehold.co/600x400/293445/FFFFFF?text=No+Image',
      'captured_at' => 'N/A',
    ]);
  }
}
