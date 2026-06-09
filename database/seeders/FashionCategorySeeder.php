<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class FashionCategorySeeder extends Seeder
{
    public function run()
    {
        $parent = Category::where('name', 'Fashion')->first();
        if ($parent) {
            $subs = ['Celana Pria', 'Celana Wanita', 'Celana Unisex'];
            foreach ($subs as $sub) {
                Category::firstOrCreate(
                    ['name' => $sub, 'parent_id' => $parent->id],
                    [
                        'slug' => strtolower(str_replace(' ', '-', 'Fashion' . '-' . $sub)),
                        'description' => 'Sub-kategori ' . $sub . ' dari Fashion'
                    ]
                );
            }
        }
    }
}
