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
        Schema::table('classes', function (Blueprint $table) {
            // クラスコードの直後にゲストコードを追加
            $table->string('guest_code', 20)
                ->nullable() // 既存データがある場合を考慮してnullable推奨
                ->unique()
                ->after('code')
                ->comment('ゲストログイン用コード(閲覧のみ)');
        });
    }

    public function down()
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropColumn('guest_code');
        });
    }
};
