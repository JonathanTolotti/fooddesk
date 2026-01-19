<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return User::orderBy('name')->paginate($perPage);
    }

    public function filter(array $filters, int $perPage = 10, int $page = 1): LengthAwarePaginator
    {
        $query = User::query();

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['role'])) {
            $query->where('role', $filters['role']);
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $status = $filters['status'] === 'active';
            $query->where('status', $status);
        }

        return $query->orderBy('users.id')->paginate($perPage, ['*'], 'page', $page);
    }

    public function create(array $data): User
    {
        $data['password'] = Hash::make($data['password']);

        return User::create($data);
    }

    public function update(User $user, array $data): User
    {
        if (isset($data['password']) && $data['password']) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return $user;
    }

    public function delete(User $user): bool
    {
        return $user->delete();
    }

    public function toggleStatus(User $user): User
    {
        $user->update(['status' => ! $user->status]);

        return $user;
    }
}
