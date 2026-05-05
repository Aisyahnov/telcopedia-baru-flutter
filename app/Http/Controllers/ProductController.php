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
        if (auth()->user()->is_banned_from_posting) {
            return redirect()->route('seller.products.index')->withErrors(['error' => 'Akses ditolak. Anda telah menerima 3 teguran retur.']);
        }
        $categories = Category::whereNull('parent_id')->with('subcategories')->get();
        return view('seller.products.create', ['categories' => $categories]);
    }

    public function store(Request $request)
    {
        if ($request->user()->is_banned_from_posting) {
            return redirect()->route('seller.products.index')->withErrors(['error' => 'Akses ditolak. Anda telah menerima 3 teguran retur.']);
        }

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'condition' => 'required|string',
            'image' => 'required|image|max:10240',
            'gallery.*' => 'nullable|image|max:10240'
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $productData = collect($validated)->except(['gallery'])->toArray();
        $product = $this->productService->createProduct($request->user()->id, $productData);

        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                $path = $file->store('products/gallery', 'public');
                \App\Models\ProductImage::create([
                    'product_id' => $product->id,
                    'image_url' => $path
                ]);
            }
        }

        // Notify Admins
        $admins = \App\Models\User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\SystemNotification(
                'Produk Baru Perlu Review',
                "Seller {$request->user()->name} mengunggah produk: {$product->name}",
                'product',
                route('admin.products')
            ));
        }

        return redirect()->route('seller.products.index')->with('success', 'Produk berhasil ditambahkan dan sedang menunggu persetujuan admin.');
    }

    public function edit(Request $request, $id)
    {
        $product = \App\Models\Product::with(['images', 'category.parent'])->where('id', $id)->where('seller_id', $request->user()->id)->firstOrFail();
        $categories = Category::whereNull('parent_id')->with('subcategories')->get();
        return view('seller.products.edit', ['product' => $product, 'categories' => $categories]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'category_id' => 'sometimes|exists:categories,id',
            'name' => 'sometimes|string',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric',
            'stock' => 'sometimes|integer',
            'condition' => 'sometimes|string',
            'image' => 'nullable|image|max:10240',
            'gallery.*' => 'nullable|image|max:10240'
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        } else {
            unset($validated['image']);
        }

        $productData = collect($validated)->except(['gallery'])->toArray();
        $product = $this->productService->updateProduct($id, $request->user()->id, $productData);

        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                $path = $file->store('products/gallery', 'public');
                \App\Models\ProductImage::create([
                    'product_id' => $product->id,
                    'image_url' => $path
                ]);
            }
        }

        return redirect()->route('seller.products.index')->with('success', 'Produk diupdate');
    }

    public function destroy(Request $request, $id)
    {
        $this->productService->deleteProduct($id, $request->user()->id);
        return back()->with('success', 'Produk dihapus');
    }
}
