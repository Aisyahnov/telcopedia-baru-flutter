<?php
try {
    echo "Counting products...\n";
    $count = \App\Models\Product::count();
    echo "Products count: " . $count . "\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
