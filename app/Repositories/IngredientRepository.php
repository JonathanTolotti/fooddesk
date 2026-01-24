<?php

namespace App\Repositories;

use App\Models\Ingredient;
use App\Repositories\Interfaces\IngredientRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class IngredientRepository implements IngredientRepositoryInterface
{
    public function all(): Collection
    {
        return Ingredient::orderBy('name')->get();
    }

    public function filter(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = Ingredient::query();

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

        return $query->orderBy('name')->paginate($perPage);
    }

    public function create(array $data): Ingredient
    {
        return Ingredient::create($data);
    }

    public function update(Ingredient $ingredient, array $data): Ingredient
    {
        $ingredient->update($data);

        return $ingredient;
    }

    public function delete(Ingredient $ingredient): bool
    {
        return $ingredient->delete();
    }
}
