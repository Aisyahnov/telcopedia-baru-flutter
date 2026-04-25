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
                'name' => 'Kemeja Flanel Premium Uniqlo',
                'category' => 'Fashion',
                'price' => 120000,
                'stock' => 5,
                'image' => 'https://placehold.co/500x500/9F1521/FFF?text=Kemeja+Flanel',
                'description' => 'Kemeja flanel bahan adem, kondisi masih 90% mulus. Cocok untuk kuliah atau santai.',
            ],
            [
                'name' => 'Celana Chino Slim Fit Erigo',
                'category' => 'Fashion',
                'price' => 180000,
                'stock' => 2,
                'image' => 'https://placehold.co/500x500/9F1521/FFF?text=Celana+Chino',
                'description' => 'Celana chino warna cream dengan bahan stretch yang nyaman dipakai seharian.',
            ],
            [
                'name' => 'Buku Pemrograman Web Dasar',
                'category' => 'Buku',
                'price' => 50000,
                'stock' => 1,
                'image' => 'https://placehold.co/500x500/9F1521/FFF?text=Buku+Pemrograman',
                'description' => 'Buku pegangan untuk belajar HTML, CSS, JS. Kondisi halaman lengkap tidak ada sobek.',
            ],
            [
                'name' => 'Kursi Kerja Ergonomis IKEA',
                'category' => 'Furniture',
                'price' => 850000,
                'stock' => 1,
                'image' => 'https://placehold.co/500x500/9F1521/FFF?text=Kursi+Ergonomis',
                'description' => 'Kursi nyaman banget buat nugas semalaman. Alasan jual karena udah mau lulus dan pindah kos.',
            ],
            [
                'name' => 'Laptop Lenovo Legion 5',
                'category' => 'Elektronik',
                'price' => 11000000,
                'stock' => 1,
                'image' => 'https://placehold.co/500x500/9F1521/FFF?text=Lenovo+Legion',
                'description' => 'Laptop gaming gahar. Pemakaian 1 tahun untuk render tugas akhir. Mulus parah.',
            ],
            [
                'name' => 'Mouse Logitech G304 Wireless',
                'category' => 'Elektronik',
                'price' => 350000,
                'stock' => 3,
                'image' => 'https://placehold.co/500x500/9F1521/FFF?text=Mouse+Logitech',
                'description' => 'Mouse gaming wireless enteng cocok buat main Valorant atau sekedar nugas. Lengkap dengan box.',
            ],
            [
                'name' => 'Helm KYT Kyoto Solid',
                'category' => 'Otomotif',
                'price' => 250000,
                'stock' => 1,
                'image' => 'https://placehold.co/500x500/9F1521/FFF?text=Helm+KYT',
                'description' => 'Helm KYT Kyoto warna hitam doff. Busa masih tebal dan nyaman.',
            ],
            [
                'name' => 'Rak Sepatu Susun Minimalis',
                'category' => 'Peralatan Kos',
                'price' => 45000,
                'stock' => 5,
                'image' => 'https://placehold.co/500x500/9F1521/FFF?text=Rak+Sepatu',
                'description' => 'Rak sepatu susun besi ringan. Bisa muat sampai 12 pasang sepatu/sandal.',
            ]
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
