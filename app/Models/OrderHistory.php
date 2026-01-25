<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderHistory extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'event',
        'field',
        'old_value',
        'new_value',
        'description',
        'user_id',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    // Relationships

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Accessors

    public function getEventLabelAttribute(): string
    {
        return match ($this->event) {
            'created' => 'Pedido criado',
            'updated' => 'Pedido atualizado',
            'item_added' => 'Item adicionado',
            'item_updated' => 'Item atualizado',
            'item_cancelled' => 'Item cancelado',
            'item_sent_to_kitchen' => 'Item enviado para cozinha',
            'item_ready' => 'Item pronto',
            'item_delivered' => 'Item entregue',
            'payment_added' => 'Pagamento adicionado',
            'payment_removed' => 'Pagamento removido',
            'discount_applied' => 'Desconto aplicado',
            'closed' => 'Pedido fechado',
            'cancelled' => 'Pedido cancelado',
            'reopened' => 'Pedido reaberto',
            default => $this->event,
        };
    }

    public function getFieldLabelAttribute(): string
    {
        return match ($this->field) {
            'status' => 'Status',
            'discount' => 'Desconto',
            'service_fee' => 'Taxa de Serviço',
            'notes' => 'Observações',
            'customer_name' => 'Cliente',
            'table_id' => 'Mesa',
            default => $this->field ?? '-',
        };
    }

    public function getFormattedOldValueAttribute(): string
    {
        return $this->formatValue($this->field, $this->old_value);
    }

    public function getFormattedNewValueAttribute(): string
    {
        return $this->formatValue($this->field, $this->new_value);
    }

    private function formatValue(?string $field, ?string $value): string
    {
        if ($value === null) {
            return '-';
        }

        if ($field === 'status') {
            return match ($value) {
                'open' => 'Aberto',
                'closed' => 'Fechado',
                'cancelled' => 'Cancelado',
                default => $value,
            };
        }

        if ($field === 'discount' || $field === 'service_fee' || str_contains($field ?? '', 'price') || str_contains($field ?? '', 'amount')) {
            return 'R$ ' . number_format((float) $value, 2, ',', '.');
        }

        return $value;
    }
}
