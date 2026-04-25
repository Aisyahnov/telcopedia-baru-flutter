<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductReturn;
use App\Models\Review;

class DummyTransactionsSeeder extends Seeder
{
    public function run(): void
    {
        $buyers = User::where('role', 'buyer')->get();
        $products = Product::all();

        if ($buyers->isEmpty() || $products->isEmpty()) {
            $this->command->info('Tidak ada buyer atau produk. Gagal membuat transaksi dummy.');
            return;
        }

        // Bikin 5 Pesanan Dummy
        foreach ($buyers->take(3) as $buyer) {
            
            // Order 1: Selesai, Tidak Komplain (Ada Review)
            $product1 = $products->random();
            $order1 = Order::create([
                'user_id' => $buyer->id,
                'total_amount' => $product1->price + 5000,
                'status' => 'completed',
                'tracking_number' => 'RESI-' . rand(10000,99999)
            ]);
            
            OrderItem::create([
                'order_id' => $order1->id,
                'product_id' => $product1->id,
                'quantity' => 1,
                'price' => $product1->price
            ]);

            Review::create([
                'user_id' => $buyer->id,
                'product_id' => $product1->id,
                'order_id' => $order1->id,
                'rating' => 5,
                'comment' => 'Barang mantap, mulus kayak baru!'
            ]);

            // Order 2: Selesai, Ada Komplain (Return Pending)
            $product2 = $products->random();
            $order2 = Order::create([
                'user_id' => $buyer->id,
                'total_amount' => $product2->price + 5000,
                'status' => 'completed',
                'tracking_number' => 'RESI-' . rand(10000,99999)
            ]);
            
            OrderItem::create([
                'order_id' => $order2->id,
                'product_id' => $product2->id,
                'quantity' => 1,
                'price' => $product2->price
            ]);

            ProductReturn::create([
                'user_id' => $buyer->id,
                'order_id' => $order2->id,
                'product_id' => $product2->id,
                'reason' => 'Barang ternyata ada sobek di bagian belakang, tidak sesuai deskripsi.',
                'status' => 'pending'
            ]);

            // Order 3: Shipped (Bisa diajukan retur oleh buyer)
            $product3 = $products->random();
            $order3 = Order::create([
                'user_id' => $buyer->id,
                'total_amount' => $product3->price + 5000,
                'status' => 'shipped',
                'tracking_number' => 'RESI-' . rand(10000,99999)
            ]);
            
            OrderItem::create([
                'order_id' => $order3->id,
                'product_id' => $product3->id,
                'quantity' => 1,
                'price' => $product3->price
            ]);
            
            // Tambahan Return yang di-approve/reject sebagai contoh
            $product4 = $products->random();
            $order4 = Order::create([
                'user_id' => $buyer->id,
                'total_amount' => $product4->price + 5000,
                'status' => 'completed',
            ]);
            OrderItem::create(['order_id' => $order4->id, 'product_id' => $product4->id, 'quantity' => 1, 'price' => $product4->price]);
            ProductReturn::create([
                'user_id' => $buyer->id,
                'order_id' => $order4->id,
                'product_id' => $product4->id,
                'reason' => 'Ukurannya kekecilan mas',
                'status' => rand(0,1) ? 'approved' : 'rejected'
            ]);
            
        }
        
        $this->command->info('Transaksi Dummy Berhasil!');
    }
}
