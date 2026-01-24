<?php

namespace App\Repositories;

use App\Enums\UserRole;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(
        private readonly User $model
    ) {}

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function findByUuid(string $uuid): ?User
    {
        return $this->model->where('uuid', $uuid)->first();
    }

    public function findByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }

    public function findByLogin(string $login): ?User
    {
        return $this->model->where('login', $login)->first();
    }

    public function create(array $data): User
    {
        return $this->model->create($data);
    }

    public function update(User $user, array $data): User
    {
        $user->update($data);

        return $user->fresh();
    }

    public function delete(User $user): bool
    {
        return $user->delete();
    }

    public function restore(User $user): bool
    {
        return $user->restore();
    }

    public function filterByRole(?UserRole $role, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->when($role, fn ($query) => $query->where('role', $role))
            ->orderBy('name')
            ->paginate($perPage);
    }
}
