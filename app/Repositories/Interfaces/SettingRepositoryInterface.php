<?php

namespace App\Repositories\Interfaces;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Collection;

interface SettingRepositoryInterface
{
    public function all(): Collection;

    public function getByCategory(string $category): Collection;

    public function getPublic(): Collection;

    public function findByKey(string $key): ?Setting;

    public function findByUuid(string $uuid): ?Setting;

    public function getValue(string $key, mixed $default = null): mixed;

    public function update(Setting $setting, array $data): Setting;

    public function updateByKey(string $key, mixed $value): ?Setting;

    public function updateMultiple(array $settings): void;
}
