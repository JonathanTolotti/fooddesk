<?php

namespace App\Repositories;

use App\Models\Category;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function __construct(
        private readonly Category $model
    ) {}

    public function all(): Collection
    {
        return $this->model
            ->orderBy('sort_order')
            ->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->orderBy('sort_order')
            ->paginate($perPage);
    }

    public function filter(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = $this->model->query();

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $isActive = $filters['status'] === 'active';
            $query->where('is_active', $isActive);
        }

        return $query->orderBy('sort_order')->paginate($perPage);
    }

    public function findByUuid(string $uuid): ?Category
    {
        return $this->model->where('uuid', $uuid)->first();
    }

    public function create(array $data): Category
    {
        return $this->model->create($data);
    }

    public function update(Category $category, array $data): Category
    {
        $category->update($data);

        return $category->fresh();
    }

    public function delete(Category $category): bool
    {
        return $category->delete();
    }

    public function getNextSortOrder(): int
    {
        return ($this->model->max('sort_order') ?? 0) + 1;
    }

    public function reorder(array $orderedIds): void
    {
        foreach ($orderedIds as $index => $id) {
            $this->model->where('id', $id)->update(['sort_order' => $index + 1]);
        }
    }
}
