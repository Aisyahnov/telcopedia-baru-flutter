<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProductService;
use App\Models\Category;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request)
    {
        $products = $this->productService->getSellerProducts($request->user()->id);
        return view('seller.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('seller.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'image' => 'nullable|string' // Idealnya diganti jadi |image|max:2048 lalu pake store()
        ]);

        $this->productService->createProduct($request->user()->id, $validated);
        return redirect()->route('seller.products.index')->with('success', 'Produk ditambahkan');
    }

    public function edit(Request $request, $id)
    {
        $product = \App\Models\Product::where('id', $id)->where('seller_id', $request->user()->id)->firstOrFail();
        $categories = Category::all();
        return view('seller.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'category_id' => 'sometimes|exists:categories,id',
            'name' => 'sometimes|string',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric',
            'stock' => 'sometimes|integer',
            'image' => 'nullable|string'
        ]);

        $this->productService->updateProduct($id, $request->user()->id, $validated);
        return redirect()->route('seller.products.index')->with('success', 'Produk diupdate');
    }

    public function destroy(Request $request, $id)
    {
        $this->productService->deleteProduct($id, $request->user()->id);
        return back()->with('success', 'Produk dihapus');
    }
}
