@extends('layouts.admin')

@section('title', 'NexCommerce Customer Reviews Moderation')
@section('page_title', 'Reviews Approval Desk')

@section('content')
    <div class="rounded-2xl bg-slate-900/40 border border-indigo-950/40 backdrop-blur-md p-6">
        <h3 class="text-lg font-bold text-white flex items-center gap-2 mb-6 border-b border-indigo-950/40 pb-4">
            <svg class="h-5 w-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.907c.961 0 1.371 1.24.588 1.81l-3.97 2.883a1 1 0 00-.364 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.971-2.883a1 1 0 00-1.17 0l-3.97 2.883c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.364-1.118L2.49 10.118c-.783-.57-.373-1.81.588-1.81h4.906a1 1 0 00.951-.69l1.519-4.674z" />
            </svg>
            Review Moderation Queue
        </h3>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse($reviews as $review)
                <div class="relative overflow-hidden rounded-2xl border transition-all duration-200 p-5 flex flex-col justify-between {{ !$review->approved ? 'border-purple-500/35 bg-purple-950/5 shadow-md shadow-purple-950/5' : 'border-indigo-950/40 bg-indigo-950/5 hover:border-slate-800' }}">
                    <!-- Card Top Accent -->
                    @if(!$review->approved)
                        <div class="absolute top-0 right-0 rounded-bl-xl bg-purple-500/10 px-2.5 py-1 text-[9px] font-bold uppercase tracking-wider text-purple-400 ring-1 ring-inset ring-purple-500/20">
                            Awaiting Moderation
                        </div>
                    @endif

                    <div>
                        <!-- Rating Stars -->
                        <div class="flex items-center gap-0.5 mb-3">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="h-4.5 w-4.5 {{ $i <= $review->rating ? 'text-amber-400' : 'text-slate-700' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>

                        <!-- Product Link -->
                        <p class="text-xs font-bold uppercase tracking-wider text-purple-400 mb-1.5">
                            Product Catalog Link
                        </p>
                        <h4 class="text-sm font-bold text-slate-200 mb-3 truncate">
                            {{ $review->product->name ?? 'Deleted Catalog Product' }}
                        </h4>

                        <!-- Review Text -->
                        <p class="text-sm text-slate-400 leading-relaxed font-medium italic mb-4">
                            "{{ $review->comment }}"
                        </p>
                    </div>

                    <!-- Card Footer Details -->
                    <div class="mt-4 border-t border-indigo-950/20 pt-4 flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-slate-300">{{ $review->user->name ?? 'Verified Buyer' }}</p>
                            <p class="text-[10px] text-slate-500 font-medium mt-0.5">{{ $review->created_at->format('M d, Y') }}</p>
                        </div>

                        <div class="flex items-center gap-1.5">
                            <!-- Approve Action -->
                            @if(!$review->approved)
                                <form method="POST" action="{{ route('admin.reviews.approve', $review->id) }}" class="m-0">
                                    @csrf
                                    <button type="submit" class="p-2 rounded-xl bg-purple-600/90 text-white border border-purple-500/10 hover:bg-purple-500 transition-colors shadow-sm" title="Approve & Publish">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    </button>
                                </form>
                            @else
                                <span class="inline-flex items-center rounded-lg bg-emerald-500/10 px-2 py-1 text-[10px] font-bold uppercase tracking-wider text-emerald-400 ring-1 ring-inset ring-emerald-500/20">
                                    <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    Published
                                </span>
                            @endif

                            <!-- Delete Action -->
                            <form method="POST" action="{{ route('admin.reviews.destroy', $review->id) }}" onsubmit="return confirm('Are you sure you want to delete this review?')" class="m-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 rounded-xl bg-slate-800 text-slate-400 border border-slate-700 hover:bg-red-500/10 hover:text-red-400 hover:border-red-500/20 transition-colors" title="Purge Review">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center text-slate-500 py-12 font-medium">
                    No customer reviews have been submitted in the moderation pool.
                </div>
            @endforelse
        </div>

        <!-- Pagination Section -->
        <div class="mt-8 border-t border-indigo-950/30 pt-4">
            {{ $reviews->links() }}
        </div>
    </div>
@endsection
