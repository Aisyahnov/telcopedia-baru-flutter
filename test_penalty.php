<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$service = new \App\Services\ProductService();
$products = $service->searchProducts();
foreach ($products as $p) {
    echo $p->name . ' - Seller: ' . $p->seller->name . PHP_EOL;
}
