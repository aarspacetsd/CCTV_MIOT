<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class Camera extends Model
{
  use HasFactory;

  protected $fillable = [
    'user_id',
    'device_id',
    'name',
    'description',
    'api_key',
    'is_active',
    'websocket_channel_id',
    'last_heartbeat_at',
    'group_id', // Menggunakan ID dari tabel master camera_groups
  ];

  protected $hidden = [
    'api_key',
  ];

  /**
   * Casts untuk kolom datetime.
   */
  protected $casts = [
    'last_heartbeat_at' => 'datetime',
  ];

  /**
   * ACCESSOR: Menentukan status aktif kamera secara dinamis.
   */
  public function getIsActiveAttribute(): bool
  {
    if (empty($this->last_heartbeat_at)) {
      return false;
    }

    $now = now();
    $lastHeartbeat = $this->last_heartbeat_at;
    $diffInSeconds = abs($now->diffInSeconds($lastHeartbeat));

    return $diffInSeconds < 15;
  }

  /**
   * Relasi ke User
   */
  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  /**
   * Relasi ke Tabel Master Group
   */
  public function group(): BelongsTo
  {
    return $this->belongsTo(CameraGroup::class, 'group_id');
  }

  /**
   * Relasi ke Image Records
   */
  public function imageRecords(): HasMany
  {
    return $this->hasMany(ImageRecord::class)->orderBy('captured_at', 'desc');
  }
}
