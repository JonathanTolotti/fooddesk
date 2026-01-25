<?php

namespace App\Repositories\Interfaces;

use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface OrderRepositoryInterface
{
    public function all(): Collection;

    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function filter(array $filters, int $perPage = 10): LengthAwarePaginator;

    public function findByUuid(string $uuid): ?Order;

    public function findOpenByTable(int $tableId): ?Order;

    public function create(array $data): Order;

    public function update(Order $order, array $data): Order;

    public function delete(Order $order): bool;

    public function getOpenOrders(): Collection;

    public function getOrdersByStatus(string $status): Collection;

    public function getTodayOrders(): Collection;
}
