<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    public function GetData()
    {
        // lấy ra danh mục
        $data = Category::select('name_category', 'image')->get();
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

        $data = Category::select('id', 'name_category', 'image')
            ->where('name_category', 'like', $key)
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
            Category::create([
                'name_category' => $request->name_category,
                'image' => $request->image,
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
            Category::where('id', $request->id)
                ->update([
                    'name_category' => $request->name_category,
                    'image' => $request->image,
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
            Category::where('id', $request->id)
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
