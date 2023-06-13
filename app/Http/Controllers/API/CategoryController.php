<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Category;

class CategoryController extends Controller
{
    public function fetch(Request $request): object
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);

        $query = Category::query();

        // Get single data
        if ($id) {
            $category = $query->find($id);

            if ($category) {
                return ResponseFormatter::success($category, 'Category found');
            }

            return ResponseFormatter::error('Category not found', 404);
        }

        // Get multiple data
        $categories = $query;

        if ($name) {
            $categories->where('name', 'like', '%' . $name . '%');
        }

        return ResponseFormatter::success(
            $categories->paginate($limit),
            'Categories found'
        );
    }
}
