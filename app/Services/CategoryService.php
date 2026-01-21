<?php

namespace App\Services;

use App\Models\Category;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    public function __construct(
        private readonly CategoryRepositoryInterface $repository
    ) {}

    public function all(): Collection
    {
        return $this->repository->all();
    }

    public function filter(array $filters): Collection
    {
        return $this->repository->filter($filters);
    }

    public function findByUuid(string $uuid): ?Category
    {
        return $this->repository->findByUuid($uuid);
    }

    public function create(array $data): Category
    {
        $data['sort_order'] = $this->repository->getNextSortOrder();

        return $this->repository->create($data);
    }

    public function update(Category $category, array $data): Category
    {
        return $this->repository->update($category, $data);
    }

    public function delete(Category $category): bool
    {
        return $this->repository->delete($category);
    }

    public function toggleStatus(Category $category): Category
    {
        return $this->repository->update($category, [
            'is_active' => !$category->is_active,
        ]);
    }

    public function reorder(array $orderedIds): void
    {
        $this->repository->reorder($orderedIds);
    }
}