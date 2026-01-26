<?php

namespace App\Services;

use App\Models\Setting;
use App\Repositories\Interfaces\SettingRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class SettingService
{
    private const CACHE_TTL = 3600; // 1 hour

    public function __construct(
        private readonly SettingRepositoryInterface $repository
    ) {}

    public function all(): Collection
    {
        return Cache::remember('settings.all', self::CACHE_TTL, function () {
            return $this->repository->all();
        });
    }

    public function getByCategory(string $category): Collection
    {
        return Cache::remember("settings.category.{$category}", self::CACHE_TTL, function () use ($category) {
            return $this->repository->getByCategory($category);
        });
    }

    public function getPublic(): Collection
    {
        return Cache::remember('settings.public', self::CACHE_TTL, function () {
            return $this->repository->getPublic();
        });
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("settings.{$key}", self::CACHE_TTL, function () use ($key, $default) {
            return $this->repository->getValue($key, $default);
        });
    }

    public function findByUuid(string $uuid): ?Setting
    {
        return $this->repository->findByUuid($uuid);
    }

    public function update(Setting $setting, mixed $value): Setting
    {
        // Convert value to string for storage
        $stringValue = match (true) {
            is_bool($value) => $value ? '1' : '0',
            is_array($value) => json_encode($value),
            default => (string) $value,
        };

        return $this->repository->update($setting, ['value' => $stringValue]);
    }

    public function updateMultiple(array $settings): void
    {
        $this->repository->updateMultiple($settings);
    }

    /**
     * Get grouped settings by category.
     */
    public function getGroupedByCategory(): array
    {
        $settings = $this->all();

        return $settings->groupBy('category')->toArray();
    }

    /**
     * Calculate service fee based on settings.
     */
    public function calculateServiceFee(float $subtotal): float
    {
        $enabled = $this->get('service_fee_enabled', true);

        if (! $enabled) {
            return 0;
        }

        $type = $this->get('service_fee_type', 'percentage');

        if ($type === 'fixed') {
            return (float) $this->get('service_fee_fixed_value', 0);
        }

        $percentage = (float) $this->get('service_fee_percentage', 10);

        return round($subtotal * ($percentage / 100), 2);
    }

    /**
     * Get service fee percentage for display.
     */
    public function getServiceFeePercentage(): float
    {
        $type = $this->get('service_fee_type', 'percentage');

        if ($type === 'fixed') {
            return 0;
        }

        return (float) $this->get('service_fee_percentage', 10);
    }

    /**
     * Get service fee display label.
     */
    public function getServiceFeeLabel(): string
    {
        $type = $this->get('service_fee_type', 'percentage');

        if ($type === 'fixed') {
            $value = (float) $this->get('service_fee_fixed_value', 0);

            return 'Taxa de Serviço (R$ ' . number_format($value, 2, ',', '.') . ')';
        }

        $percentage = (float) $this->get('service_fee_percentage', 10);

        return 'Taxa de Serviço (' . number_format($percentage, 0) . '%)';
    }

    /**
     * Get kitchen alert thresholds.
     */
    public function getKitchenAlertThresholds(): array
    {
        return [
            'yellow_minutes' => (int) $this->get('kitchen_alert_yellow_minutes', 10),
            'red_minutes' => (int) $this->get('kitchen_alert_red_minutes', 20),
            'refresh_seconds' => (int) $this->get('kitchen_refresh_seconds', 10),
        ];
    }

    /**
     * Get establishment info for receipt.
     */
    public function getEstablishmentInfo(): array
    {
        return [
            'name' => $this->get('restaurant_name', 'FoodDesk'),
            'address' => $this->get('restaurant_address', ''),
            'phone' => $this->get('restaurant_phone', ''),
            'footer_message' => $this->get('receipt_footer_message', 'Obrigado pela preferência!'),
        ];
    }
}
