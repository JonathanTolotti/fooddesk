<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class WaiterController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService
    ) {}

    /**
     * Display the waiter dashboard
     */
    public function index(): View
    {
        return view('waiter.index');
    }

    /**
     * Get open orders data for the waiter view (AJAX)
     */
    public function orders(): JsonResponse
    {
        $orders = $this->orderService->getOpenOrders();

        $ordersData = $orders->map(function (Order $order) {
            $items = $order->items()->with('ingredientCustomizations')->get();

            $pendingItems = $items->where('status', 'pending');
            $preparingItems = $items->where('status', 'preparing');
            $readyItems = $items->where('status', 'ready');
            $deliveredItems = $items->where('status', 'delivered');

            return [
                'id' => $order->id,
                'uuid' => $order->uuid,
                'table_number' => $order->table?->number,
                'table_name' => $order->table?->name,
                'customer_name' => $order->customer_name,
                'display_name' => $order->display_name,
                'type' => $order->type,
                'type_label' => $order->type_label,
                'subtotal' => (float) $order->subtotal,
                'discount' => (float) $order->discount,
                'service_fee' => (float) $order->service_fee,
                'total' => (float) $order->total,
                'total_paid' => $order->total_paid,
                'remaining_amount' => $order->remaining_amount,
                'is_fully_paid' => $order->is_fully_paid,
                'opened_at' => $order->opened_at->format('H:i'),
                'duration_minutes' => (int) $order->opened_at->diffInMinutes(now()),
                'items_summary' => [
                    'pending' => $pendingItems->count(),
                    'preparing' => $preparingItems->count(),
                    'ready' => $readyItems->count(),
                    'delivered' => $deliveredItems->count(),
                    'total' => $items->where('status', '!=', 'cancelled')->count(),
                ],
                'items' => $items->where('status', '!=', 'cancelled')->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'uuid' => $item->uuid,
                        'product_name' => $item->product_name,
                        'quantity' => $item->quantity,
                        'total_price' => (float) $item->total_price,
                        'status' => $item->status,
                        'status_label' => $item->status_label,
                        'notes' => $item->notes,
                        'customizations' => $item->ingredientCustomizations->map(fn ($c) => [
                            'ingredient_name' => $c->ingredient_name,
                            'action' => $c->action,
                            'display_text' => $c->display_text,
                        ]),
                    ];
                })->values(),
            ];
        });

        return response()->json([
            'orders' => $ordersData,
        ]);
    }
}
