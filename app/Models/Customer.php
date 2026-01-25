<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
        'phone',
        'birth_date',
        'is_active',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Customer $customer): void {
            if (empty($customer->uuid)) {
                $customer->uuid = Str::uuid();
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
            'birth_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Format phone for display
     */
    public function getFormattedPhoneAttribute(): string
    {
        $phone = preg_replace('/\D/', '', $this->phone);

        if (strlen($phone) === 11) {
            return sprintf('(%s) %s-%s', substr($phone, 0, 2), substr($phone, 2, 5), substr($phone, 7));
        }

        if (strlen($phone) === 10) {
            return sprintf('(%s) %s-%s', substr($phone, 0, 2), substr($phone, 2, 4), substr($phone, 6));
        }

        return $this->phone;
    }

    /**
     * Check if today is customer's birthday
     */
    public function getIsBirthdayAttribute(): bool
    {
        if (!$this->birth_date) {
            return false;
        }

        return $this->birth_date->format('m-d') === now()->format('m-d');
    }
}
