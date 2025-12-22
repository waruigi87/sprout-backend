<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ToDoItem extends Model
{
    use HasFactory;

    // ▼ これを追加して、使うテーブル名を明示します
    protected $table = 'todo_items';

    protected $fillable = ['class_id', 'content', 'is_completed'];
    
    protected $casts = [
        'is_completed' => 'boolean',
    ];
}