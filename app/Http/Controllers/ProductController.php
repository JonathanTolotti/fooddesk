<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $productService
    ) {}

    public function index(): View
    {
        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'name']);

        $ingredients = Ingredient::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('products.index', compact('categories', 'ingredients'));
    }

    public function filter(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'category_id', 'status']);
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        // Set current page for paginator
        request()->merge(['page' => $page]);

        $products = $this->productService->filter($filters, $perPage);

        return response()->json([
            'products' => $products->getCollection()->map(fn (Product $product) => [
                'id' => $product->id,
                'uuid' => $product->uuid,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'price_formatted' => 'R$ '.number_format($product->price, 2, ',', '.'),
                'category_id' => $product->category_id,
                'category_name' => $product->category?->name,
                'image' => $product->image,
                'image_url' => $product->image ? asset('storage/'.$product->image) : null,
                'is_active' => $product->is_active,
                'sort_order' => $product->sort_order,
                'ingredients' => $product->ingredients->map(fn ($ingredient) => [
                    'id' => $ingredient->id,
                    'name' => $ingredient->name,
                    'type' => $ingredient->pivot->type,
                    'quantity' => $ingredient->pivot->quantity,
                    'additional_price' => $ingredient->pivot->additional_price,
                    'additional_price_formatted' => $ingredient->pivot->additional_price
                        ? 'R$ '.number_format($ingredient->pivot->additional_price, 2, ',', '.')
                        : null,
                ]),
            ]),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->productService->create($request->validated());
        $product->load('category');

        return response()->json([
            'message' => 'Produto criado com sucesso.',
            'product' => $this->formatProductResponse($product),
        ], 201);
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $product = $this->productService->update($product, $request->validated());
        $product->load('category');

        return response()->json([
            'message' => 'Produto atualizado com sucesso.',
            'product' => $this->formatProductResponse($product),
        ]);
    }

    public function destroy(Product $product): JsonResponse
    {
        $this->productService->delete($product);

        return response()->json([
            'message' => 'Produto excluído com sucesso.',
        ]);
    }

    public function toggleStatus(Product $product): JsonResponse
    {
        $product = $this->productService->toggleStatus($product);

        $status = $product->is_active ? 'ativado' : 'inativado';

        return response()->json([
            'message' => "Produto {$status} com sucesso.",
            'is_active' => $product->is_active,
        ]);
    }

    public function reorder(Request $request): JsonResponse
    {
        $request->validate([
            'ordered_ids' => ['required', 'array'],
            'ordered_ids.*' => ['integer', 'exists:products,id'],
        ]);

        $this->productService->reorder($request->input('ordered_ids'));

        return response()->json([
            'message' => 'Ordem atualizada com sucesso.',
        ]);
    }

    public function history(Request $request, Product $product): JsonResponse
    {
        $perPage = $request->input('per_page', 10);
        $histories = $product->histories()->with('user')->paginate($perPage);

        return response()->json([
            'histories' => $histories->getCollection()->map(fn ($history) => [
                'id' => $history->id,
                'event' => $history->event,
                'field' => $history->field,
                'field_label' => $history->field_label,
                'old_value' => $history->formatted_old_value,
                'new_value' => $history->formatted_new_value,
                'user_name' => $history->user?->name ?? 'Sistema',
                'created_at' => $history->created_at->format('d/m/Y H:i'),
            ]),
            'pagination' => [
                'current_page' => $histories->currentPage(),
                'last_page' => $histories->lastPage(),
                'per_page' => $histories->perPage(),
                'total' => $histories->total(),
                'has_more' => $histories->hasMorePages(),
            ],
        ]);
    }

    private function formatProductResponse(Product $product): array
    {
        return [
            'id' => $product->id,
            'uuid' => $product->uuid,
            'name' => $product->name,
            'description' => $product->description,
            'price' => $product->price,
            'price_formatted' => 'R$ '.number_format($product->price, 2, ',', '.'),
            'category_id' => $product->category_id,
            'category_name' => $product->category?->name,
            'image' => $product->image,
            'image_url' => $product->image ? asset('storage/'.$product->image) : null,
            'is_active' => $product->is_active,
            'sort_order' => $product->sort_order,
            'ingredients' => $product->ingredients->map(fn ($ingredient) => [
                'id' => $ingredient->id,
                'name' => $ingredient->name,
                'type' => $ingredient->pivot->type,
                'quantity' => $ingredient->pivot->quantity,
                'additional_price' => $ingredient->pivot->additional_price,
                'additional_price_formatted' => $ingredient->pivot->additional_price
                    ? 'R$ '.number_format($ingredient->pivot->additional_price, 2, ',', '.')
                    : null,
            ]),
        ];
    }
}
