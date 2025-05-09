<?php

namespace App\Http\Controllers;

use App\Models\Level;
use Illuminate\Http\Request;

class LevelController extends Controller
{
    public function GetData()
    {
        // lấy ra danh mục
        $data = Level::get();
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

        $data = Level::select('id', 'name_level', 'image')
            ->where('name_level', 'like', $key)
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
        $data = new Level();
        $data->name_level = $request->name_level;
        $data->image = $request->image;
        if ($data->save()) {
            return response()->json(
                [
                    'status' => 200,
                    'message' => 'create success'
                ]
            );
        }
        return response()->json(
            [
                'status' => 404,
                'message' => 'create fail'
            ],
            404
        );
    }
    
    public function UpdateData(Request $request)
    {
        $data = Level::find($request->id);
        if ($data) {
            $data->name_level = $request->name_level;
            $data->image = $request->image;
            if ($data->save()) {
                return response()->json(
                    [
                        'status' => 200,
                        'message' => 'update success'
                    ]
                );
            }
        }
        return response()->json(
            [
                'status' => 404,
                'message' => 'update fail'
            ],
            404
        );
    }

    public function DeleteData(Request $request)
    {
        $data = Level::find($request->id);
        if ($data) {
            if ($data->delete()) {
                return response()->json(
                    [
                        'status' => 200,
                        'message' => 'delete success'
                    ]
                );
            }
        }
        return response()->json(
            [
                'status' => 404,
                'message' => 'delete fail'
            ],
            404
        );
    }
}
