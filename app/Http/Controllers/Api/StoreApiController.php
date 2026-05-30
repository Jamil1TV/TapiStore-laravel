<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoreApiController extends Controller
{
    public function products(Request $request): JsonResponse
    {
        $products = Product::with(['category', 'brand', 'primaryImage'])
            ->published()
            ->filter($request)
            ->latest()
            ->paginate($request->integer('per_page', 12));

        return response()->json($products);
    }

    public function showProduct(Product $product): JsonResponse
    {
        abort_unless($product->is_active, 404);

        return response()->json($product->load(['category', 'brand', 'images', 'approvedReviews.user']));
    }

    public function categories(): JsonResponse
    {
        return response()->json(
            Category::with('children')
                ->whereNull('parent_id')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get()
        );
    }

    public function brands(): JsonResponse
    {
        return response()->json(Brand::where('is_active', true)->orderBy('name')->get());
    }

    public function featured(): JsonResponse
    {
        return response()->json(
            Product::with(['brand', 'primaryImage'])
                ->published()
                ->where('is_featured', true)
                ->latest()
                ->take(12)
                ->get()
        );
    }
}
