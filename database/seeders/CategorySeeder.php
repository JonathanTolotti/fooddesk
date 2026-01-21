<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Lanches',
                'description' => 'Hambúrgueres, hot dogs e sanduíches',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Bebidas',
                'description' => 'Refrigerantes, sucos e água',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Porções',
                'description' => 'Batata frita, onion rings e petiscos',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Sobremesas',
                'description' => 'Doces e sorvetes',
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}