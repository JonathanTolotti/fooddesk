<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductHistory extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'product_id',
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

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
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
            'price' => 'Preço',
            'category_id' => 'Categoria',
            'image' => 'Imagem',
            'is_active' => 'Status',
            'sort_order' => 'Ordem',
            'ingredients' => 'Ingredientes',
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

        if ($field === 'price') {
            return 'R$ '.number_format((float) $value, 2, ',', '.');
        }

        if ($field === 'category_id') {
            $category = Category::find($value);

            return $category?->name ?? $value;
        }

        return $value;
    }
}
