<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserHistory extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'target_user_id',
        'event',
        'field',
        'old_value',
        'new_value',
        'user_id',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function targetUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFieldLabelAttribute(): string
    {
        return match ($this->field) {
            'name' => 'Nome',
            'login' => 'Login',
            'email' => 'E-mail',
            'role' => 'Perfil',
            'status' => 'Status',
            default => $this->field ?? '-',
        };
    }

    public function getFormattedOldValueAttribute(): string
    {
        return $this->formatValue($this->field, $this->old_value);
    }

    public function getFormattedNewValueAttribute(): string
    {
        return $this->formatValue($this->field, $this->new_value);
    }

    private function formatValue(?string $field, ?string $value): string
    {
        if ($value === null) {
            return '-';
        }

        if ($field === 'status') {
            return $value ? 'Ativo' : 'Inativo';
        }

        if ($field === 'role') {
            $role = UserRole::tryFrom($value);

            return $role?->label() ?? $value;
        }

        return $value;
    }
}
