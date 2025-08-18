<?php

namespace App\Http\Controllers\Pages\Log;

use App\Http\Controllers\Controller;
use App\Models\Camera;
use App\Models\ImageRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RiwayatRekamanController extends Controller
{
  public function index()
  {
    $cameras = Auth::user()->cameras()->latest()->paginate(10);
    return view('content.pages.Log.Riwayat_Rekaman_index', compact('cameras'));
  }

  public function showFolders(Camera $camera)
  {
    $this->authorize('update', $camera);
    return view('content.pages.Log.Riwayat_Rekaman', compact('camera'));
  }

  public function getFoldersData(Request $request, Camera $camera)
  {
    $this->authorize('update', $camera);

    $query = ImageRecord::where('camera_id', $camera->id)
      ->select(
        DB::raw('DATE(captured_at) as date'),
        DB::raw('count(*) as record_count')
      )
      ->groupBy('date');

    $totalRecords = $query->get()->count();

    // Handle sorting
    $orderColumnIndex = $request->input('order.0.column');
    $orderDir = $request->input('order.0.dir');
    $columns = $request->input('columns');
    $orderColumnName = $columns[$orderColumnIndex]['name'] ?? 'date';

    // FIX: Ganti 'group_month' dengan 'date' untuk sorting di database
    if ($orderColumnName === 'group_month') {
      $orderColumnName = 'date';
    }
    $query->orderBy($orderColumnName, $orderDir);

    // Handle pagination
    $records = $query->skip($request->input('start'))
      ->take($request->input('length'))
      ->get();

    $data = [];
    foreach ($records as $record) {
      $dateObj = \Carbon\Carbon::parse($record->date);
      $data[] = [
        'date' => $record->date,
        'group_month' => $dateObj->translatedFormat('F Y'),
        'formatted_date' => $dateObj->translatedFormat('l, j F Y'),
        'record_count' => $record->record_count . ' gambar',
        'action' => '<a href="' . route('log.history.images', ['camera' => $camera->id, 'date' => $record->date]) . '" class="btn btn-sm btn-primary">Lihat Detail</a>'
      ];
    }

    return response()->json([
      'draw' => intval($request->input('draw')),
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $totalRecords,
      'data' => $data,
    ]);
  }

  public function showImages(Request $request, Camera $camera, $date)
  {
    $this->authorize('update', $camera);
    try {
      $formattedDate = \Carbon\Carbon::parse($date)->format('Y-m-d');
    } catch (\Exception $e) {
      abort(404, 'Format tanggal tidak valid.');
    }
    return view('content.pages.Log.Riwayat_Rekaman_Detail', compact('camera', 'formattedDate'));
  }

  public function getImagesData(Request $request, Camera $camera, $date)
  {
    $this->authorize('update', $camera);
    try {
      $formattedDate = \Carbon\Carbon::parse($date)->format('Y-m-d');
    } catch (\Exception $e) {
      return response()->json(['error' => 'Format tanggal tidak valid.'], 400);
    }
    $query = ImageRecord::where('camera_id', $camera->id)
      ->whereDate('captured_at', $formattedDate);
    $totalRecords = $query->count();
    if ($request->filled('search.value')) {
      $searchValue = $request->input('search.value');
      $query->where(function ($q) use ($searchValue) {
        $q->whereTime('captured_at', 'like', '%' . $searchValue . '%');
      });
    }
    $filteredRecords = $query->count();
    $orderColumnIndex = $request->input('order.0.column');
    $orderDir = $request->input('order.0.dir');
    $columns = $request->input('columns');
    $orderColumnName = $columns[$orderColumnIndex]['name'] ?? 'captured_at';
    if ($orderColumnName === 'group_hour') {
      $query->orderByRaw('HOUR(captured_at) ' . $orderDir);
    } else {
      $query->orderBy($orderColumnName, $orderDir);
    }
    $images = $query->skip($request->input('start'))
      ->take($request->input('length'))
      ->get();
    $data = [];
    foreach ($images as $image) {
      $hour = $image->captured_at->hour;
      $data[] = [
        'id' => $image->id,
        'group_hour' => 'Jam ' . str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00 - ' . str_pad($hour, 2, '0', STR_PAD_LEFT) . ':59',
        'path' => Storage::url($image->path),
        'full_path' => Storage::url($image->path),
        'time' => $image->captured_at->format('H:i:s'),
        'action' => '',
      ];
    }
    return response()->json([
      'draw' => intval($request->input('draw')),
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $filteredRecords,
      'data' => $data,
    ]);
  }

  public function destroyFolder(Camera $camera, $date)
  {
    $this->authorize('delete', $camera);
    try {
      $formattedDate = \Carbon\Carbon::parse($date)->format('Y-m-d');
    } catch (\Exception $e) {
      return back()->with('error', 'Format tanggal tidak valid.');
    }
    ImageRecord::where('camera_id', $camera->id)
      ->whereDate('captured_at', $formattedDate)
      ->delete();
    $directory = "camera_images/{$camera->device_id}/{$formattedDate}";
    Storage::disk('public')->deleteDirectory($directory);
    return back()->with('success', 'Semua rekaman untuk tanggal ' . $formattedDate . ' berhasil dihapus.');
  }
}
