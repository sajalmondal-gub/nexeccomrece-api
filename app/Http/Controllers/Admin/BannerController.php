<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    private function checkPermission()
    {
        if (!auth()->user()->hasPermissionTo('manage_settings') && !auth()->user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized access.');
        }
    }

    public function index()
    {
        $this->checkPermission();
        $banners = Banner::orderBy('order', 'asc')->get();
        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        $this->checkPermission();
        return view('admin.banners.create');
    }

    public function store(Request $request)
    {
        $this->checkPermission();
        
        $request->validate([
            'title' => 'nullable|string|max:255',
            'title_bn' => 'nullable|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'link' => 'nullable|string|max:255',
            'status' => 'boolean',
            'order' => 'integer'
        ]);

        $data = $request->except('image');
        $data['status'] = $request->has('status');
        $data['order'] = $request->order ?? 0;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('banners', 'public');
        }

        Banner::create($data);

        return redirect()->route('admin.banners.index')->with('success', 'Banner uploaded successfully.');
    }

    public function edit($id)
    {
        $this->checkPermission();
        $banner = Banner::findOrFail($id);
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, $id)
    {
        $this->checkPermission();
        $banner = Banner::findOrFail($id);
        
        $request->validate([
            'title' => 'nullable|string|max:255',
            'title_bn' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'link' => 'nullable|string|max:255',
            'status' => 'boolean',
            'order' => 'integer'
        ]);

        $data = $request->except('image');
        $data['status'] = $request->has('status');
        $data['order'] = $request->order ?? 0;

        if ($request->hasFile('image')) {
            if ($banner->image && Storage::disk('public')->exists($banner->image)) {
                Storage::disk('public')->delete($banner->image);
            }
            $data['image'] = $request->file('image')->store('banners', 'public');
        }

        $banner->update($data);

        return redirect()->route('admin.banners.index')->with('success', 'Banner updated successfully.');
    }

    public function destroy($id)
    {
        $this->checkPermission();
        $banner = Banner::findOrFail($id);
        
        if ($banner->image && Storage::disk('public')->exists($banner->image)) {
            Storage::disk('public')->delete($banner->image);
        }
        
        $banner->delete();

        return redirect()->route('admin.banners.index')->with('success', 'Banner deleted successfully.');
    }
}
