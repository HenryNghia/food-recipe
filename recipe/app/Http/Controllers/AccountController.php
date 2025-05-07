<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;

class AccountController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('web')->attempt($credentials)) {
            $user = Auth::guard('web')->user();
            // Kiểm tra nếu user có id_roles bằng 2
            if ($user->id_roles == 2) {
                $token = $user->createToken('api-token-nghia')->plainTextToken;
                return response()->json(
                    [
                        'message' => 'Login Success',
                        'user' => $user,
                        'token' => $token
                    ],
                    200
                );
            } else {
                Auth::guard('api')->logout(); // Đăng xuất nếu không phải role 2
                return response()->json(
                    [
                        'message' => 'Đăng nhập không thành công.'
                    ],
                    403 // Forbidden
                );
            }
        } else {
            return response()->json(
                [
                    'message' => 'Login Failed'
                ],
                401
            );
        }
    }

    public function register(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if ($user) {
            return response()->json(['message' => 'Email đã tồn tại'], 400);
        } else {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                // Mặc định gán id_roles = 2 khi đăng ký (nếu không có logic khác)
                'id_roles' => 2,
            ]);
            return response()->json([
                'message'  => 'Tao tại tài khoản rồi nhé!',
                'status'   => true
            ]);
        }
    }

    public function logout()
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            DB::table('personal_access_tokens')
                ->where('id', $user->currentAccessToken()->id)
                ->delete();
            return response()->json([
                'message'  => 'Đã đăng xuất thành công!',
                'status'   => true,
            ]);
        } else {
            return response()->json([
                'message'  => 'Bạn cần đăng nhập hệ thống',
                'status'   => false,
            ]);
        }
    }

    public function logoutAll()
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $tokens = $user->tokens;
            foreach ($tokens as $key => $value) {
                $value->delete();
            }
            return response()->json([
                'message'  => 'Đã đăng xuất tất cả thành công!',
                'status'   => true,
            ]);
        } else {
            return response()->json([
                'message'  => 'Bạn cần đăng nhập hệ thống',
                'status'   => false,
            ]);
        }
    }

    //check token
    public function checkToken()
    {
        // Lấy người dùng hiện tại qua Sanctum
        $user = Auth::guard('sanctum')->user();

        if ($user) {
            // Lấy ID của token hiện tại
            $tokenId = $user->currentAccessToken()->id;

            // Kiểm tra trong bảng personal_access_tokens có tồn tại token với id này không
            $tokenExists = DB::table('personal_access_tokens')
                ->where('id', $tokenId)
                ->exists();

            if ($tokenExists) {
                return response()->json([
                    'status'  => true,
                    'message' => 'Bạn đang đăng nhập hệ thống',
                ], 200);
            } else {
                return response()->json([
                    'status'  => false,
                    'message' => 'Token không hợp lệ',
                ], 401);
            }
        } else {
            return response()->json([
                'status'  => false,
                'message' => 'Bạn cần đăng nhập hệ thống',
            ], 401);
        }
    }

    //lấy data của user đang đăng nhập
    public function GetData()
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            return response()->json([
                'data' => $user,
                'status' => 200,
                'message' => 'Lấy dữ liệu thành công!',
            ]);
        }
        return response()->json([
            'status' => 404,
            'message' => 'Lấy dữ liệu không thành công!',
        ]);
    }

    public function updateData(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $data = User::updated([
                'name' => $request->name,
                'avatar' => $request->avatar,
            ]);
            return response()->json([
                'status' => 200,
                'message' => 'Đã cập nhật thành công' . $request->name,
            ]);
        }
    }
}
