<?php


namespace App\Http\Controllers\Pages\Admin;

use App\Http\Controllers\Controller;
use App\Models\Camera;
use App\Models\Firmware;
use PhpMqtt\Client\Facades\MQTT;

class FirmwareController extends Controller
{
    public function sendUpdate($id)
    {
        $camera = Camera::findOrFail($id);
        $firmware = Firmware::latest()->first();

        $payload = [
            'version'  => $firmware->version,
            'url'      => asset('storage/' . $firmware->file_path),
            'checksum' => $firmware->checksum
        ];

        MQTT::publish("iot/camera/{$camera->device_id}/ota", json_encode($payload), 1);

        return back()->with('success', 'Update firmware dipicu via MQTT.');
    }
}
