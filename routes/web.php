<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});



use App\Services\SwitchBotService;

Route::get('/debug/switchbot-devices', function (SwitchBotService $service) {
    // デバイス一覧取得APIを叩く
    $response = \Illuminate\Support\Facades\Http::withHeaders([
        'Authorization' => config('services.switchbot.token'),
        'sign' => (function() {
            $token = config('services.switchbot.token');
            $secret = config('services.switchbot.secret');
            $nonce = (string) \Illuminate\Support\Str::uuid();
            $t = time() * 1000;
            $data = $token . $t . $nonce;
            return strtoupper(base64_encode(hash_hmac('sha256', $data, $secret, true)));
        })(),
        'nonce' => (string) \Illuminate\Support\Str::uuid(), // 簡易実装のため再生成
        't' => time() * 1000,
        'Content-Type' => 'application/json; charset=utf8',
    ])->get('https://api.switch-bot.com/v1.1/devices');

    return $response->json();
});