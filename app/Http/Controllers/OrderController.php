<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddOrderItemRequest;
use App\Http\Requests\AddPaymentRequest;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderItemRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use App\Models\Table;
use App\Services\CategoryService;
use App\Services\OrderService;
use App\Services\ProductService;
use App\Services\TableService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService,
        private readonly TableService $tableService,
        private readonly CategoryService $categoryService,
        private readonly ProductService $productService
    ) {}

    /**
     * Display orders listing
     */
    public function index(): View
    {
        return view('orders.index');
    }

    /**
     * Filter orders (AJAX)
     */
    public function filter(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'status', 'type', 'table_id', 'date_from', 'date_to']);
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        request()->merge(['page' => $page]);

        $orders = $this->orderService->filter($filters, $perPage);

        return response()->json([
            'orders' => $orders->getCollection()->map(fn (Order $order) => [
                'id' => $order->id,
                'uuid' => $order->uuid,
                'table_number' => $order->table?->number,
                'table_name' => $order->table?->name,
                'type' => $order->type,
                'type_label' => $order->type_label,
                'status' => $order->status,
                'status_label' => $order->status_label,
                'status_color' => $order->status_color,
                'customer_name' => $order->customer_name,
                'display_name' => $order->display_name,
                'user_name' => $order->user?->name,
                'subtotal' => (float) $order->subtotal,
                'discount' => (float) $order->discount,
                'service_fee' => (float) $order->service_fee,
                'total' => (float) $order->total,
                'total_paid' => $order->total_paid,
                'remaining_amount' => $order->remaining_amount,
                'items_count' => $order->items()->count(),
                'opened_at' => $order->opened_at->format('d/m/Y H:i'),
                'closed_at' => $order->closed_at?->format('d/m/Y H:i'),
            ]),
            'pagination' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ],
        ]);
    }

    /**
     * Get open orders (for dashboard)
     */
    public function openOrders(): JsonResponse
    {
        $orders = $this->orderService->getOpenOrders();

        return response()->json([
            'orders' => $orders->map(fn (Order $order) => [
                'id' => $order->id,
                'uuid' => $order->uuid,
                'table_number' => $order->table?->number,
                'display_name' => $order->display_name,
                'type_label' => $order->type_label,
                'total' => (float) $order->total,
                'items_count' => $order->items->count(),
                'pending_items' => $order->items->where('status', 'pending')->count(),
                'preparing_items' => $order->items->where('status', 'preparing')->count(),
                'ready_items' => $order->items->where('status', 'ready')->count(),
                'opened_at' => $order->opened_at->format('H:i'),
                'duration_minutes' => (int) $order->opened_at->diffInMinutes(now()),
            ]),
        ]);
    }

    /**
     * Store a new order
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $order = $this->orderService->create($request->validated());

        return response()->json([
            'message' => 'Pedido criado com sucesso.',
            'order' => [
                'id' => $order->id,
                'uuid' => $order->uuid,
            ],
        ], 201);
    }

    /**
     * Open order from table
     */
    public function openFromTable(Request $request, Table $table): JsonResponse
    {
        $order = $this->orderService->createFromTable(
            $table,
            $request->input('customer_name'),
            $request->input('customer_id')
        );

        return response()->json([
            'message' => $order->wasRecentlyCreated ? 'Pedido criado com sucesso.' : 'Pedido já existe para esta mesa.',
            'order' => [
                'id' => $order->id,
                'uuid' => $order->uuid,
                'is_new' => $order->wasRecentlyCreated,
            ],
        ], $order->wasRecentlyCreated ? 201 : 200);
    }

    /**
     * Show order details
     */
    public function show(Order $order): View
    {
        $order->load(['table', 'user', 'items.ingredientCustomizations', 'payments.receivedByUser']);

        $categories = $this->categoryService->all();
        $tables = $this->tableService->allActive();

        // Prepare data for JavaScript
        $orderData = [
            'uuid' => $order->uuid,
            'status' => $order->status,
            'subtotal' => (float) $order->subtotal,
            'discount' => (float) $order->discount,
            'service_fee' => (float) $order->service_fee,
            'total' => (float) $order->total,
            'total_paid' => $order->total_paid,
            'remaining_amount' => $order->remaining_amount,
            'is_fully_paid' => $order->is_fully_paid,
        ];

        $itemsData = $order->items->map(function ($item) {
            return [
                'id' => $item->id,
                'uuid' => $item->uuid,
                'product_id' => $item->product_id,
                'product_name' => $item->product_name,
                'quantity' => $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'additions_price' => (float) $item->additions_price,
                'total_price' => (float) $item->total_price,
                'status' => $item->status,
                'status_label' => $item->status_label,
                'notes' => $item->notes,
                'can_be_cancelled' => $item->canBeCancelled(),
                'customizations' => $item->ingredientCustomizations->map(function ($c) {
                    return [
                        'ingredient_name' => $c->ingredient_name,
                        'action' => $c->action,
                        'price' => (float) $c->price,
                        'display_text' => $c->display_text,
                    ];
                }),
            ];
        });

        $paymentsData = $order->payments->map(function ($p) {
            return [
                'id' => $p->id,
                'uuid' => $p->uuid,
                'method' => $p->method,
                'method_label' => $p->method_label,
                'amount' => (float) $p->amount,
                'paid_at' => $p->paid_at->format('d/m/Y H:i'),
            ];
        });

        return view('orders.show', compact('order', 'categories', 'tables', 'orderData', 'itemsData', 'paymentsData'));
    }

    /**
     * Get active products for adding to orders (AJAX)
     */
    public function getProducts(): JsonResponse
    {
        $products = $this->productService->filter(['status' => 'active'], 100);

        return response()->json([
            'products' => $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'price' => (float) $product->price,
                    'category_id' => $product->category_id,
                    'category_name' => $product->category?->name,
                    'image' => $product->image,
                    'ingredients' => $product->ingredients->map(fn ($i) => [
                        'id' => $i->id,
                        'name' => $i->name,
                        'type' => $i->pivot->type,
                        'additional_price' => (float) $i->pivot->additional_price,
                    ]),
                ];
            }),
        ]);
    }

    /**
     * Get order data (AJAX)
     */
    public function getData(Order $order): JsonResponse
    {
        $order->load(['table', 'user', 'items.ingredientCustomizations', 'payments.receivedByUser']);

        return response()->json([
            'order' => [
                'id' => $order->id,
                'uuid' => $order->uuid,
                'table_id' => $order->table_id,
                'table_number' => $order->table?->number,
                'table_name' => $order->table?->name,
                'type' => $order->type,
                'type_label' => $order->type_label,
                'status' => $order->status,
                'status_label' => $order->status_label,
                'customer_name' => $order->customer_name,
                'customer_phone' => $order->customer_phone,
                'delivery_address' => $order->delivery_address,
                'user_name' => $order->user?->name,
                'subtotal' => (float) $order->subtotal,
                'discount' => (float) $order->discount,
                'service_fee' => (float) $order->service_fee,
                'total' => (float) $order->total,
                'total_paid' => $order->total_paid,
                'remaining_amount' => $order->remaining_amount,
                'is_fully_paid' => $order->is_fully_paid,
                'notes' => $order->notes,
                'opened_at' => $order->opened_at->format('d/m/Y H:i'),
                'closed_at' => $order->closed_at?->format('d/m/Y H:i'),
                'duration_minutes' => (int) $order->opened_at->diffInMinutes(now()),
            ],
            'items' => $order->items->map(fn (OrderItem $item) => [
                'id' => $item->id,
                'uuid' => $item->uuid,
                'product_id' => $item->product_id,
                'product_name' => $item->product_name,
                'quantity' => $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'additions_price' => (float) $item->additions_price,
                'total_price' => (float) $item->total_price,
                'status' => $item->status,
                'status_label' => $item->status_label,
                'status_color' => $item->status_color,
                'notes' => $item->notes,
                'can_be_cancelled' => $item->canBeCancelled(),
                'customizations' => $item->ingredientCustomizations->map(fn ($c) => [
                    'ingredient_name' => $c->ingredient_name,
                    'action' => $c->action,
                    'action_label' => $c->action_label,
                    'price' => (float) $c->price,
                    'display_text' => $c->display_text,
                ]),
            ]),
            'payments' => $order->payments->map(fn (OrderPayment $payment) => [
                'id' => $payment->id,
                'uuid' => $payment->uuid,
                'method' => $payment->method,
                'method_label' => $payment->method_label,
                'amount' => (float) $payment->amount,
                'received_by_name' => $payment->receivedByUser?->name,
                'notes' => $payment->notes,
                'paid_at' => $payment->paid_at->format('d/m/Y H:i'),
            ]),
        ]);
    }

    /**
     * Update order
     */
    public function update(Request $request, Order $order): JsonResponse
    {
        $data = $request->validate([
            'customer_name' => ['nullable', 'string', 'max:100'],
            'customer_phone' => ['nullable', 'string', 'max:20'],
            'delivery_address' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $order = $this->orderService->update($order, $data);

        return response()->json([
            'message' => 'Pedido atualizado com sucesso.',
        ]);
    }

    /**
     * Add item to order
     */
    public function addItem(AddOrderItemRequest $request, Order $order): JsonResponse
    {
        if (! $order->canAddItems()) {
            return response()->json([
                'message' => 'Não é possível adicionar itens a este pedido.',
            ], 422);
        }

        $item = $this->orderService->addItem($order, $request->validated());

        return response()->json([
            'message' => 'Item adicionado com sucesso.',
            'item' => [
                'id' => $item->id,
                'uuid' => $item->uuid,
                'product_name' => $item->product_name,
                'quantity' => $item->quantity,
                'total_price' => (float) $item->total_price,
            ],
        ], 201);
    }

    /**
     * Update item
     */
    public function updateItem(UpdateOrderItemRequest $request, Order $order, OrderItem $item): JsonResponse
    {
        if ($item->order_id !== $order->id) {
            return response()->json(['message' => 'Item não pertence a este pedido.'], 404);
        }

        $item = $this->orderService->updateItem($item, $request->validated());

        return response()->json([
            'message' => 'Item atualizado com sucesso.',
            'item' => [
                'id' => $item->id,
                'quantity' => $item->quantity,
                'total_price' => (float) $item->total_price,
            ],
        ]);
    }

    /**
     * Update item ingredients (customizations)
     */
    public function updateItemIngredients(Request $request, Order $order, OrderItem $item): JsonResponse
    {
        if ($item->order_id !== $order->id) {
            return response()->json(['message' => 'Item não pertence a este pedido.'], 404);
        }

        if (! $order->isOpen()) {
            return response()->json([
                'message' => 'Não é possível editar itens de um pedido fechado.',
            ], 422);
        }

        if ($item->status === 'cancelled') {
            return response()->json([
                'message' => 'Este item foi cancelado e não pode ser editado.',
            ], 422);
        }

        $data = $request->validate([
            'removed_ingredients' => ['nullable', 'array'],
            'removed_ingredients.*.id' => ['required', 'integer'],
            'removed_ingredients.*.name' => ['required', 'string'],
            'added_ingredients' => ['nullable', 'array'],
            'added_ingredients.*.id' => ['required', 'integer'],
            'added_ingredients.*.name' => ['required', 'string'],
            'added_ingredients.*.price' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $item = $this->orderService->updateItemIngredients($item, $data);

        return response()->json([
            'message' => 'Item atualizado com sucesso.',
            'item' => [
                'id' => $item->id,
                'uuid' => $item->uuid,
                'additions_price' => (float) $item->additions_price,
                'total_price' => (float) $item->total_price,
                'notes' => $item->notes,
                'customizations' => $item->ingredientCustomizations->map(fn ($c) => [
                    'ingredient_name' => $c->ingredient_name,
                    'action' => $c->action,
                    'display_text' => $c->display_text,
                ]),
            ],
        ]);
    }

    /**
     * Cancel item
     */
    public function cancelItem(Request $request, Order $order, OrderItem $item): JsonResponse
    {
        if ($item->order_id !== $order->id) {
            return response()->json(['message' => 'Item não pertence a este pedido.'], 404);
        }

        if (! $item->canBeCancelled()) {
            return response()->json([
                'message' => 'Este item não pode ser cancelado.',
            ], 422);
        }

        $item = $this->orderService->cancelItem($item, $request->input('reason'));

        return response()->json([
            'message' => 'Item cancelado com sucesso.',
        ]);
    }

    /**
     * Send items to kitchen
     */
    public function sendToKitchen(Request $request, Order $order): JsonResponse
    {
        $itemIds = $request->input('item_ids');
        $count = $this->orderService->sendToKitchen($order, $itemIds);

        return response()->json([
            'message' => $count > 0
                ? "{$count} item(ns) enviado(s) para a cozinha."
                : 'Nenhum item pendente para enviar.',
            'count' => $count,
        ]);
    }

    /**
     * Deliver multiple items at once
     */
    public function deliverItems(Request $request, Order $order): JsonResponse
    {
        $itemIds = $request->input('item_ids');
        $count = $this->orderService->deliverItems($order, $itemIds);

        return response()->json([
            'message' => $count > 0
                ? "{$count} item(ns) entregue(s)."
                : 'Nenhum item pronto para entregar.',
            'count' => $count,
        ]);
    }

    /**
     * Mark item as ready
     */
    public function markItemReady(Order $order, OrderItem $item): JsonResponse
    {
        if ($item->order_id !== $order->id) {
            return response()->json(['message' => 'Item não pertence a este pedido.'], 404);
        }

        $item = $this->orderService->markItemReady($item);

        return response()->json([
            'message' => 'Item marcado como pronto.',
        ]);
    }

    /**
     * Mark item as delivered
     */
    public function markItemDelivered(Order $order, OrderItem $item): JsonResponse
    {
        if ($item->order_id !== $order->id) {
            return response()->json(['message' => 'Item não pertence a este pedido.'], 404);
        }

        $item = $this->orderService->markItemDelivered($item);

        return response()->json([
            'message' => 'Item marcado como entregue.',
        ]);
    }

    /**
     * Add payment
     */
    public function addPayment(AddPaymentRequest $request, Order $order): JsonResponse
    {
        if (! $order->isOpen()) {
            return response()->json([
                'message' => 'Não é possível adicionar pagamento a este pedido.',
            ], 422);
        }

        $payment = $this->orderService->addPayment($order, $request->validated());

        return response()->json([
            'message' => 'Pagamento registrado com sucesso.',
            'payment' => [
                'id' => $payment->id,
                'uuid' => $payment->uuid,
                'method' => $payment->method,
                'method_label' => $payment->method_label,
                'amount' => (float) $payment->amount,
                'paid_at' => $payment->paid_at->format('d/m/Y H:i'),
            ],
            'order' => [
                'total_paid' => $order->fresh()->total_paid,
                'remaining_amount' => $order->fresh()->remaining_amount,
                'is_fully_paid' => $order->fresh()->is_fully_paid,
            ],
        ], 201);
    }

    /**
     * Remove payment
     */
    public function removePayment(Order $order, OrderPayment $payment): JsonResponse
    {
        if ($payment->order_id !== $order->id) {
            return response()->json(['message' => 'Pagamento não pertence a este pedido.'], 404);
        }

        if (! $order->isOpen()) {
            return response()->json([
                'message' => 'Não é possível remover pagamento deste pedido.',
            ], 422);
        }

        $this->orderService->removePayment($payment);

        return response()->json([
            'message' => 'Pagamento removido com sucesso.',
            'order' => [
                'total_paid' => $order->fresh()->total_paid,
                'remaining_amount' => $order->fresh()->remaining_amount,
                'is_fully_paid' => $order->fresh()->is_fully_paid,
            ],
        ]);
    }

    /**
     * Apply discount
     */
    public function applyDiscount(Request $request, Order $order): JsonResponse
    {
        $request->validate([
            'discount' => ['required', 'numeric', 'min:0', 'max:'.$order->subtotal],
        ]);

        $order = $this->orderService->applyDiscount($order, $request->input('discount'));

        return response()->json([
            'message' => 'Desconto aplicado com sucesso.',
            'order' => [
                'subtotal' => (float) $order->subtotal,
                'discount' => (float) $order->discount,
                'service_fee' => (float) $order->service_fee,
                'total' => (float) $order->total,
                'remaining_amount' => $order->remaining_amount,
                'is_fully_paid' => $order->is_fully_paid,
            ],
        ]);
    }

    /**
     * Toggle service fee (10%)
     */
    public function toggleServiceFee(Order $order): JsonResponse
    {
        if (! $order->isOpen()) {
            return response()->json([
                'message' => 'Não é possível alterar a taxa de serviço deste pedido.',
            ], 422);
        }

        $order = $this->orderService->toggleServiceFee($order);

        return response()->json([
            'message' => $order->hasServiceFee()
                ? 'Taxa de serviço aplicada.'
                : 'Taxa de serviço removida.',
            'order' => [
                'subtotal' => (float) $order->subtotal,
                'discount' => (float) $order->discount,
                'service_fee' => (float) $order->service_fee,
                'total' => (float) $order->total,
                'remaining_amount' => $order->remaining_amount,
                'is_fully_paid' => $order->is_fully_paid,
            ],
        ]);
    }

    /**
     * Close order
     */
    public function close(Order $order): JsonResponse
    {
        if (! $order->is_fully_paid) {
            return response()->json([
                'message' => 'O pedido ainda possui valores pendentes.',
            ], 422);
        }

        $order = $this->orderService->close($order);

        return response()->json([
            'message' => 'Pedido fechado com sucesso.',
        ]);
    }

    /**
     * Cancel order
     */
    public function cancel(Request $request, Order $order): JsonResponse
    {
        if (! $order->isOpen()) {
            return response()->json([
                'message' => 'Apenas pedidos abertos podem ser cancelados.',
            ], 422);
        }

        $order = $this->orderService->cancel($order, $request->input('reason'));

        return response()->json([
            'message' => 'Pedido cancelado com sucesso.',
        ]);
    }

    /**
     * Transfer order to another table
     */
    public function transfer(Request $request, Order $order): JsonResponse
    {
        $request->validate([
            'table_id' => ['required', 'exists:tables,id'],
        ]);

        $newTable = Table::findOrFail($request->input('table_id'));

        // Check if new table has an open order
        $existingOrder = $newTable->orders()->where('status', 'open')->exists();
        if ($existingOrder) {
            return response()->json([
                'message' => 'A mesa de destino já possui um pedido aberto.',
            ], 422);
        }

        $order = $this->orderService->transferToTable($order, $newTable);

        return response()->json([
            'message' => 'Pedido transferido com sucesso.',
        ]);
    }

    /**
     * Get order history
     */
    public function history(Request $request, Order $order): JsonResponse
    {
        $perPage = $request->input('per_page', 10);
        $histories = $order->histories()->with('user')->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'data' => $histories->getCollection()->map(fn ($history) => [
                'id' => $history->id,
                'event' => $history->event,
                'event_label' => $history->event_label,
                'field' => $history->field,
                'field_label' => $history->field_label,
                'old_value' => $history->formatted_old_value,
                'new_value' => $history->formatted_new_value,
                'description' => $history->description,
                'user_name' => $history->user?->name ?? 'Sistema',
                'created_at' => $history->created_at->format('d/m/Y H:i'),
            ]),
            'current_page' => $histories->currentPage(),
            'last_page' => $histories->lastPage(),
            'per_page' => $histories->perPage(),
            'total' => $histories->total(),
        ]);
    }

    /**
     * Print receipt
     */
    public function receipt(Order $order): View
    {
        $order->load(['table', 'user', 'items.ingredientCustomizations', 'payments']);

        return view('orders.receipt', compact('order'));
    }
}
