<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Tambah Sellers
        $sellers = [];
        for ($i = 1; $i <= 5; $i++) {
            $sellers[] = User::create([
                'nim' => '666' . rand(100, 999),
                'name' => 'Juragan ' . ['Buku', 'Elektronik', 'Kosan', 'Gadget', 'Fashion'][$i - 1],
                'email' => 'juragan'.$i.'@telcopedia.com',
                'role' => 'seller',
                'password' => Hash::make('password'),
            ]);
        }

        // 2. Tambah Buyers
        $buyers = [];
        for ($i = 1; $i <= 5; $i++) {
            $buyers[] = User::create([
                'nim' => '777' . rand(100, 999),
                'name' => 'Mahasiswa ' . $i,
                'email' => 'mahasiswa'.$i.'@student.com',
                'role' => 'buyer',
                'password' => Hash::make('password'),
            ]);
        }

        // Ensure categories exist
        $categories = Category::all();
        if ($categories->isEmpty()) {
            $catNames = ['Fashion', 'Buku', 'Furniture', 'Elektronik', 'Alat Tulis', 'Hobi', 'Kesehatan', 'Otomotif'];
            foreach ($catNames as $name) {
                Category::create(['name' => $name, 'slug' => Str::slug($name), 'description' => 'Kategori ' . $name]);
            }
            $categories = Category::all();
        }

        // 3. Tambah Produk secara acak
        $productNames = [
            'Buku Kalkulus Edisi 9', 'Helm Bogo Retro', 'Jaket Varsity', 'Speaker Bluetooth JBL',
            'Meja Belajar Minimalis', 'Rak Buku Kayu', 'Hoodie Oversize', 'Lampu Tidur LED',
            'Kalkulator Casio Scientific', 'Sepatu Sneakers Pria', 'Tas Ransel Laptop', 'Rice Cooker Mito',
            'Kipas Angin Meja', 'Kemeja Polos Lengan Panjang', 'Gitar Akustik Yamaha', 'Mousepad Gaming Besar'
        ];

        foreach ($productNames as $index => $name) {
            Product::create([
                'seller_id' => $sellers[array_rand($sellers)]->id,
                'category_id' => $categories->random()->id,
                'name' => $name,
                'description' => 'Barang pre-loved kondisi ' . rand(80, 99) . '%. Sangat layak pakai dan original. Jual butuh atau lulus pindah kosan.',
                'price' => rand(5, 50) * 10000, // Antara 50rb sampai 500rb
                'stock' => rand(1, 10),
                'image' => null, // akan menggunakan placeholder text / ui-avatars default karena `getImageUrlAttribute` di model Product
            ]);
        }
    }
}
