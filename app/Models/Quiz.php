<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = ['category', 'question', 'options', 'answer_index', 'explanation'];

    // optionsカラムはJSONなので、自動的に配列に変換する
    protected $casts = [
        'options' => 'array',
    ];
}