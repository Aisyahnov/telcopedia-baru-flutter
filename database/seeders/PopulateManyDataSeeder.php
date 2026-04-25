<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductReturn;
use App\Models\Category;
use Illuminate\Support\Str;

class PopulateManyDataSeeder extends Seeder
{
    public function run(): void
    {
        $sellers = User::where('role', 'seller')->get();
        $buyer = User::where('role', 'buyer')->first() ?? User::create([
            'name' => 'Si Paluugada',
            'email' => 'palugada@demo.com',
            'role' => 'buyer',
            'password' => bcrypt('password'),
        ]);

        $categories = Category::all();
        if($categories->count() == 0) {
            $categories[] = Category::create(['name' => 'Lain-lain', 'slug' => 'lain-lain']);
        }

        $namaBarang = [
            'Keyboard Mekanikal', 'Tas Seminar', 'Novel Bekas Tere Liye', 
            'Kue Kering Nastar', 'Kemeja Flannel', 'Sepatu Futsal', 
            'Jam Tangan Pria', 'Lampu Meja LED', 'Sandal Capit', 'Kalkulator',
            'Mouse Wireless', 'Kacamata Hitam', 'Buku Kosong', 'Headphone', 'Action Figure'
        ];

        foreach ($sellers as $seller) {
            // Setiap seller dikasih 15 barang
            for ($i = 0; $i < 15; $i++) {
                $randName = $namaBarang[array_rand($namaBarang)] . ' - ' . Str::random(3);
                $product = Product::create([
                    'seller_id' => $seller->id,
                    'category_id' => $categories->random()->id,
                    'name' => $randName,
                    'description' => 'Ini produk acak jualan ' . $seller->name . '. Barang dijamin ori walau minus pemakaian wajar.',
                    'price' => rand(1, 40) * 10000, // Harga antara 10k - 400k
                    'stock' => rand(2, 20)
                ]);

                // Kasih 1 Order per barang (Supaya di "Pesanan Masuk" seller banyak data)
                $statusList = ['pending_payment', 'paid_verifying', 'processing', 'shipped', 'completed', 'cancelled'];
                $status = $statusList[array_rand($statusList)];
                
                $order = Order::create([
                    'user_id' => $buyer->id,
                    'total_amount' => $product->price,
                    'status' => $status,
                    'tracking_number' => ($status == 'shipped' || $status == 'completed') ? 'RESI-' . rand(11111,99999) : null
                ]);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => 1,
                    'price' => $product->price
                ]);

                // Kalau statusnya completed, 30% kemungkinan ada orang komplain / retur
                if ($status == 'completed' && rand(1, 100) <= 30) {
                    $returnStatusList = ['pending', 'approved', 'rejected'];
                    ProductReturn::create([
                        'user_id' => $buyer->id,
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'reason' => 'Barang saya terima hancur berkeping-keping. Tolong direfund.',
                        'status' => $returnStatusList[array_rand($returnStatusList)]
                    ]);
                }
            }
        }
        
        $this->command->info('Seed Masif Selesai! Kini setiap seller rata-rata punya 15 orderan dan produk di dashboard.');
    }
}
