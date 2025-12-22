<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    protected $fillable = ['name', 'description', 'image_key', 'condition_type', 'condition_value'];

    // リレーション: このバッジを獲得したクラス
    public function classes()
    {
        return $this->belongsToMany(SchoolClass::class, 'class_badges')
                    ->withPivot('awarded_at');
    }
}