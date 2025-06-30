<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ManageUserController extends Controller
{
    public function GetData()
    {
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json(
                [
                    'message' => 'chưa đăng nhập tài khoản admin'
                ]
            );
        }
        if ($user->id_roles == 1) {
            $data = User::join('roles', 'users.id_roles', '=', 'roles.id')
                ->select(
                    'roles.name_role',
                    'users.id',
                    'name',
                    'password',
                    'email',
                    'avatar',
                    'id_roles'
                )
                ->get();
            return response()->json(
                [
                    'status' => 200,
                    'data' => $data,
                    'message' => 'Lấy dữ liệu thành công'
                ]
            );
        } else {
            return response()->json(
                [
                    'message' => 'không có phải admin'
                ]
            );
        }
    }

    public function GetDataByRoleAdmin()
    {
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json(
                [
                    'message' => 'chưa đăng nhập tài khoản admin'
                ]
            );
        }
        if ($user->id_roles == 1) {
            $data = User::join('roles', 'users.id_roles', '=', 'roles.id')
                ->where('id_roles', '=', 1)
                ->select(
                    'roles.name_role',
                    'users.id',
                    'name',
                    'password',
                    'email',
                    'avatar',
                    'id_roles'
                )
                ->get();
            return response()->json(
                [
                    'status' => 200,
                    'data' => $data,
                    'message' => 'Lấy dữ liệu thành công'
                ]
            );
        } else {
            return response()->json(
                [
                    'message' => 'không có phải admin'
                ]
            );
        }
    }

    public function GetDataByRoleUser()
    {
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json(
                [
                    'message' => 'chưa đăng nhập tài khoản admin'
                ]
            );
        }
        if ($user->id_roles == 1) {
            $data = User::join('roles', 'users.id_roles', '=', 'roles.id')
                ->where('id_roles', '=', 2)
                ->select(
                    'roles.name_role',
                    'users.id',
                    'name',
                    'password',
                    'email',
                    'avatar',
                    'id_roles'
                )
                ->get();
            return response()->json(
                [
                    'status' => 200,
                    'data' => $data,
                    'message' => 'Lấy dữ liệu thành công'
                ]
            );
        } else {
            return response()->json(
                [
                    'message' => 'không có phải admin'
                ]
            );
        }
    }

    public function SearchData(Request $request)
    {
        $key = "%" . $request->abc . "%";

        $data = User::join('roles', 'users.id_roles', '=', 'roles.id')
            ->where('name', 'like', $key)
            ->select(
                'roles.name_role',
                'users.*',
            )
            ->get();

        if ($data->isEmpty()) {
            return response()->json(
                [
                    'status' => 404,
                    'message' => 'Không tìm thấy dữ liệu phù hợp'
                ],
            );
        }
        return response()->json(
            [
                'status' => 200,
                'data' => $data,
                'message' => 'Tìm kiếm thành công'
            ],
            200
        );
    }

    public function deleteData($id)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Người dùng không tồn tại!',
                ]);
            }

            $user->delete(); // hoặc $user->forceDelete(); nếu không dùng soft deletes

            return response()->json([
                'status' => 200,
                'message' => 'Xóa thành công!',
            ]);
        } catch (Exception $e) {
            Log::error("Lỗi khi xóa user: " . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi xóa!',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function updateData(Request $request)
    {
        try {
            // === Bước 1: Validation dữ liệu từ Request ===
            $validator = Validator::make($request->all(), [
                'name' => ['sometimes', 'string', 'max:255'],
                'avatar' => ['sometimes', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
                'email' => ['sometimes', 'string', 'max:255'],
                'id_roles' => ['sometimes', 'integer'],
            ]);

            // Nếu validation thất bại
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Dữ liệu gửi lên không hợp lệ.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $user = User::find($request->id);
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy người dùng để cập nhật.',
                ], 404);
            }

            $imageUrl = $user->avatar;
            if ($request->hasFile('avatar')) {
                $imageFile = $request->file('avatar');
                $imageName = time() . '_' . $imageFile->getClientOriginalName();
                $imagePath = $imageFile->storeAs('user/avatars', $imageName, 'public');
                $imageUrl = asset('storage/' . $imagePath);
            }

            // === Bước 3: Cập nhật bản ghi Recipe trong Database ===
            $user->update([
                'avatar' => $imageUrl,
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'id_roles' => $request->input('id_roles'),
            ]);

            // === Bước 4: Trả về Response thành công ===
            return response()->json([
                'status' => true,
                'message' => 'Tạo update thành công!',
                'category' => $user,
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
