<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use Illuminate\Http\Request;

class LearningController extends Controller
{
    /**
     * (6) 本日のクイズ取得
     * GET /api/v1/classes/{id}/learning/today
     */
    public function today(Request $request, $id)
    {
        // まだこのクラスが正解していないクイズをランダムに1つ取得
        // (簡易実装: 既に回答済みテーブルにレコードがない、または正解していないものを取得)
        $answeredQuizIds = QuizAnswer::where('class_id', $id)
            ->where('is_correct', true) // 正解済みのものは除外
            ->pluck('quiz_id');

        $quiz = Quiz::whereNotIn('id', $answeredQuizIds)
            ->inRandomOrder()
            ->first();

        if (!$quiz) {
            return response()->json(['has_quiz' => false]);
        }

        // 今日の分の回答済みチェック（今日すでに間違えたか？）
        // 今回は簡易化のため「未正解なら何度でも挑戦可」とします

        return response()->json([
            'has_quiz' => true,
            'quiz' => [
                'id' => $quiz->id,
                'category' => $quiz->category,
                'question' => $quiz->question,
                'options' => $quiz->options,
                // 正解(correct_index)は隠す！
                'is_answered' => false, 
            ]
        ]);
    }

    /**
     * (7) クイズ回答送信
     * POST /api/v1/classes/{id}/learning/quiz/answer
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

        // 回答履歴を保存
        QuizAnswer::create([
            'class_id' => $id,
            'quiz_id' => $quiz->id,
            'is_correct' => $isCorrect,
        ]);

        return response()->json([
            'is_correct' => $isCorrect,
            'correct_answer_index' => $quiz->answer_index,
            'explanation' => $quiz->explanation,
        ]);
    }
}