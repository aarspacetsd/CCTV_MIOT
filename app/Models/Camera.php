<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

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
        'mqtt_username',
        'mqtt_password', // Sekarang menyimpan plain text
        'mqtt_status',
        'websocket_channel_id',
        'last_heartbeat_at',
        'group_id',
    ];

    protected $hidden = [
        'api_key',
        // 'mqtt_password', // Dihapus dari hidden agar bisa dipanggil di view
    ];

    protected $casts = [
        'last_heartbeat_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($camera) {
            if (empty($camera->device_id)) {
                $camera->device_id = (string) Str::uuid();
            }

            // Generate Kredensial MQTT (Tanpa Bcrypt agar bisa di-copy)
            if (empty($camera->mqtt_username)) {
                $camera->mqtt_username = 'mqtt_' . Str::random(8);
            }
            if (empty($camera->mqtt_password)) {
                $camera->mqtt_password = Str::random(16);
            }

            if (empty($camera->api_key)) {
                $camera->api_key = Str::random(40);
            }
        });
    }

    public function getIsActiveAttribute(): bool
    {
        if (empty($this->last_heartbeat_at)) {
            return false;
        }
        $now = now();
        $diffInSeconds = abs($now->diffInSeconds($this->last_heartbeat_at));
        return $diffInSeconds < 15;
    }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function group(): BelongsTo { return $this->belongsTo(CameraGroup::class, 'group_id'); }
    public function imageRecords(): HasMany { return $this->hasMany(ImageRecord::class)->orderBy('captured_at', 'desc'); }
}
