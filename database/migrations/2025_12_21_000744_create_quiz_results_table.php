<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('quiz_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade')->comment('クラスID');
            $table->foreignId('quiz_id')->constrained('quizzes')->onDelete('cascade')->comment('クイズID');
            $table->boolean('is_correct')->comment('正誤(1:正解, 0:不正解)');
            $table->dateTime('answered_at')->useCurrent()->comment('回答日時'); // 初期値CURRENT_TIMESTAMP
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_results');
    }
};
