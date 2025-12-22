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
        // ここに最新の定義を記述します
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('バッジ名'); 
            $table->string('description')->comment('説明');
            $table->string('image_key')->comment('画像キー');
            $table->string('condition_type')->comment('判定ロジック種類');
            $table->integer('condition_value')->comment('達成値');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('badges');
    }
};