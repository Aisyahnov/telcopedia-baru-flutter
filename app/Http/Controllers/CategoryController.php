<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of all categories.
     */
    public function index()
    {
        // Ambil semua kategori
        $categories = Category::withCount(['products' => function($q) {
            $q->where('status', 'approved');
        }])->get()->map(function ($category) {
            $category->icon = $this->getIconForCategory($category->name);
            return $category;
        });

        // Ambil produk untuk kategori pertama sebagai default
        $firstCategory = $categories->first();
        $products = collect();
        if ($firstCategory) {
            $products = Product::where('category_id', $firstCategory->id)
                ->where('status', 'approved')
                ->with(['seller', 'category'])
                ->latest()
                ->take(12)
                ->get();
        }

        return view('category.index', compact('categories', 'products', 'firstCategory'));
    }

    /**
     * AJAX endpoint to get products for a category.
     */
    public function getProductsAjax($id)
    {
        $products = Product::where('category_id', $id)
            ->where('status', 'approved')
            ->with(['seller', 'category'])
            ->latest()
            ->take(13) // Ambil 13 untuk cek apakah ada "more"
            ->get();
            
        return response()->json([
            'products' => $products->take(12),
            'has_more' => $products->count() > 12,
            'category_slug' => Category::find($id)->slug ?? ''
        ]);
    }

    /**
     * Display the specified category and its products.
     */
    public function show($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        
        $products = Product::where('category_id', $category->id)
            ->where('status', 'approved')
            ->with(['seller', 'category'])
            ->latest()
            ->paginate(12);

        $category->icon = $this->getIconForCategory($category->name);

        return view('category.show', compact('category', 'products'));
    }

    /**
     * Helper to map category names to FontAwesome icons.
     */
    private function getIconForCategory($name)
    {
        $icons = [
            'Elektronik'  => 'fa-laptop-code',
            'Fashion'     => 'fa-shirt',
            'Buku'        => 'fa-book-open',
            'Alat Tulis'  => 'fa-pen-clip',
            'Hobi'        => 'fa-gamepad',
            'Jasa'        => 'fa-handshake-angle',
            'Sembako'     => 'fa-basket-shopping',
            'Peralatan Kos' => 'fa-house-user',
            'Kesehatan'   => 'fa-briefcase-medical',
            'Otomotif'    => 'fa-motorcycle',
            'Furniture'   => 'fa-couch',
            'Makanan'     => 'fa-bowl-food',
        ];

        // Cari yang mirip (case-insensitive)
        foreach ($icons as $key => $icon) {
            if (stripos($name, $key) !== false) {
                return $icon;
            }
        }

        return 'fa-tags'; // Default icon
    }
}
