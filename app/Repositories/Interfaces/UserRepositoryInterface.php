<?php

namespace App\Repositories\Interfaces;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function findByUuid(string $uuid): ?User;

    public function findByEmail(string $email): ?User;

    public function findByLogin(string $login): ?User;

    public function create(array $data): User;

    public function update(User $user, array $data): User;

    public function delete(User $user): bool;

    public function restore(User $user): bool;

    public function filterByRole(?UserRole $role, int $perPage = 15): LengthAwarePaginator;

}
