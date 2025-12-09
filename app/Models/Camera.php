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
    // [PERBAIKAN] Mengaktifkan kolom group_name agar bisa diisi (mass assignable)
    'group_name',
  ];

  protected $hidden = [
    'api_key',
  ];

  /**
   * Tambahkan properti $casts.
   * Ini memastikan Laravel selalu memperlakukan kolom ini sebagai objek Carbon (tanggal/waktu),
   * yang membuat perbandingan waktu lebih andal.
   */
  protected $casts = [
    'last_heartbeat_at' => 'datetime',
  ];

  /**
   * ACCESSOR PRODUKSI: Menentukan status aktif kamera secara dinamis.
   *
   * @return bool
   */
  public function getIsActiveAttribute(): bool
  {
    if (empty($this->last_heartbeat_at)) {
      return false;
    }

    $now = now();
    $lastHeartbeat = $this->last_heartbeat_at;
    $diffInSeconds = abs($now->diffInSeconds($lastHeartbeat));

    Log::info('--- Camera Status Check: ' . $this->name . ' ---');
    Log::info('Current Time (UTC):     ' . $now->toIso8601String());
    Log::info('Last Heartbeat (UTC):   ' . $lastHeartbeat->toIso8601String());
    Log::info('Difference in Seconds (Absolute): ' . $diffInSeconds);
    Log::info('Is considered active?   ' . ($diffInSeconds < 15 ? 'Yes' : 'No'));

    return $diffInSeconds < 15;
  }

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  public function imageRecords(): HasMany
  {
    return $this->hasMany(ImageRecord::class)->orderBy('captured_at', 'desc');
  }
}
