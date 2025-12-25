<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ToDoItem extends Model
{
    use HasFactory;

    /**
     * テーブル名を指定
     * (指定しないとLaravelは 'to_do_items' を探しにいってしまうため必須)
     */
    protected $table = 'todo_items';

    /**
     * 一括割り当て（createやupdate）を許可するカラム
     */
    protected $fillable = [
        'class_id',
        'content',
        'is_completed',
    ];

    /**
     * ネイティブな型へのキャスト
     * (DB上の 0/1 を PHP/JSON上の false/true に変換)
     */
    protected $casts = [
        'is_completed' => 'boolean',
    ];
}