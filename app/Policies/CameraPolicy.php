<?php

namespace App\Policies;

use App\Models\Camera;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CameraPolicy
{
  /**
   * Tentukan apakah pengguna dapat melihat daftar kamera.
   */
  public function viewAny(User $user): bool
  {
    return true; // Semua user yang login bisa melihat daftar kamera
  }

  /**
   * Tentukan apakah pengguna dapat membuat kamera baru.
   * Ini yang akan memperbaiki error 403 Anda.
   */
  public function create(User $user): bool
  {
    // Izinkan semua pengguna yang sudah login untuk membuat kamera
    return $user !== null;
  }

  /**
   * Tentukan apakah pengguna dapat mengupdate kamera.
   */
  public function update(User $user, Camera $camera): bool
  {
    // Hanya izinkan jika user_id di kamera sama dengan id user yang login
    return $user->id === $camera->user_id;
  }

  /**
   * Tentukan apakah pengguna dapat menghapus kamera.
   */
  public function delete(User $user, Camera $camera): bool
  {
    // Hanya izinkan jika user_id di kamera sama dengan id user yang login
    return $user->id === $camera->user_id;
  }
}
