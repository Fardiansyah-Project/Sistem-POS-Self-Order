<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // Espresso Based
            [
                'category_slug' => 'espresso-based',
                'name'          => 'Americano',
                'description'   => 'Espresso yang dilarutkan dengan air panas. Rasa kopi yang kuat dan bold.',
                'price'         => 20000,
                'sort_order'    => 1,
            ],
            [
                'category_slug' => 'espresso-based',
                'name'          => 'Cappuccino',
                'description'   => 'Espresso dengan steamed milk dan milk foam yang creamy.',
                'price'         => 30000,
                'sort_order'    => 2,
            ],
            [
                'category_slug' => 'espresso-based',
                'name'          => 'Caffe Latte',
                'description'   => 'Espresso dengan banyak steamed milk dan sedikit milk foam.',
                'price'         => 30000,
                'sort_order'    => 3,
            ],
            [
                'category_slug' => 'espresso-based',
                'name'          => 'Flat White',
                'description'   => 'Double espresso dengan microfoam susu yang lembut.',
                'price'         => 25000,
                'sort_order'    => 4,
            ],
            // Signature Drinks
            [
                'category_slug' => 'signature-drinks',
                'name'          => 'Koriro Signature Latte',
                'description'   => 'Espresso dengan gula aren cair dan susu oat. Menu andalan Koriro Coffee.',
                'price'         => 28000,
                'sort_order'    => 1,
            ],
            [
                'category_slug' => 'signature-drinks',
                'name'          => 'Brown Sugar Latte',
                'description'   => 'Espresso dengan brown sugar syrup dan susu segar yang manis.',
                'price'         => 27000,
                'sort_order'    => 2,
            ],
            [
                'category_slug' => 'signature-drinks',
                'name'          => 'Caramel Salt',
                'description'   => 'Menu Caramel Salt dari Koriro Coffee.',
                'price'         => 30000,
                'sort_order'    => 3,
            ],
            // Non Coffee
            [
                'category_slug' => 'non-coffee',
                'name'          => 'Matcha Latte',
                'description'   => 'Matcha premium grade Jepang dengan susu full cream yang creamy.',
                'price'         => 28000,
                'sort_order'    => 1,
            ],
            [
                'category_slug' => 'non-coffee',
                'name'          => 'Chocolate Milk',
                'description'   => 'Coklat premium dengan susu segar yang lembut dan manis.',
                'price'         => 22000,
                'sort_order'    => 2,
            ],
            // Snack & Food
            [
                'category_slug' => 'snack-food',
                'name'          => 'Roti Bakar Mentega',
                'description'   => 'Roti tawar tebal dipanggang dengan mentega dan susu kental manis.',
                'price'         => 15000,
                'sort_order'    => 1,
            ],
            [
                'category_slug' => 'snack-food',
                'name'          => 'Banana Foster Toast',
                'description'   => 'Roti bakar dengan topping pisang karamel dan whipped cream.',
                'price'         => 20000,
                'sort_order'    => 2,
            ],
        ];

        foreach ($products as $data) {
            $category = Category::where('slug', $data['category_slug'])->first();
            if (! $category) continue;

            $slug = Str::slug($data['name']);

            Product::updateOrCreate(
                ['slug' => $slug],
                [
                    'category_id' => $category->id,
                    'name'        => $data['name'],
                    'slug'        => $slug,
                    'description' => $data['description'],
                    'price'       => $data['price'],
                    'sort_order'  => $data['sort_order'],
                    'is_available' => true,
                ]
            );
        }
    }
}
