<?php

namespace App\Observers;

use App\Models\Setting;
use App\Models\SettingHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class SettingObserver
{
    public function created(Setting $setting): void
    {
        SettingHistory::create([
            'setting_id' => $setting->id,
            'event' => 'created',
            'old_value' => null,
            'new_value' => $setting->value,
            'user_id' => Auth::id(),
            'created_at' => now(),
        ]);

        $this->clearCache($setting);
    }

    public function updated(Setting $setting): void
    {
        $changes = $setting->getChanges();

        if (array_key_exists('value', $changes)) {
            $oldValue = $setting->getOriginal('value');
            $newValue = $changes['value'];

            SettingHistory::create([
                'setting_id' => $setting->id,
                'event' => 'updated',
                'old_value' => $oldValue,
                'new_value' => $newValue,
                'user_id' => Auth::id(),
                'created_at' => now(),
            ]);
        }

        $this->clearCache($setting);
    }

    private function clearCache(Setting $setting): void
    {
        Cache::forget("settings.{$setting->key}");
        Cache::forget("settings.category.{$setting->category}");
        Cache::forget('settings.all');
        Cache::forget('settings.public');
    }
}
