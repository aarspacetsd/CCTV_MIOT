<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EmqxService
{
    protected $baseUrl;
    protected $apiKey;
    protected $apiSecret;
    protected $callbackBaseUrl;

    public function __construct()
    {
        $this->baseUrl = env('EMQX_API_URL', 'http://localhost:18083/api/v5');
        $this->apiKey = env('EMQX_API_KEY');
        $this->apiSecret = env('EMQX_API_SECRET');
        // URL Callback ke Laravel yang bisa dijangkau oleh EMQX
        $this->callbackBaseUrl = env('EMQX_CALLBACK_URL', 'http://10.8.124.228:8084');
    }

    /**
     * Sinkronisasi total seluruh konfigurasi ke EMQX.
     */
    public function syncAll()
    {
        try {
            // 1. Setup Autentikasi (Login)
            $this->setupAuthentication();

            // 2. Setup Otorisasi (ACL/Topik)
            $this->setupAuthorization();

            // 3. Setup Jalur Gambar MQTT (Utama)
            $this->setupImageRule();

            // 4. Setup Jalur Telemetri WebSocket (Bridge)
            $this->setupWebSocketRule();

            // 5. Setup Jalur Gambar WebSocket (Bridge)
            $this->setupWebSocketImageRule();

            Log::info("EMQX: Sinkronisasi Full (MQTT + WS + WS Image) Berhasil.");
            return true;
        } catch (\Exception $e) {
            Log::error("EMQX: Sinkronisasi Gagal: " . $e->getMessage());
            throw $e; // Lempar kembali agar tampil di error page jika debug aktif
        }
    }

    /**
     * Konfigurasi HTTP Authentication di EMQX.
     */
    public function setupAuthentication()
    {
        $url = "{$this->baseUrl}/authentication";

        $payload = [
            'backend' => 'http',
            'mechanism' => 'password_based',
            'method' => 'post',
            'url' => "{$this->callbackBaseUrl}/api/mqtt/auth",
            'headers' => [
                'content-type' => 'application/json'
            ],
            'body' => [
                'username' => '${username}',
                'password' => '${password}'
            ],
            'enable' => true
        ];

        return $this->post($url, $payload);
    }

    /**
     * Konfigurasi HTTP Authorization (ACL) di EMQX.
     */
    public function setupAuthorization()
    {
        $url = "{$this->baseUrl}/authorization/sources/http";

        $payload = [
            'type' => 'http',
            'method' => 'post',
            'url' => "{$this->callbackBaseUrl}/api/mqtt/acl",
            'headers' => [
                'content-type' => 'application/json'
            ],
            'body' => [
                'username' => '${username}',
                'topic' => '${topic}',
                'action' => '${action}'
            ],
            'enable' => true
        ];

        return $this->post($url, $payload);
    }

    /**
     * Setup Rule untuk Gambar Jalur MQTT.
     */
    public function setupImageRule()
    {
        $this->createRuleAndAction(
            "action_laravel_mqtt_image",
            "/api/mqtt/webhook",
            "rule_mqtt_image",
            'SELECT * FROM "iot/camera/+/image"'
        );
    }

    /**
     * Setup Rule untuk Telemetri Jalur WebSocket.
     */
    public function setupWebSocketRule()
    {
        $this->createRuleAndAction(
            "action_laravel_ws_telemetry",
            "/api/ws-bridge/telemetry",
            "rule_ws_telemetry",
            'SELECT * FROM "ws/camera/+/telemetry"'
        );
    }

    /**
     * Setup Rule untuk Gambar Jalur WebSocket.
     */
    public function setupWebSocketImageRule()
    {
        $this->createRuleAndAction(
            "action_laravel_ws_image",
            "/api/ws-bridge/image",
            "rule_ws_image",
            'SELECT * FROM "ws/camera/+/image"'
        );
    }

    /**
     * Helper untuk membuat Action dan Rule di EMQX.
     */
    private function createRuleAndAction($actionName, $apiPath, $ruleId, $sql)
    {
        $connectorName = "conn_laravel_http";

        // 1. Pastikan Connector HTTP Ada/Terbuat
        $this->post("{$this->baseUrl}/connectors", [
            'type' => 'http',
            'name' => $connectorName,
            'base_url' => $this->callbackBaseUrl,
            'enable' => true
        ]);

        // 2. Buat Action
        $this->post("{$this->baseUrl}/actions", [
            'type' => 'http',
            'name' => $actionName,
            'connector' => $connectorName,
            'parameters' => [
                'path' => $apiPath,
                'method' => 'post',
                'headers' => [
                    'content-type' => 'application/json',
                    'accept' => 'application/json'
                ],
                'body' => json_encode([
                    'action' => 'message_publish',
                    'topic' => '${topic}',
                    'payload' => '${payload}',
                    'username' => '${username}'
                ])
            ],
            'enable' => true
        ]);

        // 3. Buat Rule
        $this->post("{$this->baseUrl}/rules", [
            'id' => $ruleId,
            'sql' => $sql,
            'actions' => ["http:{$actionName}"],
            'enable' => true
        ]);
    }

    /**
     * Helper HTTP POST dengan Basic Auth EMQX.
     */
    protected function post($url, $data)
    {
        $response = Http::withBasicAuth($this->apiKey, $this->apiSecret)
            ->post($url, $data);

        return $response;
    }

    /**
     * Helper HTTP GET dengan Basic Auth EMQX.
     */
    protected function get($url)
    {
        return Http::withBasicAuth($this->apiKey, $this->apiSecret)
            ->get($url);
    }
}
