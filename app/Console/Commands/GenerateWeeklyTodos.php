<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SchoolClass;
use App\Models\ToDoItem;
use Illuminate\Support\Facades\DB;

class GenerateWeeklyTodos extends Command
{
    protected $signature = 'todos:generate-weekly';
    protected $description = '全クラスに今週のToDoを一括作成します';

    public function handle()
    {
        $this->info('Generating weekly ToDos...');

        // 毎週配布する定型タスク
        $weeklyTasks = [
            'タンクの水量を確認する',
            '葉の様子を観察する',
            '肥料を追加する（必要な場合）',
        ];

        DB::transaction(function () use ($weeklyTasks) {
            $classes = SchoolClass::all();

            foreach ($classes as $class) {
                // オプション: 先週の完了済みタスクを削除するならここで行う
                // ToDoItem::where('class_id', $class->id)->where('is_completed', true)->delete();

                foreach ($weeklyTasks as $content) {
                    // 同じ内容の未完了タスクが既にある場合は重複させない
                    $exists = ToDoItem::where('class_id', $class->id)
                        ->where('content', $content)
                        ->where('is_completed', false)
                        ->exists();

                    if (!$exists) {
                        ToDoItem::create([
                            'class_id' => $class->id,
                            'content' => $content,
                            'is_completed' => false,
                        ]);
                    }
                }
            }
        });

        $this->info('Weekly ToDos generated successfully!');
    }
}