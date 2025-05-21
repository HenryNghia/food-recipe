<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class RecipeAdminController extends Controller
{
    public function CreateData(Request $request)
    {
        // Kiểm tra xác thực thủ công (nếu không dùng middleware 'auth:sanctum' trên route)
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Yêu cầu xác thực. Vui lòng đăng nhập.',
            ], 401);
        }

        try {
            // === Bước 1: Validation dữ liệu từ Request ===
            $validator = Validator::make($request->all(), [
                'title' => ['required', 'string', 'max:255'],
                'description' => ['required', 'string'],
                'image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
                'ingredients' => ['required', 'string'],
                'instructions' => ['required', 'string'],
                'id_category' => ['required', 'integer', 'exists:categories,id'],
                'id_level' => ['required', 'integer', 'exists:levels,id'],
                'timecook' => ['required', 'string'],
            ]);

            // Nếu validation thất bại
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Dữ liệu gửi lên không hợp lệ.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // === Bước 2: Xử lý Upload File ảnh ===
            $imageUrl = null;
            if ($request->hasFile('image')) {
                $imageFile = $request->file('image');
                $imageName = time() . '_' . $imageFile->getClientOriginalName();
                $imagePath = $imageFile->storeAs('recipes', $imageName, 'public');

                // Lấy URL công khai để truy cập file
                $imageUrl = asset('storage/' . $imagePath);
                // Đảm bảo bạn đã cấu hình Storage::disk('public') và tạo symbolic link (php artisan storage:link)
            }

            // === Bước 3: Tạo bản ghi Recipe trong Database ===
            $recipe = Recipe::create([
                'user_id' => $user->id,
                'image' => $imageUrl,
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'ingredients' => $request->input('ingredients'),
                'instructions' => $request->input('instructions'),
                'rating' => 0,
                'id_category' => $request->input('id_category'),
                'id_level' => $request->input('id_level'),
                'timecook' => $request->input('timecook'),
                // 'created_at' => now(),
            ]);

            // === Bước 4: Trả về Response thành công ===
            return response()->json([
                'status' => true,
                'message' => 'Tạo công thức thành công!',
                'recipe' => $recipe,
            ], 201);
        } catch (Exception $e) {
            // === Bước 5: Xử lý các Exception không mong muốn ===
            Log::error("Lỗi khi tạo công thức: " . $e->getMessage(), ['exception' => $e, 'request_data' => $request->all(), 'user_id' => $user->id ?? null]);
            return response()->json([
                'status' => false,
                'message' => 'Đã xảy ra lỗi server khi tạo công thức. Vui lòng thử lại sau.',
            ], 500);
        }
    }

    public function SearchData(Request $request)
    {
        $key = "%" . $request->abc . "%";
        $data = Recipe::join('users', 'users.id', 'recipes.user_id')
            ->join('levels', 'levels.id', 'recipes.id_level')
            ->join('categories', 'categories.id', 'recipes.id_category')
            ->where('title', 'like', $key)
            ->select(
                'recipes.id',
                'users.name',
                'title',
                'description',
                'ingredients',
                'instructions',
                'recipes.image',
                'categories.name_category',
                'levels.name_level',
                'rating',
                'timecook',
            )
            ->get();
        if ($data) {
            return response()->json(
                [
                    'status' => 200,
                    'data' => $data,
                    'message' => 'Tìm kiếm thành công'
                ]
            );
        }
        return response()->json(
            [
                'status' => 404,
                'message' => 'Công thưc không tồn tại'
            ]
        );
    }

    public function UpdateData(Request $request)
    {
        // Kiểm tra xác thực thủ công (nếu không dùng middleware 'auth:sanctum' trên route)
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Yêu cầu xác thực. Vui lòng đăng nhập.',
            ], 401); // 401 Unauthorized là mã chuẩn
        }

        try {
            // === Bước 1: Validation dữ liệu từ Request ===
            // Back-end validation là cần thiết ngay cả khi front-end đã validate
            $validator = Validator::make($request->all(), [
                'title' => ['required', 'string', 'max:255'],
                'description' => ['required', 'string'],
                'image' => ['sometimes', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
                'ingredients' => ['required', 'string'],
                'instructions' => ['required', 'string'],
                'id_category' => ['required', 'integer', 'exists:categories,id'],
                'id_level' => ['required', 'integer', 'exists:levels,id'],
                'timecook' => ['required', 'string'],
            ]);

            // Nếu validation thất bại
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Dữ liệu gửi lên không hợp lệ.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $recipe = Recipe::find($request->id);
            if (!$recipe) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy công thức để cập nhật.',
                ], 404);
            }

            $imageUrl = $recipe->image; // giữ ảnh cũ mặc định
            if ($request->hasFile('image')) {
                $imageFile = $request->file('image');
                $imageName = time() . '_' . $imageFile->getClientOriginalName();
                $imagePath = $imageFile->storeAs('recipes', $imageName, 'public');
                $imageUrl = asset('storage/' . $imagePath);
            }

            // === Bước 3: Cập nhật bản ghi Recipe trong Database ===
            $recipe->update([
                'user_id' => $user->id,
                'image' => $imageUrl,
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'ingredients' => $request->input('ingredients'),
                'instructions' => $request->input('instructions'),
                'rating' => 0,
                'id_category' => $request->input('id_category'),
                'id_level' => $request->input('id_level'),
                'timecook' => $request->input('timecook'),
            ]);

            // === Bước 4: Trả về Response thành công ===
            return response()->json([
                'status' => true,
                'message' => 'Tạo update thành công!',
                'recipe' => $recipe,
            ], 201);
        } catch (Exception $e) {
            // === Bước 5: Xử lý các Exception không mong muốn ===
            Log::error("Lỗi khi tạo công thức: " . $e->getMessage(), ['exception' => $e, 'request_data' => $request->all(), 'user_id' => $user->id ?? null]);
            return response()->json([
                'status' => false,
                'message' => 'Đã xảy ra lỗi server khi update công thức. Vui lòng thử lại sau.',
            ], 500);
        }
    }

    public function DeleteData($id)
    {
        try {
            Recipe::where('recipes.id', $id)
                ->delete();
            return response()->json([
                'status'            =>   200,
                'message'           =>   'Xóa danh mục thành công!',
            ]);
        } catch (Exception $e) {
            Log::info("error", $e);
            return response()->json([
                'status'            =>   404,
                'message'           =>   'error',
            ]);
        }
    }
}
