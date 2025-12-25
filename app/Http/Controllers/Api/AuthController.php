<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Support\Facades\Auth; // 今回はRequest経由で取得するので未使用なら消してもOK

class AuthController extends Controller
{
    /**
     * (1) クラスログイン（生徒・ゲスト兼用）
     * POST /api/v1/login
     */
    public function login(Request $request)
    {
        // バリデーション
        $request->validate([
            'code' => 'required|string|max:20',
        ]);

        $inputCode = $request->code;

        // ---------------------------------------------------
        // パターンA: 正規のクラスコード（生徒・先生）か確認
        // ---------------------------------------------------
        $class = SchoolClass::where('code', $inputCode)->first();

        if ($class) {
            // 正規ユーザーには「読み取り」と「書き込み」の権限を付与
            $token = $class->createToken('class_token', ['access:read', 'access:write'])->plainTextToken;

            return response()->json([
                'token' => $token,
                'role' => 'student', // フロントエンドでの表示制御用
                'class' => [
                    'id' => $class->id,
                    'name' => $class->name,
                    'locale' => $class->locale,
                ]
            ], 200);
        }

        // ---------------------------------------------------
        // パターンB: ゲストコード（保護者・見学者）か確認
        // ---------------------------------------------------
        // ※DBに guest_code カラムが追加されている前提
        $guestClass = SchoolClass::where('guest_code', $inputCode)->first();

        if ($guestClass) {
            // ゲストユーザーには「読み取り」権限のみ付与（書き込み権限なし）
            $token = $guestClass->createToken('guest_token', ['access:read'])->plainTextToken;

            return response()->json([
                'token' => $token,
                'role' => 'guest', // フロントエンドでの表示制御用
                'class' => [
                    'id' => $guestClass->id,
                    'name' => $guestClass->name,
                    'locale' => $guestClass->locale,
                ]
            ], 200);
        }

        // ---------------------------------------------------
        // 該当なし
        // ---------------------------------------------------
        return response()->json([
            'message' => 'クラスコードが間違っています。',
            'error_code' => 'E001'
        ], 401);
    }

    /**
     * (2) 管理者ログイン
     * POST /api/v1/admin/login
     */
    public function adminLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        // Emailで検索
        $admin = Admin::where('email', $request->email)->first();

        // 該当なし、またはパスワード不一致の場合: E001
        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json([
                'message' => 'メールアドレスまたはパスワードが間違っています。',
                'error_code' => 'E001'
            ], 401);
        }

        // トークン発行 (Sanctum)
        // api.php の middleware(['ability:admin_token']) を通過させるため権限を明示
        $token = $admin->createToken('admin_token', ['admin_token'])->plainTextToken;

        return response()->json([
            'token' => $token,
            'role' => 'admin',
            'admin' => [
                'id' => $admin->id,
                'name' => $admin->name,
                // Schoolリレーションがある場合のみ取得、なければ 'Unknown'
                'school_name' => $admin->school->name ?? 'Unknown School',
            ]
        ], 200);
    }

    /**
     * (3) ログアウト
     * POST /api/v1/logout
     */
    public function logout(Request $request)
    {
        // リクエストを送ってきたユーザー（クラス or 管理者）の現在のトークンを削除
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out'
        ], 200);
    }
}