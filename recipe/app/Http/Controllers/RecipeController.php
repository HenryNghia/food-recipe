<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RecipeController extends Controller
{
    public function GetData()
    {
        // lấy ra danh mục
        $data = Recipe::join('accounts', 'accounts.id', 'recipes.user.id')
        ->select(
            'accounts.user_name',
            'title',
            'description',
            'ingredients',
            'instructions',
            'image',
            'category',
        )->get();
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

    public function SearchData(Request $request)
    {
        $key = "%" . $request->abc . "%";

        $data = Recipe::join('accounts', 'accounts.id', 'recipes.user_id')
                        ->join('levels' , 'levels.id', 'recipes.id_level')
            ->where('title', 'like', $key)
            ->select(
                'id',
                'accounts.user_name',
                'title',
                'description',
                'ingredients',
                'instructions',
                'image',
                'category',
                'levels.name_level',
                'time_cook',
            )
            ->get();
        if ($data) {
            return response()->json(
                [
                    'status' => 200,
                    'data' => $data,
                    'message' => 'search success'
                ]
            );
        }
        return response()->json(
            [
                'status' => 404,
                'message' => 'not found data'
            ],
            404
        );
    }

    public function CreateData(Request $request)
    {
        try {
            Recipe::create([
                'name_category' => $request->name_category,
                'image' => $request->image,
                'user_id' => $request->user_id,
                'title' => $request->title,
                'description' => $request->description,
                'ingredients' => $request->ingredients,
                'instructions' => $request->instructions,
                'image' => $request->image,
                'category' => $request->category,
                'id_level' => $request->id_level,
                'time_cook' => $request->time_cook,
            ]);

            return response()->json([
                'status'            =>   200,
                'message'           =>   'create data success!',
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
            Recipe::where('id', $request->id)
                ->update([
                    'user_id' => $request->user_id,
                    'title' => $request->title,
                    'description' => $request->description,
                    'ingredients' => $request->ingredients,
                    'instructions' => $request->instructions,
                    'image' => $request->image,
                    'category' => $request->category,
                    'id_level' => $request->id_level,
                    'time_cook' => $request->time_cook,
                ]);
        } catch (Exception $e) {
            Log::info("error", $e);
            return response()->json([
                'status'            =>   404,
                'message'           =>   'error',
            ]);
        }
    }
    public function deleteData(Request $request)
    {
        try {
            Recipe::where('id', $request->id)
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
