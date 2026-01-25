<?php

namespace App\Models;

use App\Observers\TableObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[ObservedBy(TableObserver::class)]
class Table extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'number',
        'name',
        'capacity',
        'status',
        'is_active',
        'calling_waiter',
        'called_waiter_at',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Table $table): void {
            if (empty($table->uuid)) {
                $table->uuid = Str::uuid();
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
            'calling_waiter' => 'boolean',
            'called_waiter_at' => 'datetime',
            'number' => 'integer',
            'capacity' => 'integer',
        ];
    }

    public function histories(): HasMany
    {
        return $this->hasMany(TableHistory::class)->orderByDesc('created_at');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function currentOrder(): ?Order
    {
        return $this->orders()->where('status', 'open')->first();
    }

    public function isAvailable(): bool
    {
        return $this->status === 'available' && $this->is_active;
    }

    public function isOccupied(): bool
    {
        return $this->status === 'occupied';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'available' => 'Disponível',
            'occupied' => 'Ocupada',
            'reserved' => 'Reservada',
            'cleaning' => 'Limpeza',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'available' => 'green',
            'occupied' => 'red',
            'reserved' => 'yellow',
            'cleaning' => 'blue',
            default => 'gray',
        };
    }
}
