<?php

namespace App\Policies;

use App\Models\Camera;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CameraPolicy
{
  public function create(User $user): bool
  {
    return $user !== null;
  }

  public function update(User $user, Camera $camera): bool
  {
    // Gunakan perbandingan '==' untuk menghindari masalah tipe data.
    return $user->id == $camera->user_id;
  }

  public function delete(User $user, Camera $camera): bool
  {
    return $user->id == $camera->user_id;
  }
}
