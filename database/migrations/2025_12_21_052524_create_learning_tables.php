<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. ToDoアイテムテーブル
        Schema::create('todo_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->string('content'); // 内容 (例: 水やりをする)
            $table->boolean('is_completed')->default(false); // 完了フラグ
            $table->timestamps();
        });

        // 2. クイズマスターテーブル
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->string('category'); // カテゴリ (例: 光合成)
            $table->text('question');   // 問題文
            $table->json('options');    // 選択肢 (JSON配列: ["A", "B", "C"])
            $table->integer('correct_index'); // 正解のインデックス (0, 1, 2...)
            $table->text('explanation'); // 解説
            $table->timestamps();
        });

        // 3. クイズ回答履歴テーブル（どのクラスがどのクイズを解いたか）
        Schema::create('quiz_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('quiz_id')->constrained('quizzes')->onDelete('cascade');
            $table->boolean('is_correct'); // 正解したかどうか
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_answers');
        Schema::dropIfExists('quizzes');
        Schema::dropIfExists('todo_items');
    }
};