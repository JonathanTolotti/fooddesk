<?php

namespace App\Observers;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\UserHistory;
use Illuminate\Support\Facades\Auth;

class UserObserver
{
    protected array $auditableFields = [
        'name',
        'login',
        'email',
        'role',
        'status',
    ];

    public function created(User $user): void
    {
        UserHistory::create([
            'target_user_id' => $user->id,
            'event' => 'created',
            'field' => null,
            'old_value' => null,
            'new_value' => null,
            'user_id' => Auth::id(),
            'created_at' => now(),
        ]);
    }

    public function updated(User $user): void
    {
        $changes = $user->getChanges();

        foreach ($this->auditableFields as $field) {
            if (array_key_exists($field, $changes)) {
                $oldValue = $user->getOriginal($field);
                $newValue = $changes[$field];

                // Converte boolean para string para armazenamento
                if (is_bool($oldValue)) {
                    $oldValue = $oldValue ? '1' : '0';
                }
                if (is_bool($newValue)) {
                    $newValue = $newValue ? '1' : '0';
                }

                // Converte enum para string
                if ($oldValue instanceof UserRole) {
                    $oldValue = $oldValue->value;
                }
                if ($newValue instanceof UserRole) {
                    $newValue = $newValue->value;
                }

                UserHistory::create([
                    'target_user_id' => $user->id,
                    'event' => 'updated',
                    'field' => $field,
                    'old_value' => $oldValue,
                    'new_value' => $newValue,
                    'user_id' => Auth::id(),
                    'created_at' => now(),
                ]);
            }
        }
    }

    public function deleted(User $user): void
    {
        UserHistory::create([
            'target_user_id' => $user->id,
            'event' => 'deleted',
            'field' => null,
            'old_value' => null,
            'new_value' => null,
            'user_id' => Auth::id(),
            'created_at' => now(),
        ]);
    }
}
