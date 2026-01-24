<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Repositories\Interfaces\IngredientRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class IngredientService
{
    public function __construct(
        private readonly IngredientRepositoryInterface $ingredientRepository
    ) {}

    public function all(): Collection
    {
        return $this->ingredientRepository->all();
    }

    public function filter(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        return $this->ingredientRepository->filter($filters, $perPage);
    }

    public function create(array $data): Ingredient
    {
        return $this->ingredientRepository->create($data);
    }

    public function update(Ingredient $ingredient, array $data): Ingredient
    {
        return $this->ingredientRepository->update($ingredient, $data);
    }

    public function delete(Ingredient $ingredient): bool
    {
        return $this->ingredientRepository->delete($ingredient);
    }

    public function toggleStatus(Ingredient $ingredient): Ingredient
    {
        return $this->ingredientRepository->update($ingredient, [
            'is_active' => ! $ingredient->is_active,
        ]);
    }
}
