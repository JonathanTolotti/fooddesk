<?php

namespace App\Observers;

use App\Models\Category;
use App\Models\CategoryHistory;
use Illuminate\Support\Facades\Auth;

class CategoryObserver
{
    protected array $auditableFields = [
        'name',
        'description',
        'is_active',
        'sort_order',
    ];

    public function created(Category $category): void
    {
        CategoryHistory::create([
            'category_id' => $category->id,
            'event' => 'created',
            'field' => null,
            'old_value' => null,
            'new_value' => null,
            'user_id' => Auth::id(),
            'created_at' => now(),
        ]);
    }

    public function updated(Category $category): void
    {
        $changes = $category->getChanges();

        foreach ($this->auditableFields as $field) {
            if (array_key_exists($field, $changes)) {
                $oldValue = $category->getOriginal($field);
                $newValue = $changes[$field];

                // Converte boolean para string para armazenamento
                if (is_bool($oldValue)) {
                    $oldValue = $oldValue ? '1' : '0';
                }
                if (is_bool($newValue)) {
                    $newValue = $newValue ? '1' : '0';
                }

                CategoryHistory::create([
                    'category_id' => $category->id,
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

    public function deleted(Category $category): void
    {
        CategoryHistory::create([
            'category_id' => $category->id,
            'event' => 'deleted',
            'field' => null,
            'old_value' => null,
            'new_value' => null,
            'user_id' => Auth::id(),
            'created_at' => now(),
        ]);
    }
}