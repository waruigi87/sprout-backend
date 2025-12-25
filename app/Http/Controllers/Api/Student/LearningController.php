<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\Classes; // クラスモデル（名前はClassだと予約語と被るので注意）
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class LearningController extends Controller
{
    // 1日にポイントがもらえる正解数の上限
    const DAILY_POINT_LIMIT = 3;
    // 1問正解ごとのポイント
    const POINTS_PER_ANSWER = 10;

    /**
     * 本日のクイズ取得
     * 制限にかかわらずクイズは出すが、「ポイントがもらえるか」のフラグを渡す
     */
    public function today(Request $request, $id)
    {
        // 1. 今日の「正解」数をカウント
        $todayCorrectCount = QuizAnswer::where('class_id', $id)
            ->where('is_correct', true)
            ->whereDate('created_at', Carbon::today())
            ->count();

        // 2. まだポイントがもらえるか判定
        $isPointEligible = ($todayCorrectCount < self::DAILY_POINT_LIMIT);

        // 3. まだ正解していない問題を取得（全期間通して）
        $correctQuizIds = QuizAnswer::where('class_id', $id)
            ->where('is_correct', true)
            ->pluck('quiz_id');

        $quiz = Quiz::whereNotIn('id', $correctQuizIds)
            ->inRandomOrder()
            ->first();

        // クイズが品切れの場合
        if (!$quiz) {
            return response()->json([
                'has_quiz' => false,
                'message' => '全ての問題をクリアしました！',
            ]);
        }

        return response()->json([
            'has_quiz' => true,
            'quiz' => [
                'id' => $quiz->id,
                'category' => $quiz->category,
                'question' => $quiz->question,
                'options' => $quiz->options,
                'is_answered' => false,
            ],
            // フロントに「正解したらポイントもらえるよ」と伝えるフラグ
            'is_point_eligible' => $isPointEligible,
            // あと何回ポイントもらえるか
            'remaining_point_chances' => max(0, self::DAILY_POINT_LIMIT - $todayCorrectCount),
        ]);
    }

    /**
     * 回答送信
     */
    public function answer(Request $request, $id)
    {
        $request->validate([
            'quiz_id' => 'required|exists:quizzes,id',
            'selected_index' => 'required|integer',
        ]);

        $quiz = Quiz::findOrFail($request->quiz_id);
        
        // 正誤判定
        $isCorrect = ($quiz->answer_index === $request->selected_index);
        $pointsEarned = 0;

        // --- ポイント加算ロジック ---
        if ($isCorrect) {
            // 今日の正解数をチェック
            $todayCorrectCount = QuizAnswer::where('class_id', $id)
                ->where('is_correct', true)
                ->whereDate('created_at', Carbon::today())
                ->count();

            // 上限(3回)未満ならポイント加算
            if ($todayCorrectCount < self::DAILY_POINT_LIMIT) {
                $pointsEarned = self::POINTS_PER_ANSWER;

                // クラスのポイントを更新
                // ※App\Models\Classes または Userモデルなど適宜合わせてください
                $classModel = \App\Models\SchoolClass::findOrFail($id); 
                $classModel->increment('points', $pointsEarned);
            }
        }

        // 履歴保存
        QuizAnswer::create([
            'class_id' => $id,
            'quiz_id' => $quiz->id,
            'is_correct' => $isCorrect,
        ]);
        
        // 更新後の残り回数を計算
        $newTodayCorrectCount = QuizAnswer::where('class_id', $id)
            ->where('is_correct', true)
            ->whereDate('created_at', Carbon::today())
            ->count();
            
        $remaining = max(0, self::DAILY_POINT_LIMIT - $newTodayCorrectCount);

        return response()->json([
            'is_correct' => $isCorrect,
            'correct_answer_index' => $quiz->answer_index,
            'explanation' => $quiz->explanation,
            'points_earned' => $pointsEarned, // 今回獲得したポイント
            'remaining_point_chances' => $remaining, // あと何回ポイントもらえるか
        ]);
    }
}