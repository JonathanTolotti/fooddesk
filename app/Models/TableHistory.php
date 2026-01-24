<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TableHistory extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'table_id',
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

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFieldLabelAttribute(): string
    {
        return match ($this->field) {
            'number' => 'Número',
            'name' => 'Nome',
            'capacity' => 'Capacidade',
            'status' => 'Status',
            'is_active' => 'Ativo',
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

        if ($field === 'is_active') {
            return $value ? 'Ativo' : 'Inativo';
        }

        if ($field === 'status') {
            return match ($value) {
                'available' => 'Disponível',
                'occupied' => 'Ocupada',
                'reserved' => 'Reservada',
                'cleaning' => 'Limpeza',
                default => $value,
            };
        }

        if ($field === 'capacity') {
            return $value . ' pessoas';
        }

        return $value;
    }
}
