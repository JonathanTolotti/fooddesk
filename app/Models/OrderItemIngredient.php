<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItemIngredient extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'order_item_id',
        'ingredient_id',
        'ingredient_name',
        'action',
        'price',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    // Relationships

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }

    // Accessors

    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'removed' => 'Sem',
            'added' => 'Com',
            default => $this->action,
        };
    }

    public function getDisplayTextAttribute(): string
    {
        $prefix = $this->action === 'removed' ? '- Sem' : '+ Com';
        $price = $this->price > 0 ? ' (+R$ ' . number_format($this->price, 2, ',', '.') . ')' : '';

        return "{$prefix} {$this->ingredient_name}{$price}";
    }

    // Helper Methods

    public function isRemoved(): bool
    {
        return $this->action === 'removed';
    }

    public function isAdded(): bool
    {
        return $this->action === 'added';
    }
}
