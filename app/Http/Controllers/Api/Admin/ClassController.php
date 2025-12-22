<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ClassController extends Controller
{
    /**
     * (9) クラス一覧取得
     * GET /api/v1/admin/classes
     */
    public function index(Request $request)
    {
        // ログイン中の管理者を取得
        $admin = $request->user();

        // 管理者が所属する学校のクラス一覧を取得
        // (Adminモデルに school() リレーションがある前提)
        $classes = $admin->school->classes()
            ->orderBy('created_at', 'desc')
            ->select('id', 'name', 'code', 'created_at')
            ->get();

        return response()->json($classes);
    }

    /**
     * (10) クラス新規作成
     * POST /api/v1/admin/classes
     */
    public function store(Request $request)
    {
        // バリデーション (E005: 422 Unprocessable Entity)
        $request->validate([
            'name' => 'required|string|max:50',
        ]);

        $admin = $request->user();

        // クラスコードの生成 (G + 年 + ランダム等、仕様に合わせて調整)
        // ここでは簡易的にランダム8文字の英数字とし、ユニークチェックを行います
        do {
            $code = Str::upper(Str::random(8));
        } while (SchoolClass::where('code', $code)->exists());

        // 保存処理
        $class = SchoolClass::create([
            'school_id' => $admin->school_id,
            'name' => $request->name,
            'code' => $code,
            'locale' => 'ja', // デフォルト
        ]);

        return response()->json([
            'id' => $class->id,
            'name' => $class->name,
            'code' => $class->code,
            'created_at' => $class->created_at,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:50',
        ]);

        $admin = $request->user();

        // 自校のクラスかどうか確認して取得
        $class = SchoolClass::where('id', $id)
            ->where('school_id', $admin->school_id)
            ->firstOrFail();

        $class->update([
            'name' => $request->name,
            // code は運用途中での変更は混乱を招くため、今回は変更不可とします
        ]);

        return response()->json($class);
    }

    /**
     * (追加) クラスの削除
     * DELETE /api/v1/admin/classes/{id}
     */
    public function destroy(Request $request, $id)
    {
        $admin = $request->user();

        // 自校のクラスかどうか確認して取得
        $class = SchoolClass::where('id', $id)
            ->where('school_id', $admin->school_id)
            ->firstOrFail();

        // 削除実行（紐づくベッドや生徒データもCascade設定により消える想定）
        $class->delete();

        return response()->json(['message' => 'Class deleted successfully']);
    }
}