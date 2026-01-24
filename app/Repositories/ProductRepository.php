<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository implements ProductRepositoryInterface
{
    public function all(): Collection
    {
        return Product::with(['category', 'ingredients'])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public function filter(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = Product::with(['category', 'ingredients']);

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $isActive = $filters['status'] === 'active';
            $query->where('is_active', $isActive);
        }

        return $query->orderBy('sort_order')->orderBy('name')->paginate($perPage);
    }

    public function create(array $data): Product
    {
        if (! isset($data['sort_order'])) {
            $data['sort_order'] = $this->getNextSortOrder($data['category_id'] ?? null);
        }

        return Product::create($data);
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);

        return $product;
    }

    public function delete(Product $product): bool
    {
        return $product->delete();
    }

    public function getNextSortOrder(?int $categoryId = null): int
    {
        $query = Product::query();

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        return $query->max('sort_order') + 1;
    }

    public function reorder(array $orderedIds): void
    {
        foreach ($orderedIds as $index => $id) {
            Product::where('id', $id)->update(['sort_order' => $index + 1]);
        }
    }
}
