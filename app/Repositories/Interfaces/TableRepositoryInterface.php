<?php

namespace App\Repositories\Interfaces;

use App\Models\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface TableRepositoryInterface
{
    public function all(): Collection;

    public function allActive(): Collection;

    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function filter(array $filters, int $perPage = 10): LengthAwarePaginator;

    public function findByUuid(string $uuid): ?Table;

    public function create(array $data): Table;

    public function update(Table $table, array $data): Table;

    public function delete(Table $table): bool;

    public function getNextNumber(): int;
}
