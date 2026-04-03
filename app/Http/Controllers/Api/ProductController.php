<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request)
    {
        return response()->json(['data' => $this->productService->getSellerProducts($request->user()->id)]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'image' => 'nullable|string'
        ]);

        $product = $this->productService->createProduct($request->user()->id, $validated);
        return response()->json(['data' => $product], 201);
    }

    public function show($id)
    {
        // ... (can just query directly or via service)
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric',
            'stock' => 'sometimes|integer',
            'category_id' => 'sometimes|exists:categories,id',
            'image' => 'nullable|string'
        ]);

        $product = $this->productService->updateProduct($id, $request->user()->id, $validated);
        return response()->json(['data' => $product]);
    }

    public function destroy(Request $request, $id)
    {
        $this->productService->deleteProduct($id, $request->user()->id);
        return response()->json(['message' => 'Product deleted']);
    }
}
