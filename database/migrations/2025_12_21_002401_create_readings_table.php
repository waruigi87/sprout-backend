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
    Schema::create('readings', function (Blueprint $table) {
        $table->id();
        // 親である sensors テーブルが先に作られているので成功します
        $table->foreignId('sensor_id')->constrained('sensors')->onDelete('cascade')->comment('センサーID');
        $table->decimal('value', 8, 2)->comment('計測値');
        $table->dateTime('recorded_at')->comment('計測時刻');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('readings');
    }
};
