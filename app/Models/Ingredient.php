<?php

namespace App\Models;

use App\Observers\IngredientObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[ObservedBy(IngredientObserver::class)]
class Ingredient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
        'description',
        'is_active',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Ingredient $ingredient): void {
            if (empty($ingredient->uuid)) {
                $ingredient->uuid = Str::uuid();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function histories(): HasMany
    {
        return $this->hasMany(IngredientHistory::class)->orderByDesc('created_at');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_ingredient')
            ->withPivot(['type', 'quantity', 'additional_price'])
            ->withTimestamps();
    }
}
