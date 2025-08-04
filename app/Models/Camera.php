<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Camera extends Model
{
  use HasFactory;

  /**
   * Atribut yang dapat diisi secara massal.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'user_id',
    'device_id',
    'name',
    'description',
    'api_key',
    'is_active',
  ];

  /**
   * Atribut yang harus disembunyikan saat serialisasi.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'api_key', // Sembunyikan API key dari response JSON secara default
  ];

  /**
   * Mendefinisikan relasi many-to-one: satu Camera dimiliki oleh satu User.
   */
  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  /**
   * Mendefinisikan relasi one-to-many: satu Camera bisa memiliki banyak ImageRecord.
   */
  public function imageRecords(): HasMany
  {
    return $this->hasMany(ImageRecord::class);
  }
}
