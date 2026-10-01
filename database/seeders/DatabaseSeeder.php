<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Urutan penting: categories → products → ingredients → recipes → users
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            IngredientSeeder::class,
            ProductSeeder::class,
            RecipeSeeder::class,
            UserSeeder::class,
            TransactionSeeder::class,
        ]);
    }
}
