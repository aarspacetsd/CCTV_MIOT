<?php

namespace App\Http\Controllers\Pages\Admin;

use App\Http\Controllers\Controller;
use App\Models\Camera;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class ManajemenKameraController extends Controller
{
  /**
   * Menampilkan daftar semua kamera.
   */
  public function index()
  {
    $cameras = Auth::user()->cameras()->latest()->paginate(10);
    return view('content.pages.admin.Manajemen_Kamera', [
      'view' => 'index',
      'cameras' => $cameras
    ]);
  }

  /**
   * Menampilkan formulir untuk membuat kamera baru.
   */
  public function create()
  {
    return view('content.pages.admin.Manajemen_Kamera', ['view' => 'create']);
  }

  /**
   * Menyimpan kamera baru ke database.
   */
  public function store(Request $request)
  {
    $this->authorize('create', Camera::class);

    $request->validate([
      'name' => 'required|string|max:255',
      'description' => 'nullable|string',
    ]);

    // FIX: Buat objek Camera secara manual untuk memastikan user_id diatur.
    $camera = new Camera();
    $camera->fill($request->only('name', 'description')); // Isi nama dan deskripsi
    $camera->user_id = Auth::id(); // Atur user_id secara eksplisit
    $camera->device_id = Str::uuid();
    $camera->api_key = Str::random(60);
    $camera->websocket_channel_id = 'camera-status-' . Str::random(16);
    $camera->save(); // Simpan model ke database

    return redirect()->route('admin.cameras.edit', $camera->id)
      ->with('success', 'Kamera berhasil didaftarkan!')
      ->with('newCamera', $camera);
  }

  /**
   * Menampilkan formulir untuk mengedit kamera.
   */
  public function edit(Camera $camera)
  {
    $this->authorize('update', $camera);
    return view('content.pages.admin.Manajemen_Kamera', [
      'view' => 'edit',
      'camera' => $camera
    ]);
  }

  /**
   * Memperbarui data kamera di database.
   */
  public function update(Request $request, Camera $camera)
  {
    $this->authorize('update', $camera);

    $request->validate([
      'name' => 'required|string|max:255',
      'description' => 'nullable|string',
      'is_active' => 'required|boolean',
    ]);

    $camera->update($request->all());

    // FIX: Menggunakan nama route yang benar
    return redirect()->route('admin.cameras.index')->with('success', 'Data kamera berhasil diperbarui.');
  }

  /**
   * Menghapus kamera dari database.
   */
  public function destroy(Camera $camera)
  {
    $this->authorize('delete', $camera);

    $camera->delete();

    // FIX: Menggunakan nama route yang benar
    return redirect()->route('admin.cameras.index')->with('success', 'Kamera berhasil dihapus.');
  }
}
