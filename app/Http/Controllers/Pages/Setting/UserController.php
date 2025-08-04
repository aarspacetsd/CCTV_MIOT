<?php

namespace App\Http\Controllers\Pages\Setting;

use App\Http\Controllers\Controller;
use App\Models\User; // Pastikan model User sudah ada
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role; // Import model Role dari Spatie

class UserController extends Controller
{
  public function __construct()
  {
    // Hanya Admin yang bisa mengakses semua metode di controller ini
    $this->middleware(['auth', 'role:admin']);
  }

  /**
   * Menampilkan daftar semua user dan/atau form tambah/edit berdasarkan query parameter.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $users = User::with('roles')->latest()->get(); // Selalu ambil daftar user
    $roles = Role::all(); // Selalu ambil semua role untuk form

    $showCreateForm = false;
    $showEditForm = false;
    $editingUser = null;
    $userRoles = [];

    // Cek query parameter 'action'
    if ($request->query('action') === 'create') {
      $showCreateForm = true;
    } elseif ($request->query('action') === 'edit' && $request->query('user_id')) {
      $editingUser = User::with('roles')->find($request->query('user_id'));
      if ($editingUser) {
        $showEditForm = true;
        $userRoles = $editingUser->roles->pluck('name')->toArray();
      } else {
        // Jika user tidak ditemukan, redirect kembali ke daftar dengan pesan error
        return redirect()->route('users.index')->with('error', 'User yang ingin diedit tidak ditemukan.');
      }
    }

    // Kirim semua data yang mungkin dibutuhkan ke view
    return view('content.pages.settings.manageuser', compact(
      'users',
      'roles',
      'showCreateForm',
      'showEditForm',
      'editingUser',
      'userRoles'
    ));
  }

  /**
   * Mengarahkan ke halaman index dengan flag untuk menampilkan form tambah.
   * Ini akan dipanggil oleh tombol "Tambah User Baru".
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    return redirect()->route('users.index', ['action' => 'create']);
  }

  /**
   * Menyimpan user baru ke database.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:users',
      'password' => 'required|string|min:8|confirmed',
      'roles' => 'required|string|exists:roles,name', // Diubah dari array ke string
    ]);

    $user = User::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => Hash::make($request->password),
    ]);

    // Menggunakan assignRole untuk satu role
    $user->assignRole($request->roles);

    return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
  }

  /**
   * Mengarahkan ke halaman index dengan flag untuk menampilkan form edit.
   * Ini akan dipanggil oleh tombol "Edit" di tabel.
   *
   * @param  \App\Models\User  $user
   * @return \Illuminate\Http\Response
   */
  public function edit(User $user)
  {
    return redirect()->route('users.index', ['action' => 'edit', 'user_id' => $user->id]);
  }

  /**
   * Memperbarui user yang ada di database.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\User  $user
   * @return \Illuminate\Http\Response
   */
  // app/Http/Controllers/Pages/Setting/UserController.php

  public function update(Request $request, User $user)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
      'password' => 'nullable|string|min:8|confirmed',
      'roles' => 'required|string|exists:roles,name', // Diubah dari array ke string
    ]);

    $user->name = $request->name;
    $user->email = $request->email;

    if ($request->filled('password')) {
      $user->password = Hash::make($request->password);
    }
    $user->save();

    // syncRoles bisa menangani string untuk satu role atau array untuk banyak role
    $user->syncRoles($request->roles);

    return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
  }

  /**
   * Menghapus user dari database.
   *
   * @param  \App\Models\User  $user
   * @return \Illuminate\Http\Response
   */
  public function destroy(User $user)
  {
    $user->delete();
    return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
  }
}
