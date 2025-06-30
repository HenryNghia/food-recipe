<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function handleGoogleLogin(Request $request)
    {
        // 1. Xác thực request, đảm bảo có 'access_token'
        $request->validate([
            'access_token' => 'required',
        ]);

        try {
            // 2. Dùng access token để lấy thông tin người dùng từ Google
            $googleUser = Socialite::driver('google')->userFromToken($request->access_token);

            // 3. Tìm hoặc tạo người dùng mới
            $user = User::updateOrCreate(
                [
                    'google_id' => $googleUser->getId(),
                ],
                [
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'avatar' => $googleUser->getAvatar(),
                    'password' => Hash::make(Str::random(24)) // Tạo mật khẩu ngẫu nhiên
                ]
            );

            // 4. Tạo token cho người dùng (sử dụng Sanctum)
            $token = $user->createToken('auth-token')->plainTextToken;

            // 5. Trả về thông tin người dùng và token
            return response()->json([
                'message' => 'Đăng nhập bằng Google thành công!',
                'user' => $user,
                'token' => $token,
            ], 200);
        } catch (\Exception $e) {
            // Nếu có lỗi, trả về thông báo lỗi
            return response()->json(['error' => 'Xác thực không hợp lệ: ' . $e->getMessage()], 401);
        }
    }
}
