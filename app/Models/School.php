<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * リレーション: 学校は複数のクラスを持つ
     */
    public function classes()
    {
        // 外部キー school_id で SchoolClass と紐付く
        return $this->hasMany(SchoolClass::class, 'school_id');
    }

    /**
     * リレーション: 学校は（クラスを通じて）複数のベッドを持つ
     */
    public function hydroBeds()
    {
        // クラスを経由してベッドを取得
        return $this->hasManyThrough(HydroBed::class, SchoolClass::class, 'school_id', 'class_id');
    }
}