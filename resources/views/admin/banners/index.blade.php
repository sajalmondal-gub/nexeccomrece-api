@extends('layouts.admin')

@section('title', 'Banners & Promotions - NexCommerce')
@section('page_title', 'Dynamic App Banners')

@section('content')
<div class="mb-8 flex items-center justify-between">
    <p class="text-slate-400 text-sm font-medium">Manage promotional banners that appear on the mobile app home screen.</p>
    <a href="{{ route('admin.banners.create') }}" class="flex h-10 items-center justify-center px-6 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 font-bold text-white shadow-lg shadow-purple-500/10 hover:from-purple-500 hover:to-indigo-500 focus:outline-none transition-all text-sm">
        + Upload New Banner
    </a>
</div>

<div class="rounded-3xl border border-indigo-950/40 bg-slate-900/60 p-1 backdrop-blur-xl shadow-xl shadow-purple-950/5 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-400">
            <thead class="bg-slate-950/50 text-xs uppercase text-slate-300 font-bold tracking-wider border-b border-indigo-950/40">
                <tr>
                    <th scope="col" class="px-6 py-5 rounded-tl-3xl">Sort Order</th>
                    <th scope="col" class="px-6 py-5">Banner Preview</th>
                    <th scope="col" class="px-6 py-5">Title & Link</th>
                    <th scope="col" class="px-6 py-5">Status</th>
                    <th scope="col" class="px-6 py-5 rounded-tr-3xl">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-indigo-950/30">
                @forelse($banners as $banner)
                <tr class="hover:bg-slate-800/30 transition-colors">
                    <td class="px-6 py-4 font-semibold text-white">#{{ $banner->order }}</td>
                    <td class="px-6 py-4">
                        <div class="h-16 w-32 rounded-lg bg-slate-950 flex items-center justify-center border border-indigo-950/40 overflow-hidden shrink-0 shadow-lg">
                            @if($banner->image)
                                <img src="{{ asset('storage/' . $banner->image) }}" alt="Banner" class="h-full w-full object-cover">
                            @else
                                <span class="text-xs text-slate-600">No Image</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-semibold text-slate-200">{{ $banner->title ?? 'Untitled Banner' }}</div>
                        <div class="text-xs text-slate-500 mt-1 truncate max-w-[200px]">{{ $banner->link ?? 'No specific link' }}</div>
                    </td>
                    <td class="px-6 py-4">
                        @if($banner->status)
                            <span class="inline-flex items-center rounded-full bg-emerald-500/10 px-2.5 py-1 text-xs font-semibold text-emerald-400 border border-emerald-500/20">Active</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-red-500/10 px-2.5 py-1 text-xs font-semibold text-red-400 border border-red-500/20">Hidden</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex gap-3">
                            <a href="{{ route('admin.banners.edit', $banner->id) }}" class="text-purple-400 hover:text-purple-300 font-medium transition-colors">Edit</a>
                            
                            <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST" onsubmit="return confirm('Delete this banner? This cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-300 font-medium transition-colors">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                        <svg class="mx-auto h-12 w-12 text-slate-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        No promotional banners found. Upload one to display it on the app!
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
