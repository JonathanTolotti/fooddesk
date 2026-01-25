<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'order_id',
        'product_id',
        'product_name',
        'quantity',
        'unit_price',
        'additions_price',
        'total_price',
        'status',
        'notes',
        'sent_to_kitchen_at',
        'ready_at',
        'delivered_at',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (OrderItem $item): void {
            if (empty($item->uuid)) {
                $item->uuid = Str::uuid();
            }
        });

        static::saved(function (OrderItem $item): void {
            $item->order->recalculateTotals();
        });

        static::deleted(function (OrderItem $item): void {
            $item->order->recalculateTotals();
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'additions_price' => 'decimal:2',
            'total_price' => 'decimal:2',
            'sent_to_kitchen_at' => 'datetime',
            'ready_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    // Relationships

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function ingredientCustomizations(): HasMany
    {
        return $this->hasMany(OrderItemIngredient::class);
    }

    public function addedIngredients(): HasMany
    {
        return $this->ingredientCustomizations()->where('action', 'added');
    }

    public function removedIngredients(): HasMany
    {
        return $this->ingredientCustomizations()->where('action', 'removed');
    }

    // Accessors

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Pendente',
            'preparing' => 'Preparando',
            'ready' => 'Pronto',
            'delivered' => 'Entregue',
            'cancelled' => 'Cancelado',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'yellow',
            'preparing' => 'blue',
            'ready' => 'green',
            'delivered' => 'gray',
            'cancelled' => 'red',
            default => 'gray',
        };
    }

    // Helper Methods

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isPreparing(): bool
    {
        return $this->status === 'preparing';
    }

    public function isReady(): bool
    {
        return $this->status === 'ready';
    }

    public function isDelivered(): bool
    {
        return $this->status === 'delivered';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'preparing']);
    }

    public function canBeSentToKitchen(): bool
    {
        return $this->status === 'pending';
    }

    public function calculateTotalPrice(): float
    {
        return ($this->unit_price + $this->additions_price) * $this->quantity;
    }

    public function recalculatePrice(): void
    {
        $this->additions_price = $this->addedIngredients()->sum('price');
        $this->total_price = $this->calculateTotalPrice();
        $this->save();
    }
}
