<?php

namespace App\Http\Controllers\API;

use App\Exceptions\ProductNotCreatedException;
use App\Exceptions\ProductNotFoundException;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\ProductRequest;
use Exception;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function fetch(Request $request): object
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);

        $query = Product::with('user', 'photos')->where('user_id', Auth::id());

        // Get single data
        if ($id) {
            $product = $query->find($id);

            if ($product) {
                return ResponseFormatter::success($product, 'Product found');
            }

            return ResponseFormatter::error('Product not found', null, 404);
        }

        // Get multiple data
        $products = $query;

        if ($name) {
            $products->where('name', 'like', '%' . $name . '%');
        }

        return ResponseFormatter::success(
            $products->paginate($limit),
            'Products found'
        );
    }

    public function create(ProductRequest $request): object
    {
        try {
            // Create product
            $product = Product::create([
                'name' => $request->name,
                'sku' => $request->sku,
                'quantity' => $request->quantity,
                'price' => $request->price,
                'category_id' => $request->category_id,
                'user_id' => Auth::id()
            ]);

            if (!$product) {
                throw new ProductNotCreatedException('Product not created');
            }

            // Load relationship
            $product->load(['user', 'category']);

            return ResponseFormatter::success($product, 'Product created');
        } catch (Exception $e) {
            return ResponseFormatter::error('Product create error', $e->getMessage(), 500);
        }
    }

    public function update(ProductRequest $request, $id): object
    {
        try {
            // Get product
            $product = Product::find($id);

            // Check if product exists
            if (!$product) {
                throw new ProductNotFoundException('Product not found');
            }

            // Update product
            $product->update([
                'name' => $request->name,
                'sku' => $request->sku,
                'quantity' => $request->quantity,
                'price' => $request->price,
                'category_id' => $request->category_id,
                'user_id' => Auth::id()
            ]);

            return ResponseFormatter::success($product, 'Product updated');
        } catch (Exception $e) {
            return ResponseFormatter::error('Product update error', $e->getMessage(), 500);
        }
    }
}
