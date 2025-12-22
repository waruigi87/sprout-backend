<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizAnswer extends Model
{
    use HasFactory;

    // ▼▼▼ これを追加してください ▼▼▼
    // Laravelのデフォルトは 'quiz_answers' を探すため、
    // 実際に作成したテーブル名 'quiz_results' を指定します。
    protected $table = 'quiz_results'; 

    protected $fillable = [
        'class_id',
        'quiz_id',
        'is_correct',
    ];

    // (必要であればリレーションも記述)
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}