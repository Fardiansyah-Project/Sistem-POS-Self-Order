<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Ingredient;
use App\Models\Recipe;

class RecipeSeeder extends Seeder
{
    public function run(): void
    {
        // Helper untuk ambil ID
        $p = fn($name)  => Product::where('name', $name)->first()?->id;
        $i = fn($name)  => Ingredient::where('name', $name)->first()?->id;

        // Resep per produk (quantity_needed per 1 sajian)
        $recipes = [
            // Americano: 60ml espresso + 150ml air panas
            ['product' => 'Americano', 'ingredient' => 'Espresso Shot',   'qty' => 60],
            ['product' => 'Americano', 'ingredient' => 'Air Panas',       'qty' => 150],
            ['product' => 'Americano', 'ingredient' => 'Es Batu',         'qty' => 100],

            // Cappuccino: 30ml espresso + 120ml susu + 10g gula
            ['product' => 'Cappuccino', 'ingredient' => 'Espresso Shot', 'qty' => 30],
            ['product' => 'Cappuccino', 'ingredient' => 'Susu Full Cream','qty' => 120],
            ['product' => 'Cappuccino', 'ingredient' => 'Gula Pasir',    'qty' => 10],

            // Caffe Latte: 30ml espresso + 200ml susu
            ['product' => 'Caffe Latte', 'ingredient' => 'Espresso Shot',   'qty' => 30],
            ['product' => 'Caffe Latte', 'ingredient' => 'Susu Full Cream', 'qty' => 200],
            ['product' => 'Caffe Latte', 'ingredient' => 'Simple Syrup',    'qty' => 15],

            // Flat White: 60ml espresso + 150ml susu
            ['product' => 'Flat White', 'ingredient' => 'Espresso Shot',   'qty' => 60],
            ['product' => 'Flat White', 'ingredient' => 'Susu Full Cream', 'qty' => 150],

            // Koriro Signature Latte: 30ml espresso + 200ml susu oat + 30ml gula aren
            ['product' => 'Koriro Signature Latte', 'ingredient' => 'Espresso Shot', 'qty' => 30],
            ['product' => 'Koriro Signature Latte', 'ingredient' => 'Susu Oat',      'qty' => 200],
            ['product' => 'Koriro Signature Latte', 'ingredient' => 'Gula Aren Cair','qty' => 30],
            ['product' => 'Koriro Signature Latte', 'ingredient' => 'Es Batu',       'qty' => 150],

            // Brown Sugar Latte: 30ml espresso + 180ml susu + 25ml gula aren
            ['product' => 'Brown Sugar Latte', 'ingredient' => 'Espresso Shot',  'qty' => 30],
            ['product' => 'Brown Sugar Latte', 'ingredient' => 'Susu Full Cream','qty' => 180],
            ['product' => 'Brown Sugar Latte', 'ingredient' => 'Gula Aren Cair', 'qty' => 25],
            ['product' => 'Brown Sugar Latte', 'ingredient' => 'Es Batu',        'qty' => 150],

            // Matcha Latte: 8g matcha + 200ml susu + 10g gula
            ['product' => 'Matcha Latte', 'ingredient' => 'Matcha Powder',    'qty' => 8],
            ['product' => 'Matcha Latte', 'ingredient' => 'Susu Full Cream',  'qty' => 200],
            ['product' => 'Matcha Latte', 'ingredient' => 'Simple Syrup',     'qty' => 15],
            ['product' => 'Matcha Latte', 'ingredient' => 'Es Batu',          'qty' => 150],

            // Chocolate Milk: 20g coklat + 200ml susu + 15g gula
            ['product' => 'Chocolate Milk', 'ingredient' => 'Coklat Bubuk',     'qty' => 20],
            ['product' => 'Chocolate Milk', 'ingredient' => 'Susu Full Cream',  'qty' => 200],
            ['product' => 'Chocolate Milk', 'ingredient' => 'Gula Pasir',       'qty' => 15],
            ['product' => 'Chocolate Milk', 'ingredient' => 'Es Batu',          'qty' => 100],

            // Roti Bakar Mentega: 2 lembar roti + 20g mentega
            ['product' => 'Roti Bakar Mentega', 'ingredient' => 'Roti Tawar', 'qty' => 2],
            ['product' => 'Roti Bakar Mentega', 'ingredient' => 'Mentega',    'qty' => 20],

            // Banana Foster Toast: 2 lembar roti + 15g mentega + 20g whipped cream
            ['product' => 'Banana Foster Toast', 'ingredient' => 'Roti Tawar',    'qty' => 2],
            ['product' => 'Banana Foster Toast', 'ingredient' => 'Mentega',       'qty' => 15],
            ['product' => 'Banana Foster Toast', 'ingredient' => 'Whipped Cream', 'qty' => 20],
            ['product' => 'Banana Foster Toast', 'ingredient' => 'Gula Aren Cair','qty' => 15],
        ];

        foreach ($recipes as $r) {
            $productId    = $p($r['product']);
            $ingredientId = $i($r['ingredient']);
            if (! $productId || ! $ingredientId) continue;

            Recipe::updateOrCreate(
                ['product_id' => $productId, 'ingredient_id' => $ingredientId],
                ['quantity_needed' => $r['qty']]
            );
        }
    }
}
