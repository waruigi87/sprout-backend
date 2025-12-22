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
    Schema::create('todo_progresses', function (Blueprint $table) {
        $table->id();
        $table->foreignId('class_id')->constrained('classes')->onDelete('cascade')->comment('クラスID');
        // Itemsテーブルが先に作られているので、これで成功します
        $table->foreignId('todo_item_id')->constrained('todo_items')->onDelete('cascade')->comment('項目ID');
        $table->boolean('is_completed')->default(false)->comment('完了フラグ');
        $table->dateTime('completed_at')->nullable()->comment('完了日時');
        $table->date('target_date')->nullable()->comment('目標日');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('todo_progresses');
    }
};
