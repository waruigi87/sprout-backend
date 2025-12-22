<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Crop extends Model
{
    use HasFactory;

    /**
     * 複数代入可能な属性
     */
    protected $fillable = [
        'name',
    ];

    /**
     * リレーション: この作物を育てているベッド一覧
     * (Crop : HydroBed = 1 : 多)
     */
    public function hydroBeds()
    {
        return $this->hasMany(HydroBed::class);
    }
}