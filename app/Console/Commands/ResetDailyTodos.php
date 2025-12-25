<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ToDoItem; // 作成したモデルを使う
use Illuminate\Support\Facades\Log;

class ResetDailyTodos extends Command
{
    /**
     * コンソールで実行するコマンド名
     * 例: php artisan todos:reset
     */
    protected $signature = 'todos:reset';

    /**
     * コマンドの説明
     */
    protected $description = '全てのクラスのToDoチェックを未完了にリセットします';

    /**
     * コマンドの実行処理
     */
    public function handle()
    {
        // 全てのチェックを外す (is_completed = false に更新)
        ToDoItem::query()->update(['is_completed' => false]);

        // ログに残しておくと安心です (storage/logs/laravel.log に出力されます)
        Log::info('Daily Todo Reset executed successfully.');
        
        $this->info('All todos have been reset.');
    }
}