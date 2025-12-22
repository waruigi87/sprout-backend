<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// 1. センサーデータ取得 (既存)
Schedule::command('sensors:fetch')->hourly();

// 2. 週次ToDo生成: 毎週月曜日の朝8時に実行
Schedule::command('todos:generate-weekly')->weeklyOn(1, '8:00');

// 3. バッジ判定: 毎日深夜、または1時間に1回など
// （ユーザー体験的には、回答直後に判定するのが理想ですが、バッチならここです）
Schedule::command('badges:check')->hourly();