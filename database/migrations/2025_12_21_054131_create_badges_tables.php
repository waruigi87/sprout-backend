<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    // 1. バッジマスタ（どんなバッジがあるか）
    Schema::create('badges', function (Blueprint $table) {
        $table->id();
        $table->string('name');         // バッジ名 (例: クイズ王)
        $table->string('description');  // 説明 (例: クイズに10回正解する)
        $table->string('image_key');    // フロントエンドで画像を表示するためのキー (例: quiz_master)
        $table->string('condition_type'); // 判定ロジックの種類 (例: quiz_count)
        $table->integer('condition_value'); // 達成に必要な数値 (例: 10)
        $table->timestamps();
    });

    // 2. クラスごとの獲得バッジ（中間テーブル）
    Schema::create('class_badges', function (Blueprint $table) {
        $table->id();
        $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
        $table->foreignId('badge_id')->constrained('badges')->onDelete('cascade');
        $table->dateTime('awarded_at'); // 獲得日時
        
        // 同じバッジは1回しか取れないようにユニーク制約
        $table->unique(['class_id', 'badge_id']);
    });
}

public function down(): void
{
    Schema::dropIfExists('class_badges');
    Schema::dropIfExists('badges');
}
};
