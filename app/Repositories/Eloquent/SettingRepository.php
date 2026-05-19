<?php

namespace App\Repositories\Eloquent;

use App\Models\Setting;
use App\Repositories\Contracts\SettingRepositoryInterface;

class SettingRepository implements SettingRepositoryInterface
{
    public function all(): array
    {
        return Setting::pluck('value', 'key')->toArray();
    }

    public function getByKey(string $key, $default = null): ?string
    {
        return Setting::get($key, $default);
    }

    public function set(string $key, ?string $value): void
    {
        Setting::set($key, $value);
    }
}
