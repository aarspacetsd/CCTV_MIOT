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
    // Mengirimkan variabel 'view' => 'index' untuk logika di Blade
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
    // Mengirimkan variabel 'view' => 'create'
    return view('content.pages.admin.Manajemen_Kamera', ['view' => 'create']);
  }

  /**
   * Menyimpan kamera baru ke database.
   */
  public function store(Request $request)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'description' => 'nullable|string',
    ]);

    $camera = Auth::user()->cameras()->create([
      'device_id' => Str::uuid(),
      'name' => $request->name,
      'description' => $request->description,
      'api_key' => Str::random(60),
    ]);

    // Redirect ke halaman edit, yang akan menampilkan form edit
    // dan juga informasi kamera baru.
    return redirect()->route('cameras.edit', $camera->id)
      ->with('success', 'Kamera berhasil didaftarkan!')
      ->with('newCamera', $camera);
  }

  /**
   * Menampilkan formulir untuk mengedit kamera.
   */
  public function edit(Camera $camera)
  {
    $this->authorize('update', $camera);
    // Mengirimkan variabel 'view' => 'edit'
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

    // Redirect kembali ke halaman index (daftar kamera)
    return redirect()->route('cameras.index')->with('success', 'Data kamera berhasil diperbarui.');
  }

  /**
   * Menghapus kamera dari database.
   */
  public function destroy(Camera $camera)
  {
    $this->authorize('delete', $camera);

    $camera->delete();

    return redirect()->route('cameras.index')->with('success', 'Kamera berhasil dihapus.');
  }
}
