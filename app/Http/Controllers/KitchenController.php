<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KitchenController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService
    ) {}

    /**
     * Display the kitchen view
     */
    public function index(): View
    {
        return view('kitchen.index');
    }

    /**
     * Get items for kitchen display (AJAX)
     */
    public function items(): JsonResponse
    {
        // Get items that are preparing (sent to kitchen)
        $items = OrderItem::with(['order.table', 'ingredientCustomizations'])
            ->whereHas('order', fn ($q) => $q->where('status', 'open'))
            ->where('status', 'preparing')
            ->orderBy('sent_to_kitchen_at', 'asc')
            ->get();

        $itemsData = $items->map(function (OrderItem $item) {
            return [
                'id' => $item->id,
                'uuid' => $item->uuid,
                'order_uuid' => $item->order->uuid,
                'order_id' => $item->order->id,
                'table_number' => $item->order->table?->number,
                'table_name' => $item->order->table?->name,
                'customer_name' => $item->order->customer_name,
                'display_name' => $item->order->table
                    ? 'Mesa ' . $item->order->table->number
                    : ($item->order->customer_name ?: 'Pedido #' . $item->order->id),
                'product_name' => $item->product_name,
                'quantity' => $item->quantity,
                'notes' => $item->notes,
                'sent_at' => $item->sent_to_kitchen_at?->format('H:i'),
                'waiting_minutes' => $item->sent_to_kitchen_at
                    ? (int) $item->sent_to_kitchen_at->diffInMinutes(now())
                    : 0,
                'customizations' => $item->ingredientCustomizations->map(fn ($c) => [
                    'ingredient_name' => $c->ingredient_name,
                    'action' => $c->action,
                    'display_text' => $c->display_text,
                ]),
            ];
        });

        // Group by order for alternative view
        $byOrder = $items->groupBy('order_id')->map(function ($orderItems) {
            $first = $orderItems->first();

            return [
                'order_id' => $first->order->id,
                'order_uuid' => $first->order->uuid,
                'table_number' => $first->order->table?->number,
                'display_name' => $first->order->table
                    ? 'Mesa ' . $first->order->table->number
                    : ($first->order->customer_name ?: 'Pedido #' . $first->order->id),
                'items_count' => $orderItems->count(),
                'oldest_item_minutes' => $orderItems->min(fn ($i) => $i->sent_to_kitchen_at
                    ? (int) $i->sent_to_kitchen_at->diffInMinutes(now())
                    : 0),
                'items' => $orderItems->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'uuid' => $item->uuid,
                        'product_name' => $item->product_name,
                        'quantity' => $item->quantity,
                        'notes' => $item->notes,
                        'sent_at' => $item->sent_to_kitchen_at?->format('H:i'),
                        'waiting_minutes' => $item->sent_to_kitchen_at
                            ? (int) $item->sent_to_kitchen_at->diffInMinutes(now())
                            : 0,
                        'customizations' => $item->ingredientCustomizations->map(fn ($c) => [
                            'ingredient_name' => $c->ingredient_name,
                            'action' => $c->action,
                            'display_text' => $c->display_text,
                        ]),
                    ];
                })->values(),
            ];
        })->values();

        return response()->json([
            'items' => $itemsData,
            'by_order' => $byOrder,
            'total_items' => $items->count(),
        ]);
    }

    /**
     * Mark item as ready
     */
    public function markReady(Request $request, OrderItem $item): JsonResponse
    {
        if ($item->status !== 'preparing') {
            return response()->json([
                'message' => 'Este item não está em preparo.',
            ], 422);
        }

        $item->status = 'ready';
        $item->ready_at = now();
        $item->save();

        return response()->json([
            'message' => 'Item marcado como pronto.',
        ]);
    }

    /**
     * Mark all items of an order as ready
     */
    public function markOrderReady(Request $request, string $orderUuid): JsonResponse
    {
        $items = OrderItem::whereHas('order', fn ($q) => $q->where('uuid', $orderUuid))
            ->where('status', 'preparing')
            ->get();

        if ($items->isEmpty()) {
            return response()->json([
                'message' => 'Nenhum item em preparo neste pedido.',
            ], 422);
        }

        $items->each(function ($item) {
            $item->status = 'ready';
            $item->ready_at = now();
            $item->save();
        });

        return response()->json([
            'message' => $items->count() . ' item(ns) marcado(s) como pronto(s).',
            'count' => $items->count(),
        ]);
    }
}
