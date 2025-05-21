<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Recipe;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http; // Hoặc dùng Guzzle trực tiếp
use Illuminate\Support\Facades\Log;
use Psy\Readline\Hoa\Console;
use GuzzleHttp\Client; // Thêm dòng này nếu dùng Guzzle trực tiếp
use GuzzleHttp\Exception\RequestException; // Thêm để bắt lỗi Guzzle
use Illuminate\Support\Facades\Validator;

class RecipeController extends Controller
{
    public function GetData()
    {
        $data = Recipe::join('users', 'users.id', 'recipes.user_id')
            ->join('categories', 'categories.id', 'recipes.id_category')
            ->join('levels', 'levels.id', 'recipes.id_level')
            ->select(
                'users.name',
                'users.avatar',
                'categories.name_category',
                'levels.name_level',
                'recipes.*',
            )
            ->orderBy('recipes.rating', 'desc') // Sắp xếp theo rating giảm dần (cao nhất trước)
            ->orderBy('recipes.created_at', 'desc')
            ->get();
        $data = $data->map(function ($item) {
            $item->description = $item->description ? explode('\\', $item->description) : [];
            $item->instructions = $item->instructions ? explode('\\', $item->instructions) : [];
            $item->ingredients = $item->ingredients ? explode(',', $item->ingredients) : [];
            return $item;
        });
        if ($data) {
            return response()->json(
                [
                    'status' => 200,
                    'data' => $data,
                    'message' => 'success'
                ]
            );
        }
        return response()->json(
            [
                'status' => 404,
                'message' => 'Không tìm thấy dữ liệu'
            ],
            404
        );
    }

    public function GetDataByRating()
    {
        $data = Recipe::join('users', 'users.id', 'recipes.user_id')
            ->join('categories', 'categories.id', 'recipes.id_category')
            ->join('levels', 'levels.id', 'recipes.id_level')
            ->where('recipes.rating', '>', 4)
            ->select(
                'users.name',
                'users.avatar',
                'categories.name_category',
                'levels.name_level',
                'recipes.*',
            )
            ->orderBy('recipes.rating', 'desc') // Sắp xếp theo rating giảm dần (cao nhất trước)
            ->orderBy('recipes.created_at', 'desc') // *** Thêm sắp xếp theo thời gian tạo giảm dần (mới nhất trước) ***
            ->get();
        $data = $data->map(function ($item) {
            $item->description = $item->description ? explode('\\', $item->description) : [];
            $item->instructions = $item->instructions ? explode('\\', $item->instructions) : [];
            $item->ingredients = $item->ingredients ? explode(',', $item->ingredients) : [];
            return $item;
        });
        if ($data) {
            return response()->json(
                [
                    'status' => 200,
                    'data' => $data,
                    'message' => 'success'
                ]
            );
        }
        return response()->json(
            [
                'status' => 404,
                'message' => 'Không tìm thấy dữ liệu'
            ],
            404
        );
    }

    public function GetDataByTime()
    {
        $data = Recipe::join('users', 'users.id', 'recipes.user_id')
            ->join('categories', 'categories.id', 'recipes.id_category')
            ->join('levels', 'levels.id', 'recipes.id_level')
            ->select(
                'users.name',
                'users.avatar',
                'categories.name_category',
                'levels.name_level',
                'recipes.*',
            )
            ->orderBy('recipes.created_at', 'desc') // *** Thêm sắp xếp theo thời gian tạo giảm dần (mới nhất trước) ***
            ->get();
        $data = $data->map(function ($item) {
            $item->description = $item->description ? explode('\\', $item->description) : [];
            $item->instructions = $item->instructions ? explode('\\', $item->instructions) : [];
            $item->ingredients = $item->ingredients ? explode(',', $item->ingredients) : [];
            return $item;
        });
        if ($data) {
            return response()->json(
                [
                    'status' => 200,
                    'data' => $data,
                    'message' => 'success'
                ]
            );
        }
        return response()->json(
            [
                'status' => 404,
                'message' => 'Không tìm thấy dữ liệu'
            ],
            404
        );
    }
    public function GetDataById($id)
    {
        $data = Recipe::where('recipes.id', '=', $id) // *** QUAN TRỌNG: Lọc theo ID của công thức ***
            ->join('users', 'users.id', 'recipes.user_id')
            ->join('levels', 'levels.id', 'recipes.id_level')
            ->join('categories', 'categories.id',  'recipes.id_category')
            ->select(
                'users.name',
                'users.avatar',
                'categories.name_category',
                'levels.name_level',
                'recipes.*',
            )
            ->first();

        if ($data) {
            // ✅ Chuyển các trường thành mảng nếu cần
            $data->description = $data->description ? explode('\\', $data->description) : [];
            $data->instructions = $data->instructions ? explode('\\', $data->instructions) : [];
            $data->ingredients = $data->ingredients ? explode(',', $data->ingredients) : [];

            return response()->json([
                'status' => 200,
                'data' => $data,
                'message' => 'Recipe found successfully.'
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Recipe not found.'
            ], 404);
        }
    }

