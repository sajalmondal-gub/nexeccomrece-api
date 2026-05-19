<?php

namespace App\Services;

use App\Repositories\Contracts\SettingRepositoryInterface;

class SettingService
{
    protected $settingRepository;

    public function __construct(SettingRepositoryInterface $settingRepository)
    {
        $this->settingRepository = $settingRepository;
    }

    /**
     * Retrieve all configurations as dynamic key-value array
     */
    public function getSettings(): array
    {
        $settings = $this->settingRepository->all();
        
        // Define default settings if not seeded/present
        $defaults = [
            'site_name' => 'NexCommerce',
            'site_tagline' => 'Future of E-Commerce',
            'support_email' => 'support@nexcommerce.com',
            'contact_phone' => '+1 (555) 019-9000',
            'support_address' => '742 Evergreen Terrace, Springfield',
            'copyright_text' => 'NexCommerce E-Commerce Ecosystem',
            'meta_description' => 'E-Commerce platform crafted with purple gradients.',
            'site_logo' => null,
        ];

        return array_merge($defaults, $settings);
    }

    /**
     * Update settings details in batch
     */
    public function updateSettings(array $data): void
    {
        foreach ($data as $key => $value) {
            // Discard security token fields
            if (in_array($key, ['_token', '_method'])) {
                continue;
            }
            $this->settingRepository->set($key, $value);
        }
    }
}
