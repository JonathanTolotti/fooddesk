<?php

namespace App\Services;

use App\Models\Table;
use App\Repositories\Interfaces\TableRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class TableService
{
    public function __construct(
        private readonly TableRepositoryInterface $repository
    ) {}

    public function all(): Collection
    {
        return $this->repository->all();
    }

    public function allActive(): Collection
    {
        return $this->repository->allActive();
    }

    public function filter(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        return $this->repository->filter($filters, $perPage);
    }

    public function findByUuid(string $uuid): ?Table
    {
        return $this->repository->findByUuid($uuid);
    }

    public function create(array $data): Table
    {
        if (empty($data['number'])) {
            $data['number'] = $this->repository->getNextNumber();
        }

        return $this->repository->create($data);
    }

    public function update(Table $table, array $data): Table
    {
        return $this->repository->update($table, $data);
    }

    public function delete(Table $table): bool
    {
        return $this->repository->delete($table);
    }

    public function toggleStatus(Table $table): Table
    {
        return $this->repository->update($table, [
            'is_active' => ! $table->is_active,
        ]);
    }

    public function changeTableStatus(Table $table, string $status): Table
    {
        return $this->repository->update($table, [
            'status' => $status,
        ]);
    }
}
