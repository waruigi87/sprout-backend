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
        $table->foreignId('todo_template_id')->constrained('todo_templates')->onDelete('cascade')->comment('テンプレートID');
        $table->string('content', 255)->comment('作業内容');
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
