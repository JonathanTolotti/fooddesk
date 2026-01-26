<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SettingHistory extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'setting_id',
        'event',
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

    public function setting(): BelongsTo
    {
        return $this->belongsTo(Setting::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getEventLabelAttribute(): string
    {
        return match ($this->event) {
            'created' => 'Criado',
            'updated' => 'Alterado',
            default => $this->event,
        };
    }

    public function getFormattedOldValueAttribute(): string
    {
        return $this->formatValue($this->old_value);
    }

    public function getFormattedNewValueAttribute(): string
    {
        return $this->formatValue($this->new_value);
    }

    private function formatValue(?string $value): string
    {
        if ($value === null) {
            return '-';
        }

        $setting = $this->setting;
        if (! $setting) {
            return $value;
        }

        return match ($setting->type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 'Sim' : 'Não',
            'float' => $setting->key === 'service_fee_percentage'
                ? number_format((float) $value, 1, ',', '.') . '%'
                : (str_contains($setting->key, 'value') || str_contains($setting->key, 'price')
                    ? 'R$ ' . number_format((float) $value, 2, ',', '.')
                    : number_format((float) $value, 2, ',', '.')),
            'integer' => $value,
            'json' => $this->formatJsonValue($value),
            default => $value,
        };
    }

    private function formatJsonValue(string $value): string
    {
        $decoded = json_decode($value, true);
        if (! is_array($decoded)) {
            return $value;
        }

        // For operating days
        $dayNames = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
        $formattedDays = array_map(fn ($day) => $dayNames[$day] ?? $day, $decoded);

        return implode(', ', $formattedDays);
    }
}