    public function SearchData(Request $request)
    {
        $key = "%" . $request->abc . "%";
        $categoryId = $request->id;
        $data = Recipe::join('users', 'users.id', 'recipes.user_id')
            ->join('levels', 'levels.id', 'recipes.id_level')
            ->join('categories', 'categories.id', 'recipes.id_category')
            ->where('title', 'like', $key)
            ->where('recipes.id_category', $categoryId)
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

    // tìm kiếm tất cả công thức
    public function SearchDataALL(Request $request)
    {
        $keyword = "%" . $request->abc . "%";

        $data = Recipe::join('users', 'users.id', 'recipes.user_id')
            ->join('levels', 'levels.id', 'recipes.id_level')
            ->join('categories', 'categories.id', 'recipes.id_category')
            ->where(function ($query) use ($keyword) {
                $query->where('recipes.title', 'like', $keyword)
                    ->orWhere('categories.name_category', 'like', $keyword);
            })
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

        if ($data->isNotEmpty()) {
            return response()->json([
                'status' => 200,
                'data' => $data,
                'message' => 'Tìm kiếm thành công'
            ]);
        }

        return response()->json([
            'status' => 404,
            'message' => 'Cong thức không tồn tại'
        ]);
    }

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

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Dữ liệu gửi lên không hợp lệ.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // === Bước 2: Xử lý Upload File ảnh ===
            $imageUrl = null;
            if ($request->hasFile('image')) { // Kiểm tra file có được gửi lên không.
                $imageFile = $request->file('image'); // Lấy đối tượng UploadedFile.
                $originalName = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $imageFile->getClientOriginalExtension();
                $imageName = $originalName . '_' . time() . '.' . $extension;
                // Lưu file vào disk 'public' (thường là storage/app/public), trong thư mục 'recipes'.
                $imagePath = $imageFile->storeAs('user/recipes', $imageName, 'public');
                $imageUrl = asset('storage/' . $imagePath);
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
            ]);

