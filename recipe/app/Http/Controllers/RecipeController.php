<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Recipe;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Psy\Readline\Hoa\Console;

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

        try {
            $user = Auth::guard('sanctum')->user();
            if ($user) {
                Recipe::create([
                    'user_id' => $user->id,
                    'image' => $request->image,
                    'title' => $request->title,
                    'description' => $request->description,
                    'ingredients' => $request->ingredients,
                    'instructions' => $request->instructions,
                    'rating' => 0,
                    'id_category' => $request->id_category,
                    'id_level' => $request->id_level,
                    'timecook' => $request->timecook,
                    'created_at' => now(),
                ]);

                return response()->json([
                    'status'            =>   200,
                    'message'           =>   'tạo công thức thành công!',
                ]);
            }
            return response()->json([
                'status'            =>   404,
                'message'           =>   'tạo công thức thất bại!',
            ]);
        } catch (Exception $e) {
            Log::info("error", $e);
            return response()->json([
                'status'            =>   404,
                'message'           =>   'error',
            ]);
        }
    }

    public function UpdateData(Request $request)
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if (!$user) {
                return response()->json([
                    'status' => 401,
                    'message' => 'Người dùng chưa đăng nhập.',
                ]);
            }

            $updated = Recipe::where('recipes.id', $request->id)
                ->where('user_id', '=', $user->id)
                ->update([
                    'user_id' => $user->id,
                    'title' => $request->title,
                    'description' => $request->description,
                    'ingredients' => $request->ingredients,
                    'instructions' => $request->instructions,
                    'image' => $request->image,
                    'rating' => 0,
                    'id_category' => $request->id_category,
                    'id_level' => $request->id_level,
                    'timecook' => $request->timecook,
                    'updated_at' => now(), // ✅ dùng đúng updated_at
                ]);

            if ($updated) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Cập nhật công thức thành công.',
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'Không tìm thấy công thức để cập nhật.',
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Lỗi khi cập nhật công thức', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi cập nhật.',
            ]);
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

    // lấy công thức theo cấp độ chọn
    // public function getSearchSuggestions(Request $request)
    // {
    //     $keyword = $request->input('keyword', '');

    //     if (empty($keyword)) {
    //         return response()->json([
    //             'status' => 200,
    //             'data' => [],
    //             'message' => 'Empty keyword'
    //         ]);
    //     }

    //     try {
    //         $suggestions = Recipe::where('title', 'like', '%' . $keyword . '%')
    //             ->select('title')
    //             ->distinct()
    //             ->limit(5) // Giới hạn 5 gợi ý
    //             ->get()
    //             ->pluck('title'); // Lấy ra mảng các title

    //         return response()->json([
    //             'status' => 200,
    //             'data' => $suggestions,
    //             'message' => 'Suggestions retrieved successfully'
    //         ]);
    //     } catch (Exception $e) {
    //         Log::error("Error getting search suggestions: " . $e->getMessage());
    //         return response()->json([
    //             'status' => 500,
    //             'message' => 'Error retrieving suggestions'
    //         ], 500);
    //     }
    // }
}
