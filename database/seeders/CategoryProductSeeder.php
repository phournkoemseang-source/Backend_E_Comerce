<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategoryProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Electronics'],
            ['name' => 'Clothing'],
            ['name' => 'Books'],
            ['name' => 'Home & Garden'],
            ['name' => 'Sports & Outdoors'],
        ];

        foreach ($categories as $cat) {
            $category = Category::create([
                'name' => $cat['name'],
                'slug' => Str::slug($cat['name']),
            ]);

            // Create 5 products for each category
            for ($i = 1; $i <= 5; $i++) {
                Product::create([
                    'category_id' => $category->id,
                    'name' => "{$category->name} Product {$i}",
                    'description' => "This is a high-quality {$category->name} product. It features excellent durability and performance.",
                    'price' => rand(9, 99) + (rand(0, 99) / 100),
                    'stock' => rand(10, 100),
                ]);
            }
        }
    }
}
