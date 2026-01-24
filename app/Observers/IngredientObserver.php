<?php

namespace App\Observers;

use App\Models\Ingredient;
use App\Models\IngredientHistory;
use Illuminate\Support\Facades\Auth;

class IngredientObserver
{
    protected array $auditableFields = [
        'name',
        'description',
        'is_active',
    ];

    public function created(Ingredient $ingredient): void
    {
        IngredientHistory::create([
            'ingredient_id' => $ingredient->id,
            'event' => 'created',
            'field' => null,
            'old_value' => null,
            'new_value' => null,
            'user_id' => Auth::id(),
            'created_at' => now(),
        ]);
    }

    public function updated(Ingredient $ingredient): void
    {
        $changes = $ingredient->getChanges();

        foreach ($this->auditableFields as $field) {
            if (array_key_exists($field, $changes)) {
                $oldValue = $ingredient->getOriginal($field);
                $newValue = $changes[$field];

                if (is_bool($oldValue)) {
                    $oldValue = $oldValue ? '1' : '0';
                }
                if (is_bool($newValue)) {
                    $newValue = $newValue ? '1' : '0';
                }

                IngredientHistory::create([
                    'ingredient_id' => $ingredient->id,
                    'event' => 'updated',
                    'field' => $field,
                    'old_value' => $oldValue,
                    'new_value' => $newValue,
                    'user_id' => Auth::id(),
                    'created_at' => now(),
                ]);
            }
        }
    }

    public function deleted(Ingredient $ingredient): void
    {
        IngredientHistory::create([
            'ingredient_id' => $ingredient->id,
            'event' => 'deleted',
            'field' => null,
            'old_value' => null,
            'new_value' => null,
            'user_id' => Auth::id(),
            'created_at' => now(),
        ]);
    }
}
