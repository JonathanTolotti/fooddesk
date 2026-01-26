<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCustomerOrderItemRequest;
use App\Http\Requests\SearchCustomerRequest;
use App\Http\Requests\StoreCustomerRequest;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Table;
use App\Services\CustomerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CustomerMenuController extends Controller
{
    public function __construct(
        private CustomerService $customerService
    ) {}

    /**
     * Display the customer identification page or menu
     */
    public function index(Request $request, string $tableUuid): View
    {
        $table = Table::where('uuid', $tableUuid)
            ->where('is_active', true)
            ->firstOrFail();

        // Check if table has an open order with a customer
        $order = $table->currentOrder();

        // If order exists and has a customer, show menu directly
        if ($order && $order->customer_id) {
            return $this->showMenu($table, $order);
        }

        // Check session for customer identification
        $customerId = session("customer_{$tableUuid}");
        if ($customerId) {
            $customer = $this->customerService->findById($customerId);
            if ($customer) {
                // If there's an existing order, associate customer
                if ($order && ! $order->customer_id) {
                    $order->update(['customer_id' => $customer->id, 'customer_name' => $customer->name]);
                }

                return $this->showMenu($table, $order, $customer);
            }
        }

        // Show identification page
        return view('customer.identify', compact('table'));
    }

    /**
     * Search customer by phone
     */
    public function searchCustomer(SearchCustomerRequest $request, string $tableUuid): JsonResponse
    {
        $table = Table::where('uuid', $tableUuid)
            ->where('is_active', true)
            ->firstOrFail();

        $customer = $this->customerService->searchByPhone($request->validated('phone'));

        if ($customer) {
            // Save to session
            session(["customer_{$tableUuid}" => $customer['id']]);

            return response()->json([
                'found' => true,
                'customer' => $customer,
            ]);
        }

        return response()->json(['found' => false]);
    }

    /**
     * Register new customer
     */
    public function registerCustomer(StoreCustomerRequest $request, string $tableUuid): JsonResponse
    {
        $table = Table::where('uuid', $tableUuid)
            ->where('is_active', true)
            ->firstOrFail();

        $result = $this->customerService->register($request->validated());

        // Save to session
        session(["customer_{$tableUuid}" => $result['customer']['id']]);

        return response()->json([
            'success' => true,
            'customer' => $result['customer'],
        ]);
    }

    /**
     * Show the menu page
     */
    private function showMenu(Table $table, ?Order $order = null, ?Customer $customer = null): View
    {
        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->with(['products' => function ($query) {
                $query->where('is_active', true)
                    ->with(['ingredients' => function ($q) {
                        $q->where('is_active', true)
                            ->orderByRaw("FIELD(product_ingredient.type, 'base', 'standard', 'additional')");
                    }]);
            }])
            ->get();

        // Get customer from order if not provided
        if (! $customer && $order && $order->customer_id) {
            $customer = $order->customer;
        }

        $orderData = $order ? $this->formatOrder($order) : null;
        $customerData = $customer ? $this->customerService->formatCustomer($customer) : null;

        return view('customer.menu', compact('table', 'categories', 'order', 'orderData', 'customer', 'customerData'));
    }

    /**
     * Get current order data
     */
    public function getOrder(string $tableUuid): JsonResponse
    {
        $table = Table::where('uuid', $tableUuid)
            ->where('is_active', true)
            ->firstOrFail();

        $order = $table->currentOrder();

        if (! $order) {
            return response()->json(['order' => null]);
        }

        return response()->json([
            'order' => $this->formatOrder($order),
        ]);
    }

    /**
     * Add item to order
     */
    public function addItem(AddCustomerOrderItemRequest $request, string $tableUuid): JsonResponse
    {
        $table = Table::where('uuid', $tableUuid)
            ->where('is_active', true)
            ->firstOrFail();

        $validated = $request->validated();
        $product = Product::with('ingredients')->findOrFail($validated['product_id']);

        // Get customer from session
        $customerId = session("customer_{$tableUuid}");
        $customer = $customerId ? $this->customerService->findById($customerId) : null;

        return DB::transaction(function () use ($table, $product, $validated, $customer) {
            // Get or create order
            $order = $table->currentOrder();

            if (! $order) {
                $order = Order::create([
                    'table_id' => $table->id,
                    'customer_id' => $customer?->id,
                    'type' => 'dine_in',
                    'status' => 'open',
                    'user_id' => null, // Self-service order, no user
                    'customer_name' => $customer?->name,
                ]);

                // Update table status
                $table->update(['status' => 'occupied']);
            } elseif (! $order->customer_id && $customer) {
                // Associate customer if order exists but has no customer
                $order->update(['customer_id' => $customer->id, 'customer_name' => $customer->name]);
            }

            // Calculate additional price from added ingredients
            $additionalPrice = 0;
            $addedIngredients = $validated['added_ingredients'] ?? [];

            foreach ($product->ingredients as $ingredient) {
                if ($ingredient->pivot->type === 'additional' && in_array($ingredient->id, $addedIngredients)) {
                    $additionalPrice += $ingredient->pivot->additional_price ?? 0;
                }
            }

            // Create order item
            $item = OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => $validated['quantity'],
                'unit_price' => $product->price,
                'total_price' => ($product->price + $additionalPrice) * $validated['quantity'],
                'notes' => $validated['notes'] ?? null,
                'status' => 'pending',
            ]);

            // Save removed ingredients
            $removedIngredients = $validated['removed_ingredients'] ?? [];
            foreach ($removedIngredients as $ingredientId) {
                $ingredient = $product->ingredients->find($ingredientId);
                if ($ingredient && $ingredient->pivot->type === 'standard') {
                    $item->ingredientCustomizations()->create([
                        'ingredient_id' => $ingredientId,
                        'ingredient_name' => $ingredient->name,
                        'action' => 'removed',
                        'price' => 0,
                    ]);
                }
            }

            // Save added ingredients
            foreach ($addedIngredients as $ingredientId) {
                $ingredient = $product->ingredients->find($ingredientId);
                if ($ingredient && $ingredient->pivot->type === 'additional') {
                    $item->ingredientCustomizations()->create([
                        'ingredient_id' => $ingredientId,
                        'ingredient_name' => $ingredient->name,
                        'action' => 'added',
                        'price' => $ingredient->pivot->additional_price ?? 0,
                    ]);
                }
            }

            // Recalculate order totals
            $order->recalculateTotals();

            return response()->json([
                'message' => 'Item adicionado ao pedido!',
                'order' => $this->formatOrder($order->fresh()),
            ]);
        });
    }

    /**
     * Remove item from order
     */
    public function removeItem(Request $request, string $tableUuid, int $itemId): JsonResponse
    {
        $table = Table::where('uuid', $tableUuid)
            ->where('is_active', true)
            ->firstOrFail();

        $order = $table->currentOrder();

        if (! $order) {
            return response()->json(['message' => 'Pedido não encontrado.'], 404);
        }

        $item = $order->items()->where('id', $itemId)->first();

        if (! $item) {
            return response()->json(['message' => 'Item não encontrado.'], 404);
        }

        // Only allow removing pending items
        if ($item->status !== 'pending') {
            return response()->json([
                'message' => 'Não é possível remover itens que já foram enviados para a cozinha.',
            ], 422);
        }

        $item->ingredientCustomizations()->delete();
        $item->delete();

        $order->recalculateTotals();

        return response()->json([
            'message' => 'Item removido do pedido.',
            'order' => $this->formatOrder($order->fresh()),
        ]);
    }

    /**
     * Send pending items to kitchen
     */
    public function sendToKitchen(string $tableUuid): JsonResponse
    {
        $table = Table::where('uuid', $tableUuid)
            ->where('is_active', true)
            ->firstOrFail();

        $order = $table->currentOrder();

        if (! $order) {
            return response()->json(['message' => 'Pedido não encontrado.'], 404);
        }

        $count = $order->items()
            ->where('status', 'pending')
            ->update([
                'status' => 'preparing',
                'sent_to_kitchen_at' => now(),
            ]);

        if ($count === 0) {
            return response()->json(['message' => 'Nenhum item pendente para enviar.'], 422);
        }

        return response()->json([
            'message' => "{$count} item(ns) enviado(s) para a cozinha!",
            'order' => $this->formatOrder($order->fresh()),
        ]);
    }

    /**
     * Call waiter
     */
    public function callWaiter(string $tableUuid): JsonResponse
    {
        $table = Table::where('uuid', $tableUuid)
            ->where('is_active', true)
            ->firstOrFail();

        // Set flag on table or order to indicate waiter is being called
        $table->update(['calling_waiter' => true, 'called_waiter_at' => now()]);

        return response()->json([
            'message' => 'Garçom chamado! Aguarde um momento.',
        ]);
    }

    /**
     * Format order for JSON response
     */
    private function formatOrder(Order $order): array
    {
        $order->load(['items.ingredientCustomizations']);

        return [
            'id' => $order->id,
            'uuid' => $order->uuid,
            'status' => $order->status,
            'subtotal' => $order->subtotal,
            'discount' => $order->discount,
            'service_fee' => $order->service_fee,
            'total' => $order->total,
            'items' => $order->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'uuid' => $item->uuid,
                    'product_name' => $item->product_name,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total_price' => $item->total_price,
                    'notes' => $item->notes,
                    'status' => $item->status,
                    'status_label' => $item->status_label,
                    'can_remove' => $item->status === 'pending',
                    'customizations' => $item->ingredientCustomizations->map(function ($ing) {
                        return [
                            'name' => $ing->ingredient_name,
                            'action' => $ing->action,
                            'price' => $ing->price,
                        ];
                    }),
                ];
            }),
            'items_count' => $order->items->count(),
            'pending_count' => $order->items->where('status', 'pending')->count(),
        ];
    }
}
