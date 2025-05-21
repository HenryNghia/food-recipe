<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
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

    public function loginAdmin(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::guard('web')->attempt($credentials)) {
            $user = Auth::guard('web')->user();
            if ($user->id_roles == 1) {
                $token = $user->createToken('api-token-nghia')->plainTextToken;
                return response()->json(
                    [
                        'message' => 'Login Success',
                        'user' => $user,
                        'token' => $token,
                        'status' => 200
                    ],
                    200
                );
            } else {
                Auth::guard('api')->logout(); // Đăng xuất nếu không phải role 2
                return response()->json(
                    [
                        'message' => 'Đăng nhập không thành công.',
                        'status' => 403
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
                'status'   => 200,
            ]);
        } else {
            return response()->json([
                'message'  => 'Bạn cần đăng nhập hệ thống',
                'status'   => 404,
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
                    'status'  => 200,
                    'message' => 'Bạn đang đăng nhập hệ thống',
                    'email'     =>  $user->email,
                    'name'    =>  $user->name,
                    'list'      =>  $user->tokens,
                ], 200);
            } else {
                return response()->json([
                    'status'  => 404,
                    'message' => 'Token không hợp lệ',
                ], 401);
            }
        } else {
            return response()->json([
                'status'  => 401,
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
        if (!$user) {
            return response()->json([
                'status' => 404,
                'message' => 'chưa đăng nhập!',
            ]);
        }

        try {
            // === Bước 1: Validation dữ liệu từ Request ===
            $validator = Validator::make($request->all(), [
                'name' => ['sometimes', 'string', 'max:255'],
                'avatar' => ['sometimes', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            ]);

            // Nếu validation thất bại
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Dữ liệu gửi lên không hợp lệ.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $data = $user;
            if (!$data) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy người dùng để cập nhật.',
                ], 404);
            }

            $imageUrl = $user->avatar;
            if ($request->hasFile('avatar')) {
                $imageFile = $request->file('avatar');
                $originalName = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $imageFile->getClientOriginalExtension();
                $imageName = $originalName . '_' . time() . '.' . $extension;
                $imagePath = $imageFile->storeAs('user/avatars', $imageName, 'public');
                $imageUrl = asset('storage/' . $imagePath);
            }

            // === Bước 3: Cập nhật bản ghi Recipe trong Database ===
            $data->update([
                'avatar' => $imageUrl,
                'name' => $request->input('name'),
                'email' => $user->email,
                'id_roles' => $user->id_roles,
            ]);

            // === Bước 4: Trả về Response thành công ===
            return response()->json([
                'status' => true,
                'message' => 'Tạo update thành công!',
                'data' => $data,
            ], 201);
        } catch (Exception $e) {
            // === Bước 5: Xử lý các Exception không mong muốn ===
            Log::error("Lỗi khi tạo danh mục: " . $e->getMessage(), ['exception' => $e, 'request_data' => $request->all()]);
            return response()->json([
                'status' => false,
                'message' => 'Đã xảy ra lỗi server khi update danh mục. Vui lòng thử lại sau.',
            ], 500);
        }
    }
}
