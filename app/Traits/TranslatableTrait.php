<?php

namespace App\Traits;

trait TranslatableTrait
{
    /**
     * Get a translatable attribute depending on the active locale.
     *
     * @param string $key
     * @param mixed $fallbackValue
     * @return mixed
     */
    protected function getTranslatedAttribute(string $key, $fallbackValue)
    {
        if (app()->getLocale() === 'bn') {
            $bnKey = $key . '_bn';
            if (isset($this->attributes[$bnKey]) && !empty($this->attributes[$bnKey])) {
                return $this->attributes[$bnKey];
            }
        }
        return $fallbackValue;
    }
}
