<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CameraUpdateEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $camera;
    public $data;

    public function __construct($camera, $data)
    {
        $this->camera = $camera;
        $this->data = $data;
    }

    public function broadcastOn()
    {
        // Gunakan channel publik agar mudah diakses di dashboard
        return new Channel('cameras');
    }

    public function broadcastAs()
    {
        return 'CameraUpdated';
    }
}
