<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\OrderHistory;
use Illuminate\Support\Facades\Auth;

class OrderObserver
{
    protected array $auditableFields = [
        'status',
        'discount',
        'notes',
        'customer_name',
        'table_id',
    ];

    public function created(Order $order): void
    {
        OrderHistory::create([
            'order_id' => $order->id,
            'event' => 'created',
            'description' => "Pedido criado - {$order->type_label}",
            'user_id' => Auth::id(),
            'created_at' => now(),
        ]);
    }

    public function updated(Order $order): void
    {
        $changes = $order->getChanges();

        foreach ($this->auditableFields as $field) {
            if (array_key_exists($field, $changes)) {
                $oldValue = $order->getOriginal($field);
                $newValue = $changes[$field];

                // Determina o evento baseado no campo
                $event = 'updated';
                if ($field === 'status') {
                    $event = match ($newValue) {
                        'closed' => 'closed',
                        'cancelled' => 'cancelled',
                        'open' => 'reopened',
                        default => 'updated',
                    };
                } elseif ($field === 'discount') {
                    $event = 'discount_applied';
                }

                OrderHistory::create([
                    'order_id' => $order->id,
                    'event' => $event,
                    'field' => $field,
                    'old_value' => $oldValue,
                    'new_value' => $newValue,
                    'user_id' => Auth::id(),
                    'created_at' => now(),
                ]);
            }
        }
    }
}
