<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use Illuminate\Database\Seeder;

class IngredientSeeder extends Seeder
{
    public function run(): void
    {
        $ingredients = [
            // Pães
            ['name' => 'Pão de hambúrguer', 'description' => 'Pão tradicional para hambúrguer', 'is_active' => true],
            ['name' => 'Pão de hot dog', 'description' => 'Pão para cachorro-quente', 'is_active' => true],
            ['name' => 'Pão francês', 'description' => 'Pão francês tradicional', 'is_active' => true],
            ['name' => 'Pão australiano', 'description' => 'Pão australiano escuro', 'is_active' => true],

            // Carnes
            ['name' => 'Hambúrguer bovino 120g', 'description' => 'Blend bovino artesanal', 'is_active' => true],
            ['name' => 'Hambúrguer bovino 180g', 'description' => 'Blend bovino artesanal grande', 'is_active' => true],
            ['name' => 'Salsicha', 'description' => 'Salsicha para hot dog', 'is_active' => true],
            ['name' => 'Frango desfiado', 'description' => 'Peito de frango desfiado temperado', 'is_active' => true],
            ['name' => 'Bacon', 'description' => 'Fatias de bacon crocante', 'is_active' => true],
            ['name' => 'Calabresa', 'description' => 'Calabresa fatiada', 'is_active' => true],

            // Queijos
            ['name' => 'Queijo cheddar', 'description' => 'Fatia de queijo cheddar', 'is_active' => true],
            ['name' => 'Queijo mussarela', 'description' => 'Fatia de queijo mussarela', 'is_active' => true],
            ['name' => 'Queijo prato', 'description' => 'Fatia de queijo prato', 'is_active' => true],
            ['name' => 'Cream cheese', 'description' => 'Cream cheese cremoso', 'is_active' => true],

            // Vegetais
            ['name' => 'Alface', 'description' => 'Folhas de alface americana', 'is_active' => true],
            ['name' => 'Tomate', 'description' => 'Rodelas de tomate', 'is_active' => true],
            ['name' => 'Cebola', 'description' => 'Rodelas de cebola', 'is_active' => true],
            ['name' => 'Cebola caramelizada', 'description' => 'Cebola caramelizada no açúcar', 'is_active' => true],
            ['name' => 'Picles', 'description' => 'Fatias de picles', 'is_active' => true],
            ['name' => 'Milho', 'description' => 'Milho verde', 'is_active' => true],
            ['name' => 'Batata palha', 'description' => 'Batata palha crocante', 'is_active' => true],
            ['name' => 'Rúcula', 'description' => 'Folhas de rúcula', 'is_active' => true],
            ['name' => 'Jalapeño', 'description' => 'Pimenta jalapeño em rodelas', 'is_active' => true],

            // Molhos
            ['name' => 'Maionese', 'description' => 'Maionese tradicional', 'is_active' => true],
            ['name' => 'Ketchup', 'description' => 'Ketchup tradicional', 'is_active' => true],
            ['name' => 'Mostarda', 'description' => 'Mostarda amarela', 'is_active' => true],
            ['name' => 'Molho especial', 'description' => 'Molho especial da casa', 'is_active' => true],
            ['name' => 'Molho barbecue', 'description' => 'Molho barbecue defumado', 'is_active' => true],
            ['name' => 'Maionese de alho', 'description' => 'Maionese temperada com alho', 'is_active' => true],
            ['name' => 'Molho chipotle', 'description' => 'Molho de pimenta chipotle', 'is_active' => true],

            // Extras
            ['name' => 'Ovo', 'description' => 'Ovo frito', 'is_active' => true],
            ['name' => 'Catupiry', 'description' => 'Requeijão cremoso catupiry', 'is_active' => true],
            ['name' => 'Cheddar cremoso', 'description' => 'Cheddar cremoso derretido', 'is_active' => true],
        ];

        foreach ($ingredients as $ingredient) {
            Ingredient::create($ingredient);
        }
    }
}
