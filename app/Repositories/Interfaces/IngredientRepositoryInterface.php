<?php

namespace App\Repositories\Interfaces;

use App\Models\Ingredient;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface IngredientRepositoryInterface
{
    public function all(): Collection;

    public function filter(array $filters, int $perPage = 10): LengthAwarePaginator;

    public function create(array $data): Ingredient;

    public function update(Ingredient $ingredient, array $data): Ingredient;

    public function delete(Ingredient $ingredient): bool;
}
