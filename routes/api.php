<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Admin\ClassController;
use App\Http\Controllers\Api\Admin\HydroBedController;
use App\Http\Controllers\Api\Student\DashboardController;
use App\Http\Controllers\Api\Student\LearningController;
use App\Http\Controllers\Api\Student\ToDoController;

// v1 グループ
Route::prefix('v1')->group(function () {

    // --- 認証不要エリア ---

    // (1) クラスログイン
    Route::post('/login', [AuthController::class, 'login']);

    // (2) 管理者ログイン
    Route::post('/admin/login', [AuthController::class, 'adminLogin']);


    // --- 認証必須エリア ---
    // Sanctumミドルウェアで保護
    Route::middleware('auth:sanctum')->group(function () {
        
        // (3) ログアウト
        Route::post('/logout', [AuthController::class, 'logout']);

        // 動作確認用: ログインユーザー情報取得
        Route::get('/me', function (Request $request) {
            return $request->user();
        });

        // ▼ 追加: ダッシュボードとグラフのルート定義
        Route::get('/classes/{id}/dashboard', [DashboardController::class, 'index']);
        Route::get('/classes/{id}/graphs', [DashboardController::class, 'graphs']);

        // (6) 本日のクイズ取得
        Route::get('/classes/{id}/learning/today', [LearningController::class, 'today']);
        
        // (7) クイズ回答送信
        Route::post('/classes/{id}/learning/quiz/answer', [LearningController::class, 'answer']);

        // (8) ToDoチェック更新
        Route::patch('/classes/{id}/todos/{todo_item_id}', [ToDoController::class, 'update']);
    });

    // 管理者専用エリア (Admin Guard)
    Route::middleware(['auth:sanctum', 'ability:admin_token'])->prefix('admin')->group(function () {
        
        // (9) クラス一覧取得, (10) クラス新規作成
        Route::get('/classes', [ClassController::class, 'index']);
        Route::post('/classes', [ClassController::class, 'store']);
        Route::put('/classes/{id}', [ClassController::class, 'update']);
        Route::delete('/classes/{id}', [ClassController::class, 'destroy']);

        // (11) ベッド一覧取得, (12) ベッド新規登録
        // routes/api.php

        // (11) ベッド一覧取得, (12) ベッド新規登録
        Route::get('/hydro_beds', [HydroBedController::class, 'index']);
        Route::post('/hydro_beds', [HydroBedController::class, 'store']);

        // ★★★ ここに追加（詳細取得） ★★★
        Route::get('/hydro_beds/{id}', [HydroBedController::class, 'show']);

        Route::put('/hydro_beds/{id}', [HydroBedController::class, 'update']);
        Route::delete('/hydro_beds/{id}', [HydroBedController::class, 'destroy']);

    });

});