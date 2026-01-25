<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\OrderItem;
use App\Models\OrderItemIngredient;
use App\Models\OrderPayment;
use App\Models\Product;
use App\Models\Table;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        private readonly OrderRepositoryInterface $repository
    ) {}

    public function all(): Collection
    {
        return $this->repository->all();
    }

    public function filter(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        return $this->repository->filter($filters, $perPage);
    }

    public function findByUuid(string $uuid): ?Order
    {
        return $this->repository->findByUuid($uuid);
    }

    public function getOpenOrders(): Collection
    {
        return $this->repository->getOpenOrders();
    }

    public function getTodayOrders(): Collection
    {
        return $this->repository->getTodayOrders();
    }

    /**
     * Create a new order
     */
    public function create(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $data['user_id'] = Auth::id();

            $order = $this->repository->create($data);

            // Update table status if dine_in
            if ($order->table_id) {
                $order->table->update(['status' => 'occupied']);
            }

            return $order;
        });
    }

    /**
     * Create order from table (quick open)
     */
    public function createFromTable(Table $table, ?string $customerName = null): Order
    {
        return DB::transaction(function () use ($table, $customerName) {
            // Lock table row to prevent race condition
            $table = Table::lockForUpdate()->find($table->id);

            // Check if table already has an open order
            $existingOrder = $this->repository->findOpenByTable($table->id);
            if ($existingOrder) {
                return $existingOrder;
            }

            return $this->create([
                'table_id' => $table->id,
                'type' => 'dine_in',
                'customer_name' => $customerName,
            ]);
        });
    }

    /**
     * Update order
     */
    public function update(Order $order, array $data): Order
    {
        return $this->repository->update($order, $data);
    }

    /**
     * Add item to order
     */
    public function addItem(Order $order, array $data): OrderItem
    {
        return DB::transaction(function () use ($order, $data) {
            $product = Product::findOrFail($data['product_id']);

            // Calculate additions price
            $additionsPrice = 0;
            if (! empty($data['added_ingredients'])) {
                foreach ($data['added_ingredients'] as $ingredient) {
                    $additionsPrice += $ingredient['price'] ?? 0;
                }
            }

            $unitPrice = $product->price;
            $quantity = $data['quantity'] ?? 1;
            $totalPrice = ($unitPrice + $additionsPrice) * $quantity;

            $item = OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'additions_price' => $additionsPrice,
                'total_price' => $totalPrice,
                'notes' => $data['notes'] ?? null,
            ]);

            // Add ingredient customizations
            if (! empty($data['removed_ingredients'])) {
                foreach ($data['removed_ingredients'] as $ingredientData) {
                    OrderItemIngredient::create([
                        'order_item_id' => $item->id,
                        'ingredient_id' => $ingredientData['id'],
                        'ingredient_name' => $ingredientData['name'],
                        'action' => 'removed',
                        'price' => 0,
                    ]);
                }
            }

            if (! empty($data['added_ingredients'])) {
                foreach ($data['added_ingredients'] as $ingredientData) {
                    OrderItemIngredient::create([
                        'order_item_id' => $item->id,
                        'ingredient_id' => $ingredientData['id'],
                        'ingredient_name' => $ingredientData['name'],
                        'action' => 'added',
                        'price' => $ingredientData['price'] ?? 0,
                    ]);
                }
            }

            // Log history
            OrderHistory::create([
                'order_id' => $order->id,
                'event' => 'item_added',
                'description' => "{$quantity}x {$product->name} adicionado",
                'new_value' => $totalPrice,
                'user_id' => Auth::id(),
                'created_at' => now(),
            ]);

            return $item->fresh(['ingredientCustomizations']);
        });
    }

    /**
     * Update item quantity
     */
    public function updateItem(OrderItem $item, array $data): OrderItem
    {
        return DB::transaction(function () use ($item, $data) {
            $oldQuantity = $item->quantity;

            if (isset($data['quantity'])) {
                $item->quantity = $data['quantity'];
                $item->total_price = $item->calculateTotalPrice();
            }

            if (isset($data['notes'])) {
                $item->notes = $data['notes'];
            }

            $item->save();

            if ($oldQuantity !== $item->quantity) {
                OrderHistory::create([
                    'order_id' => $item->order_id,
                    'event' => 'item_updated',
                    'description' => "{$item->product_name}: quantidade alterada de {$oldQuantity} para {$item->quantity}",
                    'user_id' => Auth::id(),
                    'created_at' => now(),
                ]);
            }

            return $item;
        });
    }

    /**
     * Cancel item
     */
    public function cancelItem(OrderItem $item, ?string $reason = null): OrderItem
    {
        return DB::transaction(function () use ($item, $reason) {
            $item->update(['status' => 'cancelled']);

            OrderHistory::create([
                'order_id' => $item->order_id,
                'event' => 'item_cancelled',
                'description' => "{$item->quantity}x {$item->product_name} cancelado" . ($reason ? " - {$reason}" : ''),
                'user_id' => Auth::id(),
                'created_at' => now(),
            ]);

            return $item;
        });
    }

    /**
     * Send items to kitchen
     */
    public function sendToKitchen(Order $order, ?array $itemIds = null): int
    {
        return DB::transaction(function () use ($order, $itemIds) {
            $query = $order->items()->where('status', 'pending');

            if ($itemIds) {
                $query->whereIn('id', $itemIds);
            }

            $count = $query->update([
                'status' => 'preparing',
                'sent_to_kitchen_at' => now(),
            ]);

            if ($count > 0) {
                OrderHistory::create([
                    'order_id' => $order->id,
                    'event' => 'item_sent_to_kitchen',
                    'description' => "{$count} item(ns) enviado(s) para cozinha",
                    'user_id' => Auth::id(),
                    'created_at' => now(),
                ]);
            }

            return $count;
        });
    }

    /**
     * Mark item as ready
     */
    public function markItemReady(OrderItem $item): OrderItem
    {
        $item->update([
            'status' => 'ready',
            'ready_at' => now(),
        ]);

        OrderHistory::create([
            'order_id' => $item->order_id,
            'event' => 'item_ready',
            'description' => "{$item->product_name} pronto para servir",
            'user_id' => Auth::id(),
            'created_at' => now(),
        ]);

        return $item;
    }

    /**
     * Mark item as delivered
     */
    public function markItemDelivered(OrderItem $item): OrderItem
    {
        $item->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);

        OrderHistory::create([
            'order_id' => $item->order_id,
            'event' => 'item_delivered',
            'description' => "{$item->product_name} entregue",
            'user_id' => Auth::id(),
            'created_at' => now(),
        ]);

        return $item;
    }

    /**
     * Add payment
     */
    public function addPayment(Order $order, array $data): OrderPayment
    {
        return DB::transaction(function () use ($order, $data) {
            $payment = OrderPayment::create([
                'order_id' => $order->id,
                'method' => $data['method'],
                'amount' => $data['amount'],
                'received_by' => Auth::id(),
                'notes' => $data['notes'] ?? null,
            ]);

            OrderHistory::create([
                'order_id' => $order->id,
                'event' => 'payment_added',
                'description' => "Pagamento {$payment->method_label}: R$ " . number_format($payment->amount, 2, ',', '.'),
                'new_value' => $payment->amount,
                'user_id' => Auth::id(),
                'created_at' => now(),
            ]);

            return $payment;
        });
    }

    /**
     * Remove payment
     */
    public function removePayment(OrderPayment $payment): bool
    {
        $order = $payment->order;

        OrderHistory::create([
            'order_id' => $order->id,
            'event' => 'payment_removed',
            'description' => "Pagamento {$payment->method_label} removido: R$ " . number_format($payment->amount, 2, ',', '.'),
            'old_value' => $payment->amount,
            'user_id' => Auth::id(),
            'created_at' => now(),
        ]);

        return $payment->delete();
    }

    /**
     * Apply discount
     */
    public function applyDiscount(Order $order, float $discount): Order
    {
        return $this->repository->update($order, [
            'discount' => $discount,
            'total' => max(0, $order->subtotal - $discount),
        ]);
    }

    /**
     * Close order
     */
    public function close(Order $order): Order
    {
        return DB::transaction(function () use ($order) {
            // Mark all non-cancelled items as delivered
            $order->items()
                ->whereNotIn('status', ['cancelled', 'delivered'])
                ->update([
                    'status' => 'delivered',
                    'delivered_at' => now(),
                ]);

            $order = $this->repository->update($order, [
                'status' => 'closed',
                'closed_at' => now(),
            ]);

            // Free the table
            if ($order->table_id) {
                $order->table->update(['status' => 'available']);
            }

            return $order;
        });
    }

    /**
     * Cancel order
     */
    public function cancel(Order $order, ?string $reason = null): Order
    {
        return DB::transaction(function () use ($order, $reason) {
            // Cancel all non-delivered items
            $order->items()
                ->whereNotIn('status', ['delivered', 'cancelled'])
                ->update(['status' => 'cancelled']);

            $order = $this->repository->update($order, [
                'status' => 'cancelled',
                'closed_at' => now(),
                'notes' => $order->notes . ($reason ? "\nCancelamento: {$reason}" : ''),
            ]);

            // Free the table
            if ($order->table_id) {
                $order->table->update(['status' => 'available']);
            }

            return $order;
        });
    }

    /**
     * Reopen order
     */
    public function reopen(Order $order): Order
    {
        return DB::transaction(function () use ($order) {
            $order = $this->repository->update($order, [
                'status' => 'open',
                'closed_at' => null,
            ]);

            // Mark table as occupied again
            if ($order->table_id) {
                $order->table->update(['status' => 'occupied']);
            }

            return $order;
        });
    }

    /**
     * Transfer order to another table
     */
    public function transferToTable(Order $order, Table $newTable): Order
    {
        return DB::transaction(function () use ($order, $newTable) {
            $oldTable = $order->table;

            // Free old table
            if ($oldTable) {
                $oldTable->update(['status' => 'available']);
            }

            // Occupy new table
            $newTable->update(['status' => 'occupied']);

            $order = $this->repository->update($order, [
                'table_id' => $newTable->id,
            ]);

            OrderHistory::create([
                'order_id' => $order->id,
                'event' => 'updated',
                'field' => 'table_id',
                'old_value' => $oldTable?->number,
                'new_value' => $newTable->number,
                'description' => 'Pedido transferido para mesa ' . $newTable->number,
                'user_id' => Auth::id(),
                'created_at' => now(),
            ]);

            return $order;
        });
    }
}
