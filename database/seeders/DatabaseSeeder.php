<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Voucher;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Add Admin
        $admin = User::create([
            'nim' => '000000',
            'name' => 'Admin Telcopedia',
            'email' => 'admin@telcopedia.com',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        // Add Sellers
        $seller1 = User::create([
            'nim' => '111111',
            'name' => 'Seller One',
            'email' => 'seller1@telcopedia.com',
            'role' => 'seller',
            'password' => Hash::make('password'),
        ]);

        $seller2 = User::create([
            'nim' => '222222',
            'name' => 'Seller Two',
            'email' => 'seller2@telcopedia.com',
            'role' => 'seller',
            'password' => Hash::make('password'),
        ]);

        // Add Buyers
        User::create([
            'nim' => '333333',
            'name' => 'Buyer One',
            'email' => 'buyer1@telcopedia.com',
            'role' => 'buyer',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'nim' => '444444',
            'name' => 'Buyer Two',
            'email' => 'buyer2@telcopedia.com',
            'role' => 'buyer',
            'password' => Hash::make('password'),
        ]);

        // Add Categories
        $categories = ['Fashion', 'Buku', 'Furniture', 'Elektronik', 'Alat Tulis', 'Hobi', 'Peralatan Kos', 'Kesehatan', 'Otomotif'];
        foreach ($categories as $cat) {
            Category::create([
                'name' => $cat,
                'slug' => strtolower(str_replace(' ', '-', $cat)),
                'description' => 'Kategori ' . $cat
            ]);
        }

        // Add Voucher
        Voucher::create([
            'code' => 'TELCOPROMO50',
            'discount_amount' => 50000,
            'valid_until' => Carbon::now()->addDays(30),
            'created_by' => $admin->id,
        ]);

        // Add Products defined by user
        $defaultProducts = [
            [
                'name' => 'Kemeja Flanel Premium',
                'category' => 'Fashion',
                'price' => 120000,
                'stock' => 50,
                'image' => 'baju.jpg',
                'description' => 'Kemeja flanel bahan adem, cocok untuk kuliah atau santai.',
            ],
            [
                'name' => 'Celana Chino Slim Fit',
                'category' => 'Fashion',
                'price' => 180000,
                'stock' => 40,
                'image' => 'celana.jpg',
                'description' => 'Celana chino warna cream dengan bahan stretch yang nyaman.',
            ],
            [
                'name' => 'Komik Elden Vol 1',
                'category' => 'Buku',
                'price' => 50000,
                'stock' => 100,
                'image' => 'komik.jpg',
                'description' => 'Petualangan epik di dunia Elden. Bahasa Indonesia.',
            ],
            [
                'name' => 'Kursi Kerja Ergonomis',
                'category' => 'Furniture',
                'price' => 1200000,
                'stock' => 15,
                'image' => 'kursi.jpg',
                'description' => 'Kursi dengan sandaran punggung yang nyaman untuk kerja lama.',
            ],
            [
                'name' => 'Lenovo LOQ Gaming',
                'category' => 'Elektronik',
                'price' => 9000000,
                'stock' => 10,
                'image' => 'lenovo_loq.jpg',
                'description' => 'Laptop gaming gahar dengan RTX 4050 dan i5 Gen 13.',
            ],
            [
                'name' => 'Mouse Logitech RGB',
                'category' => 'Elektronik',
                'price' => 250000,
                'stock' => 30,
                'image' => 'mouse.jpg',
                'description' => 'Mouse gaming wireless dengan lampu RGB yang bisa diatur.',
            ],
        ];

        foreach ($defaultProducts as $index => $p) {
            $cat = Category::where('name', $p['category'])->first();
            Product::create([
                'seller_id' => $index % 2 == 0 ? $seller1->id : $seller2->id,
                'category_id' => $cat ? $cat->id : 1, // Fallback to 1 if not found
                'name' => $p['name'],
                'description' => $p['description'],
                'price' => $p['price'],
                'stock' => $p['stock'],
                'image' => $p['image'],
            ]);
        }
    }
}
