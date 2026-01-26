<?php

namespace App\Models;

use App\Observers\OrderObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[ObservedBy(OrderObserver::class)]
class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'table_id',
        'customer_id',
        'user_id',
        'type',
        'status',
        'customer_name',
        'customer_phone',
        'delivery_address',
        'external_id',
        'external_data',
        'subtotal',
        'discount',
        'service_fee',
        'total',
        'notes',
        'opened_at',
        'closed_at',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Order $order): void {
            if (empty($order->uuid)) {
                $order->uuid = Str::uuid();
            }
            if (empty($order->opened_at)) {
                $order->opened_at = now();
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
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'service_fee' => 'decimal:2',
            'total' => 'decimal:2',
            'external_data' => 'array',
            'opened_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    // Relationships

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(OrderPayment::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(OrderHistory::class)->orderByDesc('created_at');
    }

    // Accessors

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'dine_in' => 'Mesa',
            'takeaway' => 'Balcão',
            'delivery' => 'Delivery',
            'ifood' => 'iFood',
            'anota_ai' => 'Anota Ai',
            default => $this->type,
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'open' => 'Aberto',
            'closed' => 'Fechado',
            'cancelled' => 'Cancelado',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'open' => 'green',
            'closed' => 'blue',
            'cancelled' => 'red',
            default => 'gray',
        };
    }

    public function getTotalPaidAttribute(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, (float) $this->total - $this->total_paid);
    }

    public function getIsFullyPaidAttribute(): bool
    {
        return $this->remaining_amount <= 0;
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->table_id) {
            $table = $this->getRelationValue('table');
            if ($table instanceof Table) {
                return "Mesa {$table->number}";
            }
            return "Mesa #{$this->table_id}";
        }
        if ($this->customer_name) {
            return $this->customer_name;
        }
        return "Pedido #{$this->id}";
    }

    // Helper Methods

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function canAddItems(): bool
    {
        return $this->isOpen();
    }

    public function canClose(): bool
    {
        return $this->isOpen() && $this->is_fully_paid;
    }

    public function recalculateTotals(): void
    {
        $oldSubtotal = (float) $this->subtotal;

        $subtotal = $this->items()
            ->where('status', '!=', 'cancelled')
            ->sum('total_price');

        $this->subtotal = $subtotal;

        // Auto-apply service fee on first item if enabled in settings
        if ($oldSubtotal == 0 && $subtotal > 0 && $this->service_fee == 0) {
            $settingService = app(\App\Services\SettingService::class);
            if ($settingService->get('service_fee_enabled', true)) {
                $this->service_fee = $this->calculateServiceFee();
            }
        }
        // Recalculate service fee if it's already active
        elseif ($this->service_fee > 0) {
            $this->service_fee = $this->calculateServiceFee();
        }

        $this->total = max(0, $subtotal - $this->discount + $this->service_fee);
        $this->save();
    }

    /**
     * Check if service fee is applied
     */
    public function hasServiceFee(): bool
    {
        return $this->service_fee > 0;
    }

    /**
     * Calculate service fee based on settings.
     */
    public function calculateServiceFee(): float
    {
        $settingService = app(\App\Services\SettingService::class);

        return $settingService->calculateServiceFee((float) $this->subtotal);
    }
}
