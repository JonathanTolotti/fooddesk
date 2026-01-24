<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Models\Product;
use App\Models\ProductHistory;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    private const TYPE_LABELS = [
        'base' => 'Base',
        'standard' => 'Padrão',
        'additional' => 'Adicional',
    ];

    public function __construct(
        private readonly ProductRepositoryInterface $productRepository
    ) {}

    public function all(): Collection
    {
        return $this->productRepository->all();
    }

    public function filter(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        return $this->productRepository->filter($filters, $perPage);
    }

    public function create(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            if (isset($data['image']) && $data['image']) {
                $data['image'] = $this->uploadImage($data['image']);
            }

            $ingredients = $data['ingredients'] ?? [];
            unset($data['ingredients']);

            $product = $this->productRepository->create($data);

            if (! empty($ingredients)) {
                $this->syncIngredients($product, $ingredients);
                $this->auditIngredientChanges($product, [], $ingredients, 'created');
            }

            return $product->load('ingredients');
        });
    }

    public function update(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            if (isset($data['image']) && $data['image']) {
                // Delete old image
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
                $data['image'] = $this->uploadImage($data['image']);
            } elseif (isset($data['remove_image']) && $data['remove_image']) {
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
                $data['image'] = null;
            } else {
                unset($data['image']);
            }

            unset($data['remove_image']);

            $newIngredients = $data['ingredients'] ?? null;
            unset($data['ingredients']);

            $product = $this->productRepository->update($product, $data);

            if ($newIngredients !== null) {
                $oldIngredients = $this->getCurrentIngredients($product);
                $this->syncIngredients($product, $newIngredients);
                $this->auditIngredientChanges($product, $oldIngredients, $newIngredients, 'updated');
            }

            return $product->load('ingredients');
        });
    }

    public function delete(Product $product): bool
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        return $this->productRepository->delete($product);
    }

    public function toggleStatus(Product $product): Product
    {
        return $this->productRepository->update($product, [
            'is_active' => ! $product->is_active,
        ]);
    }

    public function reorder(array $orderedIds): void
    {
        $this->productRepository->reorder($orderedIds);
    }

    private function uploadImage($image): string
    {
        return $image->store('products', 'public');
    }

    private function syncIngredients(Product $product, array $ingredients): void
    {
        $syncData = [];

        foreach ($ingredients as $ingredient) {
            $syncData[$ingredient['id']] = [
                'type' => $ingredient['type'] ?? 'standard',
                'quantity' => $ingredient['quantity'] ?? null,
                'additional_price' => $ingredient['type'] === 'additional'
                    ? ($ingredient['additional_price'] ?? null)
                    : null,
            ];
        }

        $product->ingredients()->sync($syncData);
    }

    private function getCurrentIngredients(Product $product): array
    {
        return $product->ingredients->map(fn ($ingredient) => [
            'id' => $ingredient->id,
            'name' => $ingredient->name,
            'type' => $ingredient->pivot->type,
            'quantity' => $ingredient->pivot->quantity,
            'additional_price' => $ingredient->pivot->additional_price,
        ])->toArray();
    }

    private function auditIngredientChanges(Product $product, array $oldIngredients, array $newIngredients, string $event): void
    {
        $userId = Auth::id();
        $now = now();

        // Index old ingredients by id
        $oldById = [];
        foreach ($oldIngredients as $old) {
            $oldById[$old['id']] = $old;
        }

        // Index new ingredients by id and get names
        $newById = [];
        $ingredientIds = array_column($newIngredients, 'id');
        $ingredientNames = Ingredient::whereIn('id', $ingredientIds)->pluck('name', 'id')->toArray();

        foreach ($newIngredients as $new) {
            $newById[$new['id']] = array_merge($new, [
                'name' => $ingredientNames[$new['id']] ?? 'Ingrediente #'.$new['id'],
            ]);
        }

        $changes = [];

        // Check for added ingredients
        foreach ($newById as $id => $new) {
            if (! isset($oldById[$id])) {
                $typeLabel = self::TYPE_LABELS[$new['type']] ?? $new['type'];
                $priceInfo = $new['type'] === 'additional' && ! empty($new['additional_price'])
                    ? ', R$ '.number_format((float) $new['additional_price'], 2, ',', '.')
                    : '';

                $changes[] = [
                    'product_id' => $product->id,
                    'event' => $event,
                    'field' => 'ingredients',
                    'old_value' => null,
                    'new_value' => "Adicionado: {$new['name']} ({$typeLabel}{$priceInfo})",
                    'user_id' => $userId,
                    'created_at' => $now,
                ];
            }
        }

        // Check for removed ingredients
        foreach ($oldById as $id => $old) {
            if (! isset($newById[$id])) {
                $changes[] = [
                    'product_id' => $product->id,
                    'event' => $event,
                    'field' => 'ingredients',
                    'old_value' => "Removido: {$old['name']}",
                    'new_value' => null,
                    'user_id' => $userId,
                    'created_at' => $now,
                ];
            }
        }

        // Check for modified ingredients
        foreach ($newById as $id => $new) {
            if (isset($oldById[$id])) {
                $old = $oldById[$id];

                // Type changed
                if ($old['type'] !== $new['type']) {
                    $oldTypeLabel = self::TYPE_LABELS[$old['type']] ?? $old['type'];
                    $newTypeLabel = self::TYPE_LABELS[$new['type']] ?? $new['type'];

                    $changes[] = [
                        'product_id' => $product->id,
                        'event' => $event,
                        'field' => 'ingredients',
                        'old_value' => "{$new['name']}: {$oldTypeLabel}",
                        'new_value' => "{$new['name']}: {$newTypeLabel}",
                        'user_id' => $userId,
                        'created_at' => $now,
                    ];
                }

                // Additional price changed (only for additional type)
                $oldPrice = (float) ($old['additional_price'] ?? 0);
                $newPrice = (float) ($new['additional_price'] ?? 0);

                if ($new['type'] === 'additional' && $oldPrice !== $newPrice) {
                    $oldPriceFormatted = $oldPrice > 0
                        ? 'R$ '.number_format($oldPrice, 2, ',', '.')
                        : 'Sem preço';
                    $newPriceFormatted = $newPrice > 0
                        ? 'R$ '.number_format($newPrice, 2, ',', '.')
                        : 'Sem preço';

                    $changes[] = [
                        'product_id' => $product->id,
                        'event' => $event,
                        'field' => 'ingredients',
                        'old_value' => "{$new['name']}: {$oldPriceFormatted}",
                        'new_value' => "{$new['name']}: {$newPriceFormatted}",
                        'user_id' => $userId,
                        'created_at' => $now,
                    ];
                }
            }
        }

        if (! empty($changes)) {
            ProductHistory::insert($changes);
        }
    }
}
