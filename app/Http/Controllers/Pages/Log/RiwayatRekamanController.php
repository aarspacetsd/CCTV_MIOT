<?php

namespace App\Http\Controllers\Pages\Log;

use App\Http\Controllers\Controller;
use App\Models\Camera;
use App\Models\ImageRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RiwayatRekamanController extends Controller
{
  /**
   * Menampilkan daftar kamera untuk dipilih.
   * Ini adalah titik awal baru untuk riwayat rekaman.
   */
  public function index()
  {
    $cameras = Auth::user()->cameras()->latest()->paginate(10);
    return view('content.pages.Log.Riwayat_Rekaman_index', compact('cameras'));
  }

  /**
   * Menampilkan daftar folder (dikelompokkan per tanggal) untuk kamera tertentu.
   */
  public function showFolders(Camera $camera)
  {
    $this->authorize('update', $camera);

    $dates = ImageRecord::where('camera_id', $camera->id)
      ->select(DB::raw('DATE(captured_at) as date'))
      ->distinct()
      ->orderBy('date', 'desc')
      ->paginate(15);

    return view('content.pages.Log.Riwayat_Rekaman', compact('camera', 'dates'));
  }

  /**
   * Menampilkan daftar gambar pada tanggal tertentu untuk kamera tertentu.
   */
  public function showImages(Request $request, Camera $camera, $date)
  {
    $this->authorize('update', $camera);

    try {
      $formattedDate = \Carbon\Carbon::parse($date)->format('Y-m-d');
    } catch (\Exception $e) {
      abort(404, 'Format tanggal tidak valid.');
    }

    $images = ImageRecord::where('camera_id', $camera->id)
      ->whereDate('captured_at', $formattedDate)
      ->orderBy('captured_at', 'desc')
      ->paginate(20);

    return view('content.pages.Log.Riwayat_Rekaman_Detail', compact('camera', 'images', 'formattedDate'));
  }
}
