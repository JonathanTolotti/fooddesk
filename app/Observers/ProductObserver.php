<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\ProductHistory;
use Illuminate\Support\Facades\Auth;

class ProductObserver
{
    protected array $auditableFields = [
        'name',
        'description',
        'price',
        'category_id',
        'image',
        'is_active',
        'sort_order',
    ];

    public function created(Product $product): void
    {
        ProductHistory::create([
            'product_id' => $product->id,
            'event' => 'created',
            'field' => null,
            'old_value' => null,
            'new_value' => null,
            'user_id' => Auth::id(),
            'created_at' => now(),
        ]);
    }

    public function updated(Product $product): void
    {
        $changes = $product->getChanges();

        foreach ($this->auditableFields as $field) {
            if (array_key_exists($field, $changes)) {
                $oldValue = $product->getOriginal($field);
                $newValue = $changes[$field];

                if (is_bool($oldValue)) {
                    $oldValue = $oldValue ? '1' : '0';
                }
                if (is_bool($newValue)) {
                    $newValue = $newValue ? '1' : '0';
                }

                ProductHistory::create([
                    'product_id' => $product->id,
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

    public function deleted(Product $product): void
    {
        ProductHistory::create([
            'product_id' => $product->id,
            'event' => 'deleted',
            'field' => null,
            'old_value' => null,
            'new_value' => null,
            'user_id' => Auth::id(),
            'created_at' => now(),
        ]);
    }
}
