<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductReturn;
use App\Models\Category;

class PopulateAllSellersSeeder extends Seeder
{
    public function run(): void
    {
        $sellers = User::where('role', 'seller')->get();
        // Buyer fallback
        $buyer = \App\Models\User::where('role', 'buyer')->first();
        if (!$buyer) {
            $buyer = \App\Models\User::create([
                'name' => 'Demo Buyer',
                'email' => 'demobuyer@test.com',
                'role' => 'buyer',
                'password' => bcrypt('password'),
            ]);
        }
        
        $cat = Category::first() ?? Category::create(['name' => 'General', 'slug' => 'general']);

        foreach ($sellers as $index => $seller) {
            // Create a specific product for this seller
            $product = Product::create([
                'seller_id' => $seller->id,
                'category_id' => $cat->id,
                'name' => 'Barang Spesial Lapak ' . $seller->name,
                'description' => 'Ini produk otomatis agar dashboard tidak kosong.',
                'price' => rand(1, 10) * 50000,
                'stock' => 10,
            ]);

            // Create pending order
            $order1 = Order::create([
                'user_id' => $buyer->id,
                'total_amount' => $product->price,
                'status' => 'pending_payment'
            ]);
            OrderItem::create([
                'order_id' => $order1->id,
                'product_id' => $product->id,
                'quantity' => 1,
                'price' => $product->price
            ]);

            // Create completed order with a return
            $order2 = Order::create([
                'user_id' => $buyer->id,
                'total_amount' => $product->price,
                'status' => 'completed'
            ]);
            OrderItem::create([
                'order_id' => $order2->id,
                'product_id' => $product->id,
                'quantity' => 1,
                'price' => $product->price
            ]);
            ProductReturn::create([
                'user_id' => $buyer->id,
                'order_id' => $order2->id,
                'product_id' => $product->id,
                'reason' => 'Barang cacat pabrik, mohon diretur secepetnya!',
                'status' => 'pending'
            ]);
        }
        $this->command->info('Semua seller berhasil diberi data dummy.');
    }
}
