<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log; // 1. Tambahkan Log facade

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
  ];

  protected $hidden = [
    'api_key',
  ];

  /**
   * 2. [BARU] Tambahkan properti $casts.
   * Ini memastikan Laravel selalu memperlakukan kolom ini sebagai objek Carbon (tanggal/waktu),
   * yang membuat perbandingan waktu lebih andal.
   */
  protected $casts = [
    'last_heartbeat_at' => 'datetime',
  ];

  /**
   * ACCESSOR PRODUKSI: Menentukan status aktif kamera secara dinamis.
   * Method ini sekarang menggunakan waktu server yang sebenarnya (`now()`).
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
    // [PERBAIKAN] Gunakan abs() untuk mendapatkan nilai absolut (selalu positif) dari selisih waktu.
    $diffInSeconds = abs($now->diffInSeconds($lastHeartbeat));

    // 3. [BARU] Tambahkan logging untuk debugging
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
