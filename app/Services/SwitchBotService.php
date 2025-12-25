<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Exception;

class SwitchBotService
{
    protected string $baseUrl = 'https://api.switch-bot.com/v1.1';
    protected string $token;
    protected string $secret;

    public function __construct()
    {
        $this->token = config('services.switchbot.token');
        $this->secret = config('services.switchbot.secret');
    }

    /**
     * APIリクエスト用ヘッダー生成 (署名作成)
     */
    protected function getHeaders(): array
    {
        $nonce = (string) Str::uuid();
        $t = time() * 1000; // ミリ秒
        $data = $this->token . $t . $nonce;
        $sign = strtoupper(base64_encode(hash_hmac('sha256', $data, $this->secret, true)));

        return [
            'Authorization' => $this->token,
            'sign' => $sign,
            'nonce' => $nonce,
            't' => $t,
            'Content-Type' => 'application/json; charset=utf8',
        ];
    }

    /**
     * デバイスのステータス（温湿度など）を取得
     */
    public function getDeviceStatus(string $deviceId)
    {
        // ▼ 修正: withOptions(['verify' => false]) を追加して、SSLエラーを回避
        $response = Http::withOptions(['verify' => false])
            ->withHeaders($this->getHeaders())
            ->get("{$this->baseUrl}/devices/{$deviceId}/status");

        if ($response->failed()) {
            throw new Exception("SwitchBot API Error: " . $response->body());
        }

        return $response->json();
    }

    public function getAllDevices()
    {
        // SSLエラー回避の設定を入れて取得
        $response = Http::withOptions(['verify' => false])
            ->withHeaders($this->getHeaders())
            ->get("{$this->baseUrl}/devices");

        if ($response->failed()) {
            throw new Exception("SwitchBot API Error: " . $response->body());
        }

        return $response->json();
    }
}