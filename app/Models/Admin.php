<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
// ▼ 追加: Schoolモデルを使うのでuseが必要（もしなければ）
use App\Models\School; 

class Admin extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'school_id',
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    /**
     * リレーション修正
     * 間違い: return $this->belongsTo(SchoolClass::class);
     * 正解  : return $this->belongsTo(School::class);
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }
}