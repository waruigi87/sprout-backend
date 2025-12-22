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
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->text('question')->comment('問題文');
            $table->json('options')->comment('選択肢(配列形式)');
            $table->integer('answer_index')->comment('正解Index(0始まり)');
            $table->text('explanation')->comment('解説');
            $table->string('category', 50)->nullable()->comment('カテゴリ');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
