<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    private array $ingredients;

    public function run(): void
    {
        // Get categories
        $lanches = Category::where('name', 'Lanches')->first();
        $bebidas = Category::where('name', 'Bebidas')->first();
        $porcoes = Category::where('name', 'Porções')->first();

        // Get ingredients by name (for easy reference)
        $this->ingredients = Ingredient::all()->keyBy('name')->toArray();

        // === LANCHES ===

        // X-Burger
        $this->createProductWithIngredients([
            'name' => 'X-Burger',
            'description' => 'Hambúrguer com queijo e salada',
            'price' => 18.90,
            'category_id' => $lanches->id,
            'is_active' => true,
            'sort_order' => 1,
        ], [
            ['name' => 'Pão de hambúrguer', 'type' => 'base'],
            ['name' => 'Hambúrguer bovino 120g', 'type' => 'base'],
            ['name' => 'Queijo mussarela', 'type' => 'standard'],
            ['name' => 'Alface', 'type' => 'standard'],
            ['name' => 'Tomate', 'type' => 'standard'],
            ['name' => 'Maionese', 'type' => 'standard'],
            ['name' => 'Bacon', 'type' => 'additional', 'price' => 4.00],
            ['name' => 'Ovo', 'type' => 'additional', 'price' => 3.00],
            ['name' => 'Cheddar cremoso', 'type' => 'additional', 'price' => 3.50],
        ]);

        // X-Bacon
        $this->createProductWithIngredients([
            'name' => 'X-Bacon',
            'description' => 'Hambúrguer com bacon crocante e queijo',
            'price' => 24.90,
            'category_id' => $lanches->id,
            'is_active' => true,
            'sort_order' => 2,
        ], [
            ['name' => 'Pão de hambúrguer', 'type' => 'base'],
            ['name' => 'Hambúrguer bovino 120g', 'type' => 'base'],
            ['name' => 'Bacon', 'type' => 'base'],
            ['name' => 'Queijo cheddar', 'type' => 'standard'],
            ['name' => 'Alface', 'type' => 'standard'],
            ['name' => 'Tomate', 'type' => 'standard'],
            ['name' => 'Maionese', 'type' => 'standard'],
            ['name' => 'Cebola caramelizada', 'type' => 'additional', 'price' => 3.00],
            ['name' => 'Ovo', 'type' => 'additional', 'price' => 3.00],
        ]);

        // X-Tudo
        $this->createProductWithIngredients([
            'name' => 'X-Tudo',
            'description' => 'O mais completo! Hambúrguer, bacon, ovo, presunto e queijo',
            'price' => 32.90,
            'category_id' => $lanches->id,
            'is_active' => true,
            'sort_order' => 3,
        ], [
            ['name' => 'Pão de hambúrguer', 'type' => 'base'],
            ['name' => 'Hambúrguer bovino 180g', 'type' => 'base'],
            ['name' => 'Bacon', 'type' => 'base'],
            ['name' => 'Ovo', 'type' => 'base'],
            ['name' => 'Queijo cheddar', 'type' => 'standard'],
            ['name' => 'Queijo mussarela', 'type' => 'standard'],
            ['name' => 'Alface', 'type' => 'standard'],
            ['name' => 'Tomate', 'type' => 'standard'],
            ['name' => 'Cebola', 'type' => 'standard'],
            ['name' => 'Milho', 'type' => 'standard'],
            ['name' => 'Batata palha', 'type' => 'standard'],
            ['name' => 'Maionese', 'type' => 'standard'],
            ['name' => 'Ketchup', 'type' => 'standard'],
            ['name' => 'Catupiry', 'type' => 'additional', 'price' => 4.00],
        ]);

        // X-Salada
        $this->createProductWithIngredients([
            'name' => 'X-Salada',
            'description' => 'Hambúrguer com salada fresca',
            'price' => 16.90,
            'category_id' => $lanches->id,
            'is_active' => true,
            'sort_order' => 4,
        ], [
            ['name' => 'Pão de hambúrguer', 'type' => 'base'],
            ['name' => 'Hambúrguer bovino 120g', 'type' => 'base'],
            ['name' => 'Alface', 'type' => 'standard'],
            ['name' => 'Tomate', 'type' => 'standard'],
            ['name' => 'Cebola', 'type' => 'standard'],
            ['name' => 'Maionese', 'type' => 'standard'],
            ['name' => 'Queijo mussarela', 'type' => 'additional', 'price' => 2.50],
            ['name' => 'Bacon', 'type' => 'additional', 'price' => 4.00],
        ]);

        // Hot Dog Tradicional
        $this->createProductWithIngredients([
            'name' => 'Hot Dog Tradicional',
            'description' => 'Cachorro-quente com molho e batata palha',
            'price' => 14.90,
            'category_id' => $lanches->id,
            'is_active' => true,
            'sort_order' => 5,
        ], [
            ['name' => 'Pão de hot dog', 'type' => 'base'],
            ['name' => 'Salsicha', 'type' => 'base'],
            ['name' => 'Milho', 'type' => 'standard'],
            ['name' => 'Batata palha', 'type' => 'standard'],
            ['name' => 'Ketchup', 'type' => 'standard'],
            ['name' => 'Mostarda', 'type' => 'standard'],
            ['name' => 'Maionese', 'type' => 'standard'],
            ['name' => 'Queijo cheddar', 'type' => 'additional', 'price' => 2.50],
            ['name' => 'Bacon', 'type' => 'additional', 'price' => 4.00],
            ['name' => 'Catupiry', 'type' => 'additional', 'price' => 3.50],
        ]);

        // Hot Dog Especial
        $this->createProductWithIngredients([
            'name' => 'Hot Dog Especial',
            'description' => 'Cachorro-quente completo com cheddar e bacon',
            'price' => 22.90,
            'category_id' => $lanches->id,
            'is_active' => true,
            'sort_order' => 6,
        ], [
            ['name' => 'Pão de hot dog', 'type' => 'base'],
            ['name' => 'Salsicha', 'type' => 'base'],
            ['name' => 'Bacon', 'type' => 'base'],
            ['name' => 'Cheddar cremoso', 'type' => 'standard'],
            ['name' => 'Milho', 'type' => 'standard'],
            ['name' => 'Batata palha', 'type' => 'standard'],
            ['name' => 'Cebola caramelizada', 'type' => 'standard'],
            ['name' => 'Ketchup', 'type' => 'standard'],
            ['name' => 'Maionese', 'type' => 'standard'],
            ['name' => 'Jalapeño', 'type' => 'additional', 'price' => 2.00],
        ]);

        // Misto Quente
        $this->createProductWithIngredients([
            'name' => 'Misto Quente',
            'description' => 'Pão francês com presunto e queijo na chapa',
            'price' => 9.90,
            'category_id' => $lanches->id,
            'is_active' => true,
            'sort_order' => 7,
        ], [
            ['name' => 'Pão francês', 'type' => 'base'],
            ['name' => 'Queijo mussarela', 'type' => 'base'],
            ['name' => 'Ovo', 'type' => 'additional', 'price' => 3.00],
            ['name' => 'Bacon', 'type' => 'additional', 'price' => 4.00],
        ]);

        // Hambúrguer Artesanal
        $this->createProductWithIngredients([
            'name' => 'Hambúrguer Artesanal',
            'description' => 'Pão australiano, blend especial, queijo cheddar e molho da casa',
            'price' => 34.90,
            'category_id' => $lanches->id,
            'is_active' => true,
            'sort_order' => 8,
        ], [
            ['name' => 'Pão australiano', 'type' => 'base'],
            ['name' => 'Hambúrguer bovino 180g', 'type' => 'base'],
            ['name' => 'Queijo cheddar', 'type' => 'base'],
            ['name' => 'Molho especial', 'type' => 'base'],
            ['name' => 'Alface', 'type' => 'standard'],
            ['name' => 'Tomate', 'type' => 'standard'],
            ['name' => 'Cebola caramelizada', 'type' => 'standard'],
            ['name' => 'Picles', 'type' => 'standard'],
            ['name' => 'Bacon', 'type' => 'additional', 'price' => 4.00],
            ['name' => 'Ovo', 'type' => 'additional', 'price' => 3.00],
            ['name' => 'Rúcula', 'type' => 'additional', 'price' => 2.00],
        ]);

        // === BEBIDAS ===

        Product::create([
            'name' => 'Coca-Cola Lata 350ml',
            'description' => 'Refrigerante Coca-Cola lata',
            'price' => 6.00,
            'category_id' => $bebidas->id,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Product::create([
            'name' => 'Coca-Cola 600ml',
            'description' => 'Refrigerante Coca-Cola garrafa',
            'price' => 9.00,
            'category_id' => $bebidas->id,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        Product::create([
            'name' => 'Guaraná Antarctica Lata 350ml',
            'description' => 'Refrigerante Guaraná Antarctica lata',
            'price' => 5.50,
            'category_id' => $bebidas->id,
            'is_active' => true,
            'sort_order' => 3,
        ]);

        Product::create([
            'name' => 'Sprite Lata 350ml',
            'description' => 'Refrigerante Sprite lata',
            'price' => 5.50,
            'category_id' => $bebidas->id,
            'is_active' => true,
            'sort_order' => 4,
        ]);

        Product::create([
            'name' => 'Fanta Laranja Lata 350ml',
            'description' => 'Refrigerante Fanta Laranja lata',
            'price' => 5.50,
            'category_id' => $bebidas->id,
            'is_active' => true,
            'sort_order' => 5,
        ]);

        Product::create([
            'name' => 'Suco Natural de Laranja 300ml',
            'description' => 'Suco de laranja natural',
            'price' => 8.00,
            'category_id' => $bebidas->id,
            'is_active' => true,
            'sort_order' => 6,
        ]);

        Product::create([
            'name' => 'Suco Natural de Limão 300ml',
            'description' => 'Suco de limão natural',
            'price' => 7.00,
            'category_id' => $bebidas->id,
            'is_active' => true,
            'sort_order' => 7,
        ]);

        Product::create([
            'name' => 'Água Mineral 500ml',
            'description' => 'Água mineral sem gás',
            'price' => 4.00,
            'category_id' => $bebidas->id,
            'is_active' => true,
            'sort_order' => 8,
        ]);

        Product::create([
            'name' => 'Água com Gás 500ml',
            'description' => 'Água mineral com gás',
            'price' => 4.50,
            'category_id' => $bebidas->id,
            'is_active' => true,
            'sort_order' => 9,
        ]);

        Product::create([
            'name' => 'Cerveja Heineken Long Neck',
            'description' => 'Cerveja Heineken 330ml',
            'price' => 12.00,
            'category_id' => $bebidas->id,
            'is_active' => true,
            'sort_order' => 10,
        ]);

        // === PORÇÕES ===

        Product::create([
            'name' => 'Batata Frita',
            'description' => 'Porção de batata frita crocante',
            'price' => 18.00,
            'category_id' => $porcoes->id,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Product::create([
            'name' => 'Batata Frita com Cheddar e Bacon',
            'description' => 'Batata frita coberta com cheddar cremoso e bacon',
            'price' => 28.00,
            'category_id' => $porcoes->id,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        Product::create([
            'name' => 'Onion Rings',
            'description' => 'Anéis de cebola empanados',
            'price' => 22.00,
            'category_id' => $porcoes->id,
            'is_active' => true,
            'sort_order' => 3,
        ]);

        Product::create([
            'name' => 'Nuggets (10 unidades)',
            'description' => 'Nuggets de frango empanados',
            'price' => 24.00,
            'category_id' => $porcoes->id,
            'is_active' => true,
            'sort_order' => 4,
        ]);
    }

    private function createProductWithIngredients(array $productData, array $ingredientsData): void
    {
        $product = Product::create($productData);

        foreach ($ingredientsData as $ing) {
            $ingredientId = $this->ingredients[$ing['name']]['id'] ?? null;

            if ($ingredientId) {
                $product->ingredients()->attach($ingredientId, [
                    'type' => $ing['type'],
                    'quantity' => null,
                    'additional_price' => $ing['price'] ?? null,
                ]);
            }
        }
    }
}
