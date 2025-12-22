<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sensor extends Model
{
    use HasFactory;

    // 一括割り当て可能な項目
    protected $fillable = [
        'hydro_bed_id',
        'device_id',
        'type',
        'name',
    ];

    /**
     * リレーション: 設置されているベッド
     */
    public function hydroBed()
    {
        return $this->belongsTo(HydroBed::class);
    }

    /**
     * リレーション: 計測データ履歴
     * (Readingモデルもまだの場合は作成が必要です)
     */
    public function readings()
    {
        return $this->hasMany(Reading::class);
    }
}