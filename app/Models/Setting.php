<?php

namespace App\Models;

use App\Observers\SettingObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[ObservedBy(SettingObserver::class)]
class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'key',
        'category',
        'type',
        'value',
        'default_value',
        'label',
        'description',
        'is_public',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Setting $setting): void {
            if (empty($setting->uuid)) {
                $setting->uuid = Str::uuid();
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
            'is_public' => 'boolean',
        ];
    }

    public function histories(): HasMany
    {
        return $this->hasMany(SettingHistory::class)->orderByDesc('created_at');
    }

    /**
     * Get the typed value based on the setting type.
     */
    public function getTypedValueAttribute(): mixed
    {
        $value = $this->value ?? $this->default_value;

        return match ($this->type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'float' => (float) $value,
            'json' => json_decode($value, true) ?? [],
            'time' => $value,
            default => $value,
        };
    }

    /**
     * Get the category label in Portuguese.
     */
    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'establishment' => 'Estabelecimento',
            'service' => 'Serviço',
            'kitchen' => 'Cozinha',
            'table' => 'Mesas',
            'hours' => 'Horários',
            default => $this->category,
        };
    }
}
