<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * (1) クラスログイン
     * POST /api/v1/login
     */
    public function login(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:20',
        ]);

        // クラスコードで検索
        $class = SchoolClass::where('code', $request->code)->first();

        // 該当なしの場合: E001
        if (!$class) {
            return response()->json([
                'message' => 'クラスコード（またはメールアドレス/パスワード）が間違っています。',
                'error_code' => 'E001'
            ], 401);
        }

        // トークン発行 (Sanctum)
        $token = $class->createToken('class_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'class' => [
                'id' => $class->id,
                'name' => $class->name,
                'locale' => $class->locale,
            ]
        ], 200);
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
                'message' => 'クラスコード（またはメールアドレス/パスワード）が間違っています。',
                'error_code' => 'E001'
            ], 401);
        }

        // トークン発行 (Sanctum)
        // 管理者権限を表すために ability を追加することも可能ですが今回はシンプルに
        $token = $admin->createToken('admin_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'admin' => [
                'id' => $admin->id,
                'name' => $admin->name,
                'school_name' => $admin->school->name ?? 'Unknown School', // Schoolリレーションが必要
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