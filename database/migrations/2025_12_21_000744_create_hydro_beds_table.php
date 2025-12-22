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
        Schema::create('hydro_beds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade')->comment('クラスID');
            // 作物は未設定(NULL)の場合もあるため nullable
            $table->foreignId('crop_id')->nullable()->constrained('crops')->nullOnDelete()->comment('作物ID');
            $table->string('name', 50)->comment('ベッド名');
            $table->string('location', 100)->nullable()->comment('設置場所');
            $table->string('status', 20)->default('standby')->comment('状態(standby/active)');
            $table->dateTime('planted_at')->nullable()->comment('栽培開始日');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hydro_beds');
    }
};
