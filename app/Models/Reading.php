<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reading extends Model
{
    use HasFactory;

    // 一括保存を許可するカラム
    protected $fillable = [
        'sensor_id',
        'type',        // 追加したカラム (temperature / humidity)
        'value',
        'recorded_at',
    ];

    // 型変換の設定
    protected $casts = [
        'recorded_at' => 'datetime',
        'value' => 'float',
    ];

    // リレーション: このデータはどのセンサーのものか
    public function sensor()
    {
        return $this->belongsTo(Sensor::class);
    }
}