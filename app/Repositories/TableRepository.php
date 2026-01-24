<?php

namespace App\Repositories;

use App\Models\Table;
use App\Repositories\Interfaces\TableRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class TableRepository implements TableRepositoryInterface
{
    public function __construct(
        private readonly Table $model
    ) {}

    public function all(): Collection
    {
        return $this->model
            ->orderBy('number')
            ->get();
    }

    public function allActive(): Collection
    {
        return $this->model
            ->where('is_active', true)
            ->orderBy('number')
            ->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->orderBy('number')
            ->paginate($perPage);
    }

    public function filter(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = $this->model->query();

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('number', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            if ($filters['status'] === 'active') {
                $query->where('is_active', true);
            } elseif ($filters['status'] === 'inactive') {
                $query->where('is_active', false);
            } else {
                // Filter by table status (available, occupied, reserved, cleaning)
                $query->where('status', $filters['status']);
            }
        }

        return $query->orderBy('number')->paginate($perPage);
    }

    public function findByUuid(string $uuid): ?Table
    {
        return $this->model->where('uuid', $uuid)->first();
    }

    public function create(array $data): Table
    {
        return $this->model->create($data);
    }

    public function update(Table $table, array $data): Table
    {
        $table->update($data);

        return $table->fresh();
    }

    public function delete(Table $table): bool
    {
        return $table->delete();
    }

    public function getNextNumber(): int
    {
        return ($this->model->withTrashed()->max('number') ?? 0) + 1;
    }
}
