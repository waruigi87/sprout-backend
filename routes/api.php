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
    // Sanctumミドルウェアで保護（ログインしていれば誰でも通る）
    Route::middleware('auth:sanctum')->group(function () {
        
        // (3) ログアウト
        Route::post('/logout', [AuthController::class, 'logout']);

        // 動作確認用: ログインユーザー情報取得
        Route::get('/me', function (Request $request) {
            return $request->user();
        });

        // ▼▼ 閲覧系ルート (ゲストも生徒もOK) ▼▼
        // ゲストトークンには 'access:read' が付与されているため、ここはアクセス可能
        
        // ダッシュボード取得
        Route::get('/classes/{id}/dashboard', [DashboardController::class, 'index']);
        // グラフ取得
        Route::get('/classes/{id}/graphs', [DashboardController::class, 'graphs']);
        // 本日のクイズ取得（問題を見るだけならゲストもOK）
        Route::get('/classes/{id}/learning/today', [LearningController::class, 'today']);


        // Route::middleware('ability:access:write')->group(function () { 
        // ↓ 'abilities' (複数形) に変更
        Route::middleware('abilities:access:write')->group(function () {
            Route::post('/classes/{id}/learning/quiz/answer', [LearningController::class, 'answer']);
            Route::patch('/classes/{id}/todos/{todo_item_id}', [ToDoController::class, 'update']);
        });
    });

    // 管理者専用エリア (Admin Guard)
    // admin_token 権限が必要
    Route::middleware(['auth:sanctum', 'ability:admin_token'])->prefix('admin')->group(function () {
        
        // (9) クラス一覧取得, (10) クラス新規作成
        Route::get('/classes', [ClassController::class, 'index']);
        Route::post('/classes', [ClassController::class, 'store']);
        Route::put('/classes/{id}', [ClassController::class, 'update']);
        Route::delete('/classes/{id}', [ClassController::class, 'destroy']);

        // (11) ベッド一覧取得, (12) ベッド新規登録
        Route::get('/hydro_beds', [HydroBedController::class, 'index']);
        Route::post('/hydro_beds', [HydroBedController::class, 'store']);

        // 詳細取得
        Route::get('/hydro_beds/{id}', [HydroBedController::class, 'show']);

        Route::put('/hydro_beds/{id}', [HydroBedController::class, 'update']);
        Route::delete('/hydro_beds/{id}', [HydroBedController::class, 'destroy']);

    });

});