<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Helper to retrieve setting by key
     */
    public static function get(string $key, $default = null)
    {
        if (app()->getLocale() === 'bn') {
            $setting = self::where('key', $key . '_bn')->first();
            if ($setting && !empty($setting->value)) {
                return $setting->value;
            }
        }
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Helper to set setting key value
     */
    public static function set(string $key, ?string $value)
    {
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}
