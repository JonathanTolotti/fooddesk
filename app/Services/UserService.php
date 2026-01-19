<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return User::where('status', true)->orderBy('name')->paginate($perPage);
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
