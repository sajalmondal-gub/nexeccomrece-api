<?php

namespace App\Repositories\Contracts;

interface SettingRepositoryInterface
{
    /**
     * Get all settings as key-value pairs
     */
    public function all(): array;

    /**
     * Get a setting value by key
     */
    public function getByKey(string $key, $default = null): ?string;

    /**
     * Set a setting value by key
     */
    public function set(string $key, ?string $value): void;
}
