<?php

namespace App\Repositories;

use App\Models\Order;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderRepository implements OrderRepositoryInterface
{
    public function __construct(
        private readonly Order $model
    ) {}

    public function all(): Collection
    {
        return $this->model
            ->with(['table', 'user', 'items'])
            ->orderByDesc('opened_at')
            ->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->with(['table', 'user'])
            ->orderByDesc('opened_at')
            ->paginate($perPage);
    }

    public function filter(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = $this->model->with(['table', 'user']);

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhereHas('table', function ($q) use ($search) {
                        $q->where('number', 'like', "%{$search}%");
                    });
            });
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['type']) && $filters['type'] !== '') {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['table_id']) && $filters['table_id'] !== '') {
            $query->where('table_id', $filters['table_id']);
        }

        if (isset($filters['date_from']) && $filters['date_from'] !== '') {
            $query->whereDate('opened_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to']) && $filters['date_to'] !== '') {
            $query->whereDate('opened_at', '<=', $filters['date_to']);
        }

        return $query->orderByDesc('opened_at')->paginate($perPage);
    }

    public function findByUuid(string $uuid): ?Order
    {
        return $this->model
            ->with(['table', 'user', 'items.ingredientCustomizations', 'payments'])
            ->where('uuid', $uuid)
            ->first();
    }

    public function findOpenByTable(int $tableId): ?Order
    {
        return $this->model
            ->where('table_id', $tableId)
            ->where('status', 'open')
            ->first();
    }

    public function create(array $data): Order
    {
        return $this->model->create($data);
    }

    public function update(Order $order, array $data): Order
    {
        $order->update($data);

        return $order->fresh(['table', 'user', 'items', 'payments']);
    }

    public function delete(Order $order): bool
    {
        return $order->delete();
    }

    public function getOpenOrders(): Collection
    {
        return $this->model
            ->with(['table', 'user', 'items'])
            ->where('status', 'open')
            ->orderByDesc('opened_at')
            ->get();
    }

    public function getOrdersByStatus(string $status): Collection
    {
        return $this->model
            ->with(['table', 'user'])
            ->where('status', $status)
            ->orderByDesc('opened_at')
            ->get();
    }

    public function getTodayOrders(): Collection
    {
        return $this->model
            ->with(['table', 'user'])
            ->whereDate('opened_at', today())
            ->orderByDesc('opened_at')
            ->get();
    }
}
