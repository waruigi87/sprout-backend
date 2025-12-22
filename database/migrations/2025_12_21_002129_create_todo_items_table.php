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
        Schema::create('todo_items', function (Blueprint $table) {
            $table->id();
            // ▼ 修正: テンプレートIDではなく、クラスIDに紐付ける形に変更
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade')->comment('クラスID');
            
            $table->string('content', 255)->comment('作業内容');
            
            // ▼ 追加: 完了フラグを追加
            $table->boolean('is_completed')->default(false)->comment('完了フラグ');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('todo_items');
    }
};