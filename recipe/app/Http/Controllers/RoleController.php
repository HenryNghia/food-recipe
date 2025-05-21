<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function GetData()
    {
        $data = Role::get();
        return response()->json(
            [
                'status' => 200,
                'message' => 'lấy role thành công',
                'data' => $data,
            ]
        );
    }
}
