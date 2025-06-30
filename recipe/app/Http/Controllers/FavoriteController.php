<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function GetDataByUser()
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $favorite = Favorite::where('favorites.user_id', $user->id)
                ->join('recipes', 'recipes.id', '=', 'favorites.recipe_id')
                ->join('users as recipe_owner', 'recipe_owner.id', '=', 'recipes.user_id') // alias ở đây
                ->select(
                    'recipes.id',
                    'recipes.title',
                    'recipes.rating',
                    'recipes.image',
                    'recipe_owner.name as recipe_owner_name', // tên người tạo công thức
                    'favorites.*',
                )
                ->orderBy('favorites.saved_date', 'desc')
                ->get();

            return response()->json([
                'data' => $favorite,
                'status' => 200,
                'message' => 'Lấy dữ liệu thành công',
            ]);
        } else {
            return response()->json([
                'status' => 401,
                'message' => 'Không xác thực',
            ]);
        }
    }

    public function checkfavorite()
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $favorites = Favorite::where('user_id', $user->id)
                ->select('recipe_id')
                ->get();

            if ($favorites->isNotEmpty()) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Đã có trong danh sách yêu thích',
                    'data' => $favorites,
                ]);
            } else {
                return response()->json([
                    'status' => 201,
                    'message' => 'Không có trong danh sách yêu thích',
                    'data' => [],
                ]);
            }
        } else {
            return response()->json([
                'status' => 401,
                'message' => 'Không xác thực',
            ], 401);
        }
    }
    // thêm vào công thức yêu thích
    public function createData(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $exists = Favorite::where('user_id', $user->id)
                ->where('recipe_id', $request->recipe_id)
                ->first();

            if ($exists) {
                return response()->json([
                    'status' => 201,
                    'message' => 'Đã có trong danh sách yêu thích'
                ], 200);
            }

            Favorite::create([
                'user_id' => $user->id,
                'recipe_id' => $request->recipe_id,
                'saved_date' => now(),
            ]);
            return response()->json([
                'status' => 200,
                'message' => 'Đã thêm vào danh sách yêu thích thành công',
            ]);
        }
    }

    public function DeleteData(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            Favorite::where('user_id', $user->id)
                ->where('recipe_id', $request->recipe_id)
                ->delete();
            return response()->json([
                'status' => 200,
                'message' => 'xóa dữ liệu thành công',
            ]);
        } else {
            return response()->json([
                'status' => 401,
                'message' => 'xóa không thành công',
            ]);
        }
    }

    public function CheckData(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $exists = Favorite::where('user_id', $user->id)
                ->where('recipe_id', $request->recipe_id)
                ->first();

            if ($exists) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Đã có trong danh sách yêu thích',
                ]);
            } else {
                return response()->json([
                    'status' => 201,
                    'message' => 'Không có trong danh sách yêu thích',
                ]);
            }
        }
    }
}
