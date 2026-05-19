<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SettingService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    protected $settingService;

    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    /**
     * Display settings form dashboard
     */
    public function index()
    {
        // Enforce settings configuration access permission
        if (!auth()->user()->hasPermissionTo('manage_settings') && !auth()->user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized access.');
        }

        $settings = $this->settingService->getSettings();

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update settings details in batch
     */
    public function update(Request $request)
    {
        if (!auth()->user()->hasPermissionTo('manage_settings') && !auth()->user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'site_name' => 'required|string|max:50',
            'site_name_bn' => 'nullable|string|max:50',
            'site_tagline' => 'nullable|string|max:100',
            'site_tagline_bn' => 'nullable|string|max:100',
            'support_email' => 'required|email|max:100',
            'contact_phone' => 'nullable|string|max:30',
            'support_address' => 'nullable|string|max:250',
            'support_address_bn' => 'nullable|string|max:250',
            'copyright_text' => 'nullable|string|max:100',
            'copyright_text_bn' => 'nullable|string|max:100',
            'meta_description' => 'nullable|string|max:500',
            'meta_description_bn' => 'nullable|string|max:500',
            'site_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'site_favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,ico,svg,webp|max:1024',
        ]);

        $data = $request->except(['site_logo', 'site_favicon']);

        if ($request->hasFile('site_logo')) {
            $path = $request->file('site_logo')->store('site', 'public');
            $data['site_logo'] = $path;
        }

        if ($request->hasFile('site_favicon')) {
            $path = $request->file('site_favicon')->store('site', 'public');
            $data['site_favicon'] = $path;
        }

        $this->settingService->updateSettings($data);

        return redirect()->back()->with('success', 'Site settings updated successfully!');
    }
}
