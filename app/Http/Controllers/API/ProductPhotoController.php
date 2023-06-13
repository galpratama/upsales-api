<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\API\ProductPhotoRequest;
use App\Models\ProductPhoto; // assuming ProductPhoto is the model for product photos

class ProductPhotoController extends Controller
{
    public function create(ProductPhotoRequest $request)
    {
        $productPhoto = ProductPhoto::create([
            'product_id' => $request->product_id,
            'path' => $request->photo->store('photos', 'public')
        ]);

        return ResponseFormatter::success($productPhoto, 'Product photo uploaded successfully');
    }

    public function destroy($id): object
    {
        $productPhoto = ProductPhoto::find($id);

        if (!$productPhoto) {
            return ResponseFormatter::error('Product photo not found', 404);
        }

        // Delete from storage
        Storage::disk('public')->delete($productPhoto->path);

        $productPhoto->delete();

        return ResponseFormatter::success(null, 'Product photo deleted successfully');
    }
}
