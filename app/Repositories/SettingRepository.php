<?php

namespace App\Repositories;

use App\Models\Setting;
use App\Repositories\Interfaces\SettingRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class SettingRepository implements SettingRepositoryInterface
{
    public function __construct(
        private readonly Setting $model
    ) {}

    public function all(): Collection
    {
        return $this->model
            ->orderBy('category')
            ->orderBy('id')
            ->get();
    }

    public function getByCategory(string $category): Collection
    {
        return $this->model
            ->where('category', $category)
            ->orderBy('id')
            ->get();
    }

    public function getPublic(): Collection
    {
        return $this->model
            ->where('is_public', true)
            ->get();
    }

    public function findByKey(string $key): ?Setting
    {
        return $this->model->where('key', $key)->first();
    }

    public function findByUuid(string $uuid): ?Setting
    {
        return $this->model->where('uuid', $uuid)->first();
    }

    public function getValue(string $key, mixed $default = null): mixed
    {
        $setting = $this->findByKey($key);

        if (! $setting) {
            return $default;
        }

        return $setting->typed_value;
    }

    public function update(Setting $setting, array $data): Setting
    {
        $setting->update($data);

        return $setting->fresh();
    }

    public function updateByKey(string $key, mixed $value): ?Setting
    {
        $setting = $this->findByKey($key);

        if (! $setting) {
            return null;
        }

        // Convert value to string for storage
        $stringValue = match (true) {
            is_bool($value) => $value ? '1' : '0',
            is_array($value) => json_encode($value),
            default => (string) $value,
        };

        $setting->update(['value' => $stringValue]);

        return $setting->fresh();
    }

    public function updateMultiple(array $settings): void
    {
        foreach ($settings as $key => $value) {
            $this->updateByKey($key, $value);
        }
    }
}
