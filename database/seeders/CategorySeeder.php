<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Espresso Based',   'slug' => 'espresso-based',   'icon' => '☕', 'sort_order' => 1],
            ['name' => 'Manual Brew',      'slug' => 'manual-brew',      'icon' => '🫖', 'sort_order' => 2],
            ['name' => 'Non Coffee',       'slug' => 'non-coffee',       'icon' => '🧋', 'sort_order' => 3],
            ['name' => 'Signature Drinks', 'slug' => 'signature-drinks', 'icon' => '✨', 'sort_order' => 4],
            ['name' => 'Snack & Food',     'slug' => 'snack-food',       'icon' => '🍞', 'sort_order' => 5],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['slug' => $cat['slug']], $cat);
        }
    }
}
