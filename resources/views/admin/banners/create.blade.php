@extends('layouts.admin')

@section('title', 'Upload Banner - NexCommerce')
@section('page_title', 'Upload Promotional Banner')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-8">
        <a href="{{ route('admin.banners.index') }}" class="text-sm font-medium text-slate-400 hover:text-white transition-colors">&larr; Back to Banners</a>
    </div>

    <div class="rounded-3xl border border-indigo-950/40 bg-slate-900/60 p-8 backdrop-blur-xl shadow-xl shadow-purple-950/5">
        <form method="POST" action="{{ route('admin.banners.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Image Upload -->
            <div>
                <label for="image" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Banner Image (Required)</label>
                <input type="file" name="image" id="image" required accept="image/*"
                    class="block w-full text-sm text-slate-500 file:mr-4 file:py-3 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-purple-500/10 file:text-purple-400 hover:file:bg-purple-500/20 transition-all cursor-pointer border border-indigo-950/60 bg-slate-950 rounded-xl p-1.5 focus:border-purple-500 focus:outline-none focus:ring-1 focus:ring-purple-500">
                @error('image') <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            <!-- Title -->
            <div>
                <label for="title" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Banner Title (Optional)</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" placeholder="Summer Sale 2026"
                    class="block w-full rounded-xl border border-indigo-950/60 bg-slate-950 px-4 py-3 text-sm text-slate-100 placeholder-slate-500 focus:border-purple-500 focus:outline-none focus:ring-1 focus:ring-purple-500 transition-all">
                @error('title') <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            <!-- Link -->
            <div>
                <label for="link" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Action Link (Optional)</label>
                <input type="text" name="link" id="link" value="{{ old('link') }}" placeholder="https://app.nexcommerce.com/sale"
                    class="block w-full rounded-xl border border-indigo-950/60 bg-slate-950 px-4 py-3 text-sm text-slate-100 placeholder-slate-500 focus:border-purple-500 focus:outline-none focus:ring-1 focus:ring-purple-500 transition-all">
                <p class="mt-2 text-xs text-slate-500">The URL that opens when a user taps this banner in the app.</p>
                @error('link') <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-6">
                <!-- Order -->
                <div>
                    <label for="order" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Sort Order</label>
                    <input type="number" name="order" id="order" value="{{ old('order', 0) }}"
                        class="block w-full rounded-xl border border-indigo-950/60 bg-slate-950 px-4 py-3 text-sm text-slate-100 placeholder-slate-500 focus:border-purple-500 focus:outline-none focus:ring-1 focus:ring-purple-500 transition-all">
                    <p class="mt-2 text-[10px] text-slate-500">Lower numbers appear first (e.g. 0, 1, 2).</p>
                </div>

                <!-- Status Toggle -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Visibility Status</label>
                    <label class="relative inline-flex items-center cursor-pointer mt-2">
                        <input type="checkbox" name="status" value="1" class="sr-only peer" checked>
                        <div class="w-11 h-6 bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-500"></div>
                        <span class="ml-3 text-sm font-medium text-slate-300">Active (Visible on App)</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end pt-6 border-t border-indigo-950/40">
                <button type="submit" class="flex h-12 items-center justify-center px-8 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 font-bold text-white shadow-lg shadow-purple-500/10 hover:from-purple-500 hover:to-indigo-500 focus:outline-none transition-all">
                    Upload & Publish Banner
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
