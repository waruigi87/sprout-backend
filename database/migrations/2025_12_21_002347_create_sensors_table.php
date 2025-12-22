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
    Schema::create('sensors', function (Blueprint $table) {
        $table->id();
        $table->foreignId('hydro_bed_id')->constrained('hydro_beds')->onDelete('cascade')->comment('ベッドID');
        $table->string('device_id', 100)->comment('デバイスID');
        $table->string('type', 20)->comment('タイプ');
        $table->string('name', 50)->nullable()->comment('表示名');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensors');
    }
};
