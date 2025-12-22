<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class SchoolClass extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'classes'; // テーブル名を明示

    protected $fillable = [
        'school_id',
        'name',
        'code',
        'locale',
    ];

    // --- ここから修正 ---

    /**
     * 正: クラスは「一つの学校」に所属する
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * 正: クラスは「複数のベッド」を直接持つ
     * (hasManyThrough ではなく hasMany です)
     */
    public function hydroBeds()
    {
        return $this->hasMany(HydroBed::class, 'class_id');
    }


    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'badge_grants', 'class_id', 'badge_id')
                    ->withPivot('granted_at')
                    ->withTimestamps();
    }
}