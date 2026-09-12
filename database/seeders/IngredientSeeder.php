<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ingredient;

class IngredientSeeder extends Seeder
{
    public function run(): void
    {
        $ingredients = [
            // Kopi & Espresso
            ['name' => 'Espresso Shot',       'unit' => 'ml',     'stock_quantity' => 5000,  'minimum_stock' => 500,  'cost_per_unit' => 1.5],
            ['name' => 'Biji Kopi Arabika',   'unit' => 'gram',   'stock_quantity' => 10000, 'minimum_stock' => 1000, 'cost_per_unit' => 0.12],
            // Dairy
            ['name' => 'Susu Full Cream',     'unit' => 'ml',     'stock_quantity' => 20000, 'minimum_stock' => 2000, 'cost_per_unit' => 0.015],
            ['name' => 'Susu Oat',            'unit' => 'ml',     'stock_quantity' => 5000,  'minimum_stock' => 500,  'cost_per_unit' => 0.03],
            ['name' => 'Heavy Cream',         'unit' => 'ml',     'stock_quantity' => 3000,  'minimum_stock' => 300,  'cost_per_unit' => 0.025],
            // Sweeteners & Syrups
            ['name' => 'Gula Pasir',          'unit' => 'gram',   'stock_quantity' => 10000, 'minimum_stock' => 500,  'cost_per_unit' => 0.008],
            ['name' => 'Simple Syrup',        'unit' => 'ml',     'stock_quantity' => 3000,  'minimum_stock' => 300,  'cost_per_unit' => 0.02],
            ['name' => 'Gula Aren Cair',      'unit' => 'ml',     'stock_quantity' => 2000,  'minimum_stock' => 200,  'cost_per_unit' => 0.035],
            ['name' => 'Matcha Powder',       'unit' => 'gram',   'stock_quantity' => 2000,  'minimum_stock' => 200,  'cost_per_unit' => 0.25],
            ['name' => 'Coklat Bubuk',        'unit' => 'gram',   'stock_quantity' => 3000,  'minimum_stock' => 300,  'cost_per_unit' => 0.08],
            // Extras
            ['name' => 'Es Batu',             'unit' => 'gram',   'stock_quantity' => 50000, 'minimum_stock' => 5000, 'cost_per_unit' => 0.001],
            ['name' => 'Air Panas',           'unit' => 'ml',     'stock_quantity' => 99999, 'minimum_stock' => 0,    'cost_per_unit' => 0.001],
            ['name' => 'Whipped Cream',       'unit' => 'gram',   'stock_quantity' => 2000,  'minimum_stock' => 200,  'cost_per_unit' => 0.05],
            // Food
            ['name' => 'Roti Tawar',          'unit' => 'lembar', 'stock_quantity' => 100,   'minimum_stock' => 10,   'cost_per_unit' => 500],
            ['name' => 'Mentega',             'unit' => 'gram',   'stock_quantity' => 2000,  'minimum_stock' => 200,  'cost_per_unit' => 0.05],
        ];

        foreach ($ingredients as $ingredient) {
            Ingredient::updateOrCreate(['name' => $ingredient['name']], $ingredient);
        }
    }
}
