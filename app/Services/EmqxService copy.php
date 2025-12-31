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

        // Menggunakan IP Host yang sudah diverifikasi (10.8.124.228)
        $this->callbackBaseUrl = env('EMQX_CALLBACK_URL', 'http://10.8.124.228:8084');
    }

    /**
     * Sinkronisasi total semua konfigurasi ke EMQX.
     * Digunakan untuk otomasi agar tidak perlu akses URL sync manual.
     */
    public function syncAll()
    {
        try {
            $this->setupAuthentication();
            $this->setupAuthorization();
            $this->setupImageRule();
            Log::info("EMQX: Full synchronization completed successfully.");
            return true;
        } catch (\Exception $e) {
            Log::error("EMQX: Full synchronization failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Helper untuk mendapatkan URL callback yang benar
     */
    protected function getUrl($path)
    {
        return rtrim($this->callbackBaseUrl, '/') . $path;
    }

    /**
     * Setup Autentikasi (Password-based HTTP)
     */
    public function setupAuthentication()
    {
        $url = "{$this->baseUrl}/authentication";
        $response = $this->get($url);

        if ($response->successful()) {
            $exists = collect($response->json())->contains(fn($item) => ($item['backend'] ?? '') === 'http');
            if (!$exists) {
                $this->post($url, [
                    'mechanism' => 'password_based',
                    'backend' => 'http',
                    'method' => 'post',
                    'url' => $this->getUrl('/api/mqtt/auth'),
                    'headers' => [
                        'content-type' => 'application/json',
                        'accept' => 'application/json'
                    ],
                    'body' => [
                        'username' => '${username}',
                        'password' => '${password}'
                    ],
                    'enable' => true
                ]);
                Log::info("EMQX: Auth HTTP created.");
            }
        }
    }

    /**
     * Setup Authorization (ACL HTTP)
     */
    public function setupAuthorization()
    {
        $url = "{$this->baseUrl}/authorization/sources";
        $response = $this->get($url);

        if ($response->successful()) {
            $exists = collect($response->json())->contains(fn($item) => ($item['type'] ?? '') === 'http');
            if (!$exists) {
                $this->post($url, [
                    'type' => 'http',
                    'method' => 'post',
                    'url' => $this->getUrl('/api/mqtt/acl'),
                    'headers' => [
                        'content-type' => 'application/json',
                        'accept' => 'application/json'
                    ],
                    'body' => [
                        'username' => '${username}',
                        'topic' => '${topic}',
                        'action' => '${action}'
                    ],
                    'enable' => true
                ]);
                Log::info("EMQX: Authz HTTP created.");
            }
        }
    }

    /**
     * Setup Konektor, Aksi, dan Rule (Alur Data Gambar)
     * URUTAN: Connector -> Action -> Rule
     */
    public function setupImageRule()
    {
        $connectorName = "conn_laravel_http";
        $actionName = "action_laravel_webhook";
        $ruleId = "rule_capture_images";

        // 1. Buat Connector (Koneksi Fisik ke Host)
        $this->post("{$this->baseUrl}/connectors", [
            'type' => 'http',
            'name' => $connectorName,
            'url' => $this->callbackBaseUrl,
            'headers' => [
                'content-type' => 'application/json',
                'accept' => 'application/json'
            ],
            'resource_opts' => [
                'health_check_interval' => '15s'
            ],
            'enable' => true
        ]);

        // 2. Buat Action (Operasi Webhook menggunakan Connector)
        $this->post("{$this->baseUrl}/actions", [
            'type' => 'http',
            'name' => $actionName,
            'connector' => $connectorName,
            'parameters' => [
                'path' => '/api/mqtt/webhook',
                'method' => 'post',
                'headers' => [
                    'content-type' => 'application/json'
                ],
                'body' => json_encode([
                    'action' => 'message_publish',
                    'topic' => '${topic}',
                    'payload' => '${payload}',
                    'username' => '${username}'
                ]),
                'max_retries' => 3
            ],
            'enable' => true
        ]);

        // 3. Buat Rule (Trigger SQL)
        $ruleCheck = $this->get("{$this->baseUrl}/rules/{$ruleId}");
        if ($ruleCheck->failed()) {
            $this->post("{$this->baseUrl}/rules", [
                'id' => $ruleId,
                'sql' => 'SELECT * FROM "iot/camera/+/image"',
                'actions' => ["http:{$actionName}"],
                'enable' => true
            ]);
            Log::info("EMQX: Rule capture images created.");
        }
    }

    protected function get($url) {
        return Http::withBasicAuth($this->apiKey, $this->apiSecret)->get($url);
    }

    protected function post($url, $data) {
        return Http::withBasicAuth($this->apiKey, $this->apiSecret)->post($url, $data);
    }
}
