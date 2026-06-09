<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$service = new \App\Services\ProductService();
$products = $service->searchProducts(null, null);

echo "Search results count: " . $products->count() . "\n";

$banned = \App\Models\User::where('role', 'seller')->get()->filter(function($u) { return $u->is_banned_from_posting; })->pluck('id');
echo "Banned seller IDs: " . json_encode($banned) . "\n";

$allProducts = \App\Models\Product::count();
$approvedProducts = \App\Models\Product::where('status', 'approved')->count();
echo "All products: $allProducts, Approved: $approvedProducts\n";

$approvedVisible = \App\Models\Product::where('status', 'approved')->whereNotIn('seller_id', $banned)->count();
echo "Approved & Not Banned: $approvedVisible\n";

$recommended = $service->getRecommendedProducts(null);
echo "Recommended results count: " . $recommended->count() . "\n";
