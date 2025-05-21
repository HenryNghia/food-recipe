<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function GetData()
    {
        // lấy ra danh mục
        $data = Category::all()->map(function ($cat) {
            // Thêm trường image_url
            $cat->image_url = $cat->image
                ? Storage::url($cat->image)
                : null;
            return $cat;
        });

        if ($data->isEmpty()) {
            return response()->json([
                'status'  => 404,
                'message' => 'Không tìm thấy dữ liệu'
            ], 404);
        }

        return response()->json([
            'status'  => 200,
            'data'    => $data,
            'message' => 'success'
        ]);
    }

    public function SearchData(Request $request)
    {
        $key = "%" . $request->abc . "%";

        // Thực hiện truy vấn tìm kiếm
        $data = Category::select('id', 'name_category', 'image')
            ->where('name_category', 'like', $key)
            ->get();

        // Kiểm tra nếu Collection rỗng sử dụng phương thức isEmpty()
        if ($data->isEmpty()) {
            // Trả về response JSON với status 404 (Not Found)
            return response()->json(
                [
                    'status' => 404,
                    'message' => 'Không tìm thấy dữ liệu phù hợp' // Tin nhắn rõ ràng hơn
                ],
            );
        }

        // Nếu tìm thấy dữ liệu
        return response()->json(
            [
                'status' => 200,
                'data' => $data,
                'message' => 'Tìm kiếm thành công' // Tin nhắn thành công
            ],
            200 // Trả về mã HTTP status 200
        );
    }

    public function CreateData(Request $request)
    {
        // 1. Validate inputs
        $request->validate([
            'name_category' => 'required|string|max:255',
            'image'         => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        try {
            // 2. Xử lý upload file
            if ($request->hasFile('image')) {
                $image      = $request->file('image');
                $fileName   = time() . '_' . $image->getClientOriginalName();
                $filePath   = $image->storeAs('categories', $fileName, 'public');
                $imageUrl = asset('storage/' . $filePath);
            } else {
                // (Tùy chọn) nếu không có file thì đặt mặc định
                $filePath = null;
            }

            // 3. Tạo bản ghi category
            $category = Category::create([
                'name_category' => $request->name_category,
                'image'         =>  $imageUrl,
            ]);

            return response()->json([
                'status'  => 200,
                'message' => 'Create data success!',
                'data'    => $category,
            ]);
        } catch (\Exception $e) {
            // Log chi tiết lỗi
            Log::error('Error creating category: ' . $e->getMessage());

            return response()->json([
                'status'  => 500,
                'message' => 'Error creating data',
            ], 500);
        }
    }
    public function UpdateData(Request $request)
    {
        try {
            // === Bước 1: Validation dữ liệu từ Request ===
            $validator = Validator::make($request->all(), [
                'name_category' => ['sometimes', 'string', 'max:255'],
                'image' => ['sometimes', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            ]);

            // Nếu validation thất bại
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Dữ liệu gửi lên không hợp lệ.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $category = Category::find($request->id);
            if (!$category) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy công thức để cập nhật.',
                ], 404);
            }

            $imageUrl = $category->image; // giữ ảnh cũ mặc định
            if ($request->hasFile('image')) {
                $imageFile = $request->file('image');
                $imageName = time() . '_' . $imageFile->getClientOriginalName();
                $imagePath = $imageFile->storeAs('categories', $imageName, 'public');
                $imageUrl = asset('storage/' . $imagePath);
            }

            // === Bước 3: Cập nhật bản ghi Recipe trong Database ===
            $category->update([
                'image' => $imageUrl,
                'name_category' => $request->input('name_category'),
            ]);

            // === Bước 4: Trả về Response thành công ===
            return response()->json([
                'status' => true,
                'message' => 'Tạo update thành công!',
                'category' => $category,
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


    public function deleteData($id)
    {
        try {
            Category::where('id', $id)
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
