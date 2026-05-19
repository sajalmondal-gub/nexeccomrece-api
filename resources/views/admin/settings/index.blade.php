@extends('layouts.admin')

@section('title', 'Site Settings - NexCommerce')
@section('page_title', 'Global Site Configuration')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header Summary -->
    <div class="mb-8">
        <p class="text-slate-400 text-sm font-medium">Manage e-commerce site headers, support records, SEO tags, logos, and dynamic system layouts.</p>
    </div>

    <!-- Main Card -->
    <div class="rounded-3xl border border-indigo-950/40 bg-slate-900/60 p-8 backdrop-blur-xl shadow-xl shadow-purple-950/5">
        <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-8" enctype="multipart/form-data">
            @csrf

            <!-- Section 1: Visual Identity -->
            <div>
                <h3 class="text-sm font-bold uppercase tracking-wider text-purple-400 mb-6 flex items-center gap-2">
                    <svg class="h-4.5 w-4.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    1. Brand Identity
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Site Name -->
                    <div>
                        <label for="site_name" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Site Title / Name</label>
                        <input type="text" name="site_name" id="site_name" required value="{{ old('site_name', $settings['site_name']) }}"
                            class="block w-full rounded-xl border border-indigo-950/60 bg-slate-950 px-4 py-3 text-sm text-slate-100 placeholder-slate-500 focus:border-purple-500 focus:outline-none focus:ring-1 focus:ring-purple-500 transition-all font-medium">
                        @error('site_name')
                            <p class="mt-1.5 text-xs text-red-400 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Site Name Bangla -->
                    <div>
                        <label for="site_name_bn" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Site Title (Bangla)</label>
                        <input type="text" name="site_name_bn" id="site_name_bn" value="{{ old('site_name_bn', $settings['site_name_bn'] ?? '') }}"
                            class="block w-full rounded-xl border border-indigo-950/60 bg-slate-950 px-4 py-3 text-sm text-slate-100 placeholder-slate-500 focus:border-purple-500 focus:outline-none focus:ring-1 focus:ring-purple-500 transition-all font-medium">
                    </div>

                    <!-- Tagline -->
                    <div>
                        <label for="site_tagline" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Brand Tagline</label>
                        <input type="text" name="site_tagline" id="site_tagline" value="{{ old('site_tagline', $settings['site_tagline']) }}"
                            class="block w-full rounded-xl border border-indigo-950/60 bg-slate-950 px-4 py-3 text-sm text-slate-100 placeholder-slate-500 focus:border-purple-500 focus:outline-none focus:ring-1 focus:ring-purple-500 transition-all font-medium">
                    </div>

                    <!-- Tagline Bangla -->
                    <div>
                        <label for="site_tagline_bn" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Brand Tagline (Bangla)</label>
                        <input type="text" name="site_tagline_bn" id="site_tagline_bn" value="{{ old('site_tagline_bn', $settings['site_tagline_bn'] ?? '') }}"
                            class="block w-full rounded-xl border border-indigo-950/60 bg-slate-950 px-4 py-3 text-sm text-slate-100 placeholder-slate-500 focus:border-purple-500 focus:outline-none focus:ring-1 focus:ring-purple-500 transition-all font-medium">
                    </div>
                </div>

                <!-- Site Logo & Favicon -->
                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="site_logo" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Upload Site Logo</label>
                        <div class="flex gap-4 items-center">
                            <input type="file" name="site_logo" id="site_logo" accept="image/*"
                                class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-purple-500/10 file:text-purple-400 hover:file:bg-purple-500/20 transition-all cursor-pointer">
                            
                            <!-- Logo Preview -->
                            <div class="h-12 w-12 shrink-0 rounded-xl bg-slate-950 flex items-center justify-center border border-indigo-950/40">
                                @if(!empty($settings['site_logo']) && str_starts_with($settings['site_logo'], 'http'))
                                    <img src="{{ $settings['site_logo'] }}" alt="Logo" class="h-8 w-8 object-contain">
                                @elseif(!empty($settings['site_logo']))
                                    <img src="{{ asset('storage/' . $settings['site_logo']) }}" alt="Logo" class="h-8 w-8 object-contain">
                                @else
                                    <svg class="h-5 w-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="site_favicon" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Upload Favicon (Icon)</label>
                        <div class="flex gap-4 items-center">
                            <input type="file" name="site_favicon" id="site_favicon" accept="image/*"
                                class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-indigo-500/10 file:text-indigo-400 hover:file:bg-indigo-500/20 transition-all cursor-pointer">
                            
                            <!-- Favicon Preview -->
                            <div class="h-12 w-12 shrink-0 rounded-xl bg-slate-950 flex items-center justify-center border border-indigo-950/40">
                                @if(!empty($settings['site_favicon']))
                                    <img src="{{ asset('storage/' . $settings['site_favicon']) }}" alt="Favicon" class="h-8 w-8 object-contain">
                                @else
                                    <svg class="h-5 w-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Divider -->
            <div class="h-px bg-slate-800/40"></div>

            <!-- Section 2: Contact Records -->
            <div>
                <h3 class="text-sm font-bold uppercase tracking-wider text-purple-400 mb-6 flex items-center gap-2">
                    <svg class="h-4.5 w-4.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    2. Support & Logistics Contacts
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Support Email -->
                    <div>
                        <label for="support_email" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Support Email Address</label>
                        <input type="email" name="support_email" id="support_email" required value="{{ old('support_email', $settings['support_email']) }}"
                            class="block w-full rounded-xl border border-indigo-950/60 bg-slate-950 px-4 py-3 text-sm text-slate-100 placeholder-slate-500 focus:border-purple-500 focus:outline-none focus:ring-1 focus:ring-purple-500 transition-all font-medium">
                    </div>

                    <!-- Contact Phone -->
                    <div>
                        <label for="contact_phone" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Contact Phone Number</label>
                        <input type="text" name="contact_phone" id="contact_phone" value="{{ old('contact_phone', $settings['contact_phone']) }}"
                            class="block w-full rounded-xl border border-indigo-950/60 bg-slate-950 px-4 py-3 text-sm text-slate-100 placeholder-slate-500 focus:border-purple-500 focus:outline-none focus:ring-1 focus:ring-purple-500 transition-all font-medium">
                    </div>
                </div>

                <!-- Support Address -->
                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="support_address" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Office / warehouse Address</label>
                        <textarea name="support_address" id="support_address" rows="3"
                            class="block w-full rounded-xl border border-indigo-950/60 bg-slate-950 px-4 py-3 text-sm text-slate-100 placeholder-slate-500 focus:border-purple-500 focus:outline-none focus:ring-1 focus:ring-purple-500 transition-all font-medium resize-none">{{ old('support_address', $settings['support_address']) }}</textarea>
                    </div>
                    <div>
                        <label for="support_address_bn" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Office Address (Bangla)</label>
                        <textarea name="support_address_bn" id="support_address_bn" rows="3"
                            class="block w-full rounded-xl border border-indigo-950/60 bg-slate-950 px-4 py-3 text-sm text-slate-100 placeholder-slate-500 focus:border-purple-500 focus:outline-none focus:ring-1 focus:ring-purple-500 transition-all font-medium resize-none">{{ old('support_address_bn', $settings['support_address_bn'] ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Divider -->
            <div class="h-px bg-slate-800/40"></div>

            <!-- Section 3: Localization & SEO -->
            <div>
                <h3 class="text-sm font-bold uppercase tracking-wider text-purple-400 mb-6 flex items-center gap-2">
                    <svg class="h-4.5 w-4.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                    </svg>
                    3. Legalities & SEO Search Tags
                </h3>

                <!-- Copyright Text -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="copyright_text" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Copyright Footer Text</label>
                        <input type="text" name="copyright_text" id="copyright_text" value="{{ old('copyright_text', $settings['copyright_text']) }}"
                            class="block w-full rounded-xl border border-indigo-950/60 bg-slate-950 px-4 py-3 text-sm text-slate-100 placeholder-slate-500 focus:border-purple-500 focus:outline-none focus:ring-1 focus:ring-purple-500 transition-all font-medium">
                    </div>
                    <div>
                        <label for="copyright_text_bn" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Copyright Footer Text (Bangla)</label>
                        <input type="text" name="copyright_text_bn" id="copyright_text_bn" value="{{ old('copyright_text_bn', $settings['copyright_text_bn'] ?? '') }}"
                            class="block w-full rounded-xl border border-indigo-950/60 bg-slate-950 px-4 py-3 text-sm text-slate-100 placeholder-slate-500 focus:border-purple-500 focus:outline-none focus:ring-1 focus:ring-purple-500 transition-all font-medium">
                    </div>
                </div>

                <!-- Meta Description -->
                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="meta_description" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">SEO Meta Description</label>
                        <textarea name="meta_description" id="meta_description" rows="3"
                            class="block w-full rounded-xl border border-indigo-950/60 bg-slate-950 px-4 py-3 text-sm text-slate-100 placeholder-slate-500 focus:border-purple-500 focus:outline-none focus:ring-1 focus:ring-purple-500 transition-all font-medium resize-none">{{ old('meta_description', $settings['meta_description']) }}</textarea>
                    </div>
                    <div>
                        <label for="meta_description_bn" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">SEO Meta Description (Bangla)</label>
                        <textarea name="meta_description_bn" id="meta_description_bn" rows="3"
                            class="block w-full rounded-xl border border-indigo-950/60 bg-slate-950 px-4 py-3 text-sm text-slate-100 placeholder-slate-500 focus:border-purple-500 focus:outline-none focus:ring-1 focus:ring-purple-500 transition-all font-medium resize-none">{{ old('meta_description_bn', $settings['meta_description_bn'] ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end pt-4 border-t border-slate-800/40">
                <button type="submit"
                    class="flex h-12 items-center justify-center px-8 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 font-bold text-white shadow-lg shadow-purple-500/10 hover:from-purple-500 hover:to-indigo-500 focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all tracking-wide">
                    Save System Settings
                </button>
            </div>

        </form>
    </div>
</div>
@endsection
