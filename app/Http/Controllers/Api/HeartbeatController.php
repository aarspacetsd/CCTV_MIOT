<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Events\CameraOnline;
use App\Models\Camera;
use Illuminate\Http\Request;

class HeartbeatController extends Controller
{
  /**
   * Menerima sinyal heartbeat dari kamera dan menyiarkan status online.
   */
  public function __invoke(Request $request)
  {
    $request->validate([
      'websocket_channel_id' => 'required|string|exists:cameras,websocket_channel_id'
    ]);

    $channelId = $request->input('websocket_channel_id');

    // Memicu event CameraOnline
    broadcast(new CameraOnline($channelId));

    return response()->json(['status' => 'ok']);
  }
}
