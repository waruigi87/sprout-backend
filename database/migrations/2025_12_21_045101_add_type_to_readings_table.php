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
        Schema::table('readings', function (Blueprint $table) {
            // temperature, humidity 等の種別を記録
            $table->string('type', 20)->after('sensor_id')->comment('計測タイプ(temperature/humidity)');
        });
    }

    public function down(): void
    {
        Schema::table('readings', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
