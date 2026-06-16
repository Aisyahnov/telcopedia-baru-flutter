<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\OrderItem;
use App\Models\PenarikanDana;
use App\Models\ProductReturn;

$sellers = User::where('role', 'seller')->get();
foreach($sellers as $seller) {
    $income = 0;
    
    // Sum up completed order items
    $items = OrderItem::whereHas('order', function($q) {
        $q->where('status', 'completed');
    })->whereHas('product', function($q) use ($seller) {
        $q->where('seller_id', $seller->id);
    })->get();

    foreach($items as $item) {
        $income += ($item->price * $item->quantity) * 0.95; // Deduct 5% admin fee
    }

    // Subtract approved withdrawals
    $withdrawals = PenarikanDana::where('user_id', $seller->id)->where('status', 'approved')->sum('amount');
    
    $finalSaldo = $income - $withdrawals;
    $seller->balance = max(0, $finalSaldo);
    $seller->save();
    
    echo "Seller {$seller->name} updated to {$seller->balance}\n";
}
echo "Done!\n";
