<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // database/migrations/xxxx_xx_xx_add_points_to_classes_table.php
    public function up()
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->integer('points')->default(0)->after('name')->comment('獲得ポイント');
        });
    }

    public function down()
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropColumn('points');
        });
    }
};
