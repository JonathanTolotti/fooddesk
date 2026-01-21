<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CategoryHistory extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'category_id',
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFieldLabelAttribute(): string
    {
        return match ($this->field) {
            'name' => 'Nome',
            'description' => 'Descrição',
            'is_active' => 'Status',
            'sort_order' => 'Ordem',
            default => $this->field ?? '-',
        };
    }

    public function getFormattedOldValueAttribute(): string
    {
        if ($this->field === 'is_active') {
            return $this->old_value ? 'Ativo' : 'Inativo';
        }

        return $this->old_value ?? '-';
    }

    public function getFormattedNewValueAttribute(): string
    {
        if ($this->field === 'is_active') {
            return $this->new_value ? 'Ativo' : 'Inativo';
        }

        return $this->new_value ?? '-';
    }
}