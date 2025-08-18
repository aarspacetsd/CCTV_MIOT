<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CameraOnline implements ShouldBroadcast
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  /**
   * Nama channel WebSocket yang akan dikirimi pesan.
   * @var string
   */
  public string $channelId;

  /**
   * Buat instance event baru.
   */
  public function __construct(string $channelId)
  {
    $this->channelId = $channelId;
  }

  /**
   * Dapatkan channel tempat event ini akan disiarkan.
   *
   * @return array<int, \Illuminate\Broadcasting\Channel>
   */
  public function broadcastOn(): array
  {
    // Kita menggunakan PrivateChannel agar hanya frontend yang tahu ID-nya
    // yang bisa mendengarkan.
    return [
      new PrivateChannel($this->channelId),
    ];
  }

  /**
   * Nama event yang akan didengarkan oleh frontend.
   */
  public function broadcastAs(): string
  {
    return 'camera.online';
  }
}
