<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class OrderPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'order_id',
        'method',
        'amount',
        'received_by',
        'notes',
        'paid_at',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (OrderPayment $payment): void {
            if (empty($payment->uuid)) {
                $payment->uuid = Str::uuid();
            }
            if (empty($payment->paid_at)) {
                $payment->paid_at = now();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    // Relationships

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function receivedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    // Accessors

    public function getMethodLabelAttribute(): string
    {
        return match ($this->method) {
            'credit_card' => 'Cartão de Crédito',
            'debit_card' => 'Cartão de Débito',
            'cash' => 'Dinheiro',
            'pix' => 'PIX',
            default => $this->method,
        };
    }

    public function getMethodIconAttribute(): string
    {
        return match ($this->method) {
            'credit_card', 'debit_card' => 'credit-card',
            'cash' => 'banknotes',
            'pix' => 'qr-code',
            default => 'currency-dollar',
        };
    }
}