            // === Bước 4: Trả về Response thành công ===
            return response()->json([
                'status' => true,
                'message' => 'Tạo công thức thành công!',
                'recipe' => $recipe,
            ], 201);
        } catch (Exception $e) {
            // === Bước 5: Xử lý các Exception không mong muốn ===
            Log::error("Lỗi khi tạo công thức: " . $e->getMessage(), [
                'exception_details' => $e->getTraceAsString(), // Cung cấp thêm chi tiết cho việc debug
                'request_data' => $request->except(['image']), // Loại bỏ dữ liệu file lớn khỏi log nếu cần
                'user_id' => $user->id ?? null
            ]);
            return response()->json([
                'status' => false,
                'message' => 'Đã xảy ra lỗi server khi tạo công thức. Vui lòng thử lại sau.',
            ], 500);

        }
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
            $validator = Validator::make($request->all(), [
                'title' => ['sometimes', 'string', 'max:255'],
                'description' => ['sometimes', 'string'],
                'image' => ['sometimes', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
                'ingredients' => ['sometimes', 'string'],
                'instructions' => ['sometimes', 'string'],
                'id_category' => ['sometimes', 'integer', 'exists:categories,id'],
                'id_level' => ['sometimes', 'integer', 'exists:levels,id'],
                'timecook' => ['sometimes', 'string'],
            ]);

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

            // === Bước 2: Xử lý Upload File ảnh ===
            $imageUrl = $recipe->image;
            if ($request->hasFile('image')) {
                $imageFile = $request->file('image');
                $originalName = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $imageFile->getClientOriginalExtension();
                $imageName = $originalName . '_' . time() . '.' . $extension;
                // Lưu file vào disk 'public' (thường là storage/app/public), trong thư mục 'recipes'.
                $imagePath = $imageFile->storeAs('user/avatars', $imageName, 'public');
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
                'message' => 'Cập nhật thành công!',
                'recipe' => $recipe,
            ], 201);
        } catch (Exception $e) {
            // === Bước 5: Xử lý các Exception không mong muốn ===
            Log::error("Lỗi khi tạo công thức: " . $e->getMessage(), ['exception' => $e, 'request_data' => $request->all(), 'user_id' => $user->id ?? null]);
            return response()->json([
                'status' => false,
                'message' => 'Đã xảy ra lỗi server khi cập nhật công thức. Vui lòng thử lại sau.',
            ], 500);
        }
    }

    public function deleteData(Request $request)
    {
        try {
            Recipe::where('recipes.id', $request->id)
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

    //lấy công thức theo danh mục chọn
    public function GetDataByCategory($categoryId)
    {
        $data = Recipe::join('users', 'users.id', 'recipes.user_id')
            ->join('categories', 'categories.id', 'recipes.id_category')
            ->join('levels', 'levels.id', 'recipes.id_level')
            ->where('recipes.id_category', $categoryId)
            ->select(
                'users.name',
                'users.avatar',
                'categories.name_category',
                'levels.name_level',
                'recipes.*',
            )
            ->orderBy('recipes.created_at', 'desc')
            ->get();
        $data = $data->map(function ($item) {
            $item->description = $item->description ? explode('\\', $item->description) : [];
            $item->instructions = $item->instructions ? explode('\\', $item->instructions) : [];
            $item->ingredients = $item->ingredients ? explode(',', $item->ingredients) : [];
            return $item;
        });
        if ($data) {
            return response()->json(
                [
                    'status' => 200,
                    'data' => $data,
                    'message' => 'success'
                ]
            );
        }
        return response()->json(
            [
                'status' => 404,
                'message' => 'Không tìm thấy dữ liệu'
            ],
            404
        );
    }

    public function GetDataByUser()
    {
        try {
            $user = Auth::guard('sanctum')->user();
            if ($user) {
                if ($user->id_roles === 2) {
                    $data = Recipe::join('users', 'users.id', 'recipes.user_id')
                        ->join('categories', 'categories.id', 'recipes.id_category')
                        ->join('levels', 'levels.id', 'recipes.id_level')
                        ->where('recipes.user_id', $user->id)
                        ->select(
                            'users.name',
                            'users.avatar',
                            'categories.name_category',
                            'levels.name_level',
                            'recipes.*',
                        )
                        ->orderBy('recipes.created_at', 'desc')
                        ->get();
                    $data = $data->map(function ($item) {
                        $item->description = $item->description ? explode('\\', $item->description) : [];
                        $item->instructions = $item->instructions ? explode('\\', $item->instructions) : [];
                        $item->ingredients = $item->ingredients ? explode(',', $item->ingredients) : [];
                        return $item;
                    });
                    if ($data) {
                        return response()->json(
                            [
                                'status' => 200,
                                'data' => $data,
                                'message' => 'success'
                            ]
                        );
                    }
                    return response()->json(
                        [
                            'status' => 404,
                            'message' => 'Không tìm thấy dữ liệu'
                        ],
                        404
                    );
                } else {
                    return response()->json(
                        [
                            'status' => 404,
                            'message' => 'Không có quyền truy cập',
                            'data' => $user,
                        ],
                        404
                    );
                }
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
