<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Table;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CustomerMenuController extends Controller
{
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
            $customer = Customer::find($customerId);
            if ($customer) {
                // If there's an existing order, associate customer
                if ($order && !$order->customer_id) {
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
    public function searchCustomer(Request $request, string $tableUuid): JsonResponse
    {
        $table = Table::where('uuid', $tableUuid)
            ->where('is_active', true)
            ->firstOrFail();

        $phone = preg_replace('/\D/', '', $request->input('phone', ''));

        if (strlen($phone) < 10) {
            return response()->json(['found' => false, 'message' => 'Telefone inválido']);
        }

        $customer = Customer::where('phone', $phone)->first();

        if ($customer) {
            // Save to session
            session(["customer_{$tableUuid}" => $customer->id]);

            return response()->json([
                'found' => true,
                'customer' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'phone' => $customer->formatted_phone,
                    'birth_date' => $customer->birth_date?->format('d/m/Y'),
                    'is_birthday' => $customer->is_birthday,
                ],
            ]);
        }

        return response()->json(['found' => false]);
    }

    /**
     * Register new customer
     */
    public function registerCustomer(Request $request, string $tableUuid): JsonResponse
    {
        $table = Table::where('uuid', $tableUuid)
            ->where('is_active', true)
            ->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'birth_date' => 'nullable|date',
        ], [
            'name.required' => 'Informe seu nome.',
            'phone.required' => 'Informe seu telefone.',
        ]);

        $phone = preg_replace('/\D/', '', $validated['phone']);

        // Check if phone already exists
        $existing = Customer::where('phone', $phone)->first();
        if ($existing) {
            session(["customer_{$tableUuid}" => $existing->id]);
            return response()->json([
                'success' => true,
                'customer' => [
                    'id' => $existing->id,
                    'name' => $existing->name,
                ],
            ]);
        }

        $customer = Customer::create([
            'name' => $validated['name'],
            'phone' => $phone,
            'birth_date' => $validated['birth_date'] ?? null,
        ]);

        // Save to session
        session(["customer_{$tableUuid}" => $customer->id]);

        return response()->json([
            'success' => true,
            'customer' => [
                'id' => $customer->id,
                'name' => $customer->name,
            ],
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
        if (!$customer && $order && $order->customer_id) {
            $customer = $order->customer;
        }

        $orderData = $order ? $this->formatOrder($order) : null;
        $customerData = $customer ? [
            'id' => $customer->id,
            'name' => $customer->name,
            'phone' => $customer->formatted_phone,
            'is_birthday' => $customer->is_birthday,
        ] : null;

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

        if (!$order) {
            return response()->json(['order' => null]);
        }

        return response()->json([
            'order' => $this->formatOrder($order),
        ]);
    }

    /**
     * Add item to order
     */
    public function addItem(Request $request, string $tableUuid): JsonResponse
    {
        $table = Table::where('uuid', $tableUuid)
            ->where('is_active', true)
            ->firstOrFail();

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:99',
            'notes' => 'nullable|string|max:500',
            'removed_ingredients' => 'nullable|array',
            'removed_ingredients.*' => 'exists:ingredients,id',
            'added_ingredients' => 'nullable|array',
            'added_ingredients.*' => 'exists:ingredients,id',
        ]);

        $product = Product::with('ingredients')->findOrFail($validated['product_id']);

        // Get customer from session
        $customerId = session("customer_{$tableUuid}");
        $customer = $customerId ? Customer::find($customerId) : null;

        return DB::transaction(function () use ($table, $product, $validated, $customer) {
            // Get or create order
            $order = $table->currentOrder();

            if (!$order) {
                $order = Order::create([
                    'table_id' => $table->id,
                    'customer_id' => $customer?->id,
                    'type' => 'dine_in',
                    'status' => 'open',
                    'user_id' => null, // Customer order, no user
                    'customer_name' => $customer?->name,
                ]);

                // Update table status
                $table->update(['status' => 'occupied']);
            } elseif (!$order->customer_id && $customer) {
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
                    $item->ingredients()->create([
                        'ingredient_id' => $ingredientId,
                        'ingredient_name' => $ingredient->name,
                        'action' => 'removed',
                        'additional_price' => 0,
                    ]);
                }
            }

            // Save added ingredients
            foreach ($addedIngredients as $ingredientId) {
                $ingredient = $product->ingredients->find($ingredientId);
                if ($ingredient && $ingredient->pivot->type === 'additional') {
                    $item->ingredients()->create([
                        'ingredient_id' => $ingredientId,
                        'ingredient_name' => $ingredient->name,
                        'action' => 'added',
                        'additional_price' => $ingredient->pivot->additional_price ?? 0,
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

        if (!$order) {
            return response()->json(['message' => 'Pedido não encontrado.'], 404);
        }

        $item = $order->items()->where('id', $itemId)->first();

        if (!$item) {
            return response()->json(['message' => 'Item não encontrado.'], 404);
        }

        // Only allow removing pending items
        if ($item->status !== 'pending') {
            return response()->json([
                'message' => 'Não é possível remover itens que já foram enviados para a cozinha.',
            ], 422);
        }

        $item->ingredients()->delete();
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

        if (!$order) {
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
        $order->load(['items.ingredients']);

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
                    'customizations' => $item->ingredients->map(function ($ing) {
                        return [
                            'name' => $ing->ingredient_name,
                            'action' => $ing->action,
                            'price' => $ing->additional_price,
                        ];
                    }),
                ];
            }),
            'items_count' => $order->items->count(),
            'pending_count' => $order->items->where('status', 'pending')->count(),
        ];
    }
}
