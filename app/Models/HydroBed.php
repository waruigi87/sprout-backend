<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HydroBed extends Model
{
    use HasFactory;

    // 一括割り当て可能な項目
    protected $fillable = [
        'class_id',
        'crop_id',
        'name',
        'location',
        'status',
        'planted_at',
    ];

    // データ型の変換（planted_at を Carbonインスタンスとして扱う）
    protected $casts = [
        'planted_at' => 'datetime',
    ];

    /**
     * リレーション: 所属するクラス
     * モデル名は SchoolClass としている点に注意
     */
    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /**
     * リレーション: 育てている作物
     */
    public function crop()
    {
        return $this->belongsTo(Crop::class);
    }

    /**
     * リレーション: 設置されているセンサー
     */
    public function sensors()
    {
        return $this->hasMany(Sensor::class);
    }
}