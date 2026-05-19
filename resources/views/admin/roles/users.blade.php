@extends('layouts.admin')

@section('title', 'Manage Users Roles - NexCommerce')
@section('page_title', 'User Accounts Directory')

@section('content')
<div class="space-y-6">
    <!-- Header summary -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <p class="text-slate-400 text-sm font-medium">Reassign security roles to registered accounts. Assigning administrative roles instantly opens system controls.</p>
        
        <a href="{{ route('admin.roles.index') }}" class="inline-flex h-11 items-center justify-center px-5 rounded-xl border border-purple-500/40 bg-purple-500/5 text-sm font-semibold text-purple-400 hover:bg-purple-500/10 transition-colors">
            <svg class="h-4.5 w-4.5 text-purple-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.957 11.957 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
            Spatie Roles Matrix
        </a>
    </div>

    <!-- Responsive Admin Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        
        <!-- Left: Users Table (Col Span 2) -->
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-3xl border border-indigo-950/40 bg-slate-900/60 overflow-hidden backdrop-blur-xl shadow-xl shadow-purple-950/5">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-800/60 text-left">
                        <thead class="bg-slate-950/40">
                            <tr>
                                <th scope="col" class="px-6 py-4.5 text-xs font-bold uppercase tracking-wider text-slate-400">User Profile</th>
                                <th scope="col" class="px-6 py-4.5 text-xs font-bold uppercase tracking-wider text-slate-400">Email Address</th>
                                <th scope="col" class="px-6 py-4.5 text-xs font-bold uppercase tracking-wider text-slate-400">Current Role</th>
                                <th scope="col" class="px-6 py-4.5 text-xs font-bold uppercase tracking-wider text-slate-400 text-right">Assign Security Role</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/40 bg-transparent">
                            @foreach($users as $user)
                                <tr class="hover:bg-slate-900/30 transition-colors">
                                    <!-- Name / ID -->
                                    <td class="whitespace-nowrap px-6 py-5">
                                        <div class="flex items-center gap-3">
                                            <div class="h-10 w-10 rounded-xl bg-gradient-to-tr from-purple-500 to-indigo-500 flex items-center justify-center font-bold text-white shadow-md">
                                                {{ substr($user->name, 0, 2) }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-bold text-slate-200">{{ $user->name }}</div>
                                                <div class="text-[10px] text-slate-500 font-semibold mt-0.5">USER ID: #{{ $user->id }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Email -->
                                    <td class="whitespace-nowrap px-6 py-5">
                                        <span class="text-sm text-slate-300 font-medium">{{ $user->email }}</span>
                                    </td>

                                    <!-- Current Role Badge -->
                                    <td class="whitespace-nowrap px-6 py-5">
                                        @php
                                            $firstRole = $user->roles->first();
                                            $roleName = $firstRole ? $firstRole->name : 'No Role';
                                            
                                            // Custom colors based on role
                                            if ($roleName === 'Super Admin') {
                                                $bg = 'bg-red-500/10 text-red-400 border-red-500/20';
                                            } elseif ($roleName === 'Admin') {
                                                $bg = 'bg-purple-500/10 text-purple-400 border-purple-500/20';
                                            } elseif ($roleName === 'Support' || $roleName === 'Manager') {
                                                $bg = 'bg-indigo-500/10 text-indigo-400 border-indigo-500/20';
                                            } else {
                                                $bg = 'bg-slate-950 text-slate-400 border-indigo-950/40';
                                            }
                                        @endphp
                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold border {{ $bg }}">
                                            {{ $roleName }}
                                        </span>
                                    </td>

                                    <!-- Role Reassign Selection Form -->
                                    <td class="whitespace-nowrap px-6 py-5 text-right">
                                        @if($user->email === 'admin@nexcommerce.com')
                                            <!-- Protect the seeded Super Admin from role changes -->
                                            <span class="text-xs text-slate-500 font-semibold italic">Protected Seed Admin</span>
                                        @else
                                            <form method="POST" action="{{ route('admin.users.update-role', $user->id) }}" class="inline-flex items-center gap-3 m-0">
                                                @csrf
                                                <select name="role" required
                                                    class="rounded-xl border border-indigo-950/60 bg-slate-950 px-3 py-2 text-xs text-slate-100 placeholder-slate-500 focus:border-purple-500 focus:outline-none focus:ring-1 focus:ring-purple-500 font-semibold">
                                                    @foreach($roles as $role)
                                                        <option value="{{ $role->name }}" {{ $roleName === $role->name ? 'selected' : '' }}>
                                                            {{ $role->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                
                                                <button type="submit" class="flex h-8 items-center justify-center px-4 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 text-xs font-bold text-white shadow-md hover:from-purple-500 hover:to-indigo-500 transition-all">
                                                    Reassign
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right: Add Administrator Card (Col Span 1) -->
        <div class="lg:col-span-1">
            <div class="rounded-3xl border border-indigo-950/40 bg-slate-900/60 p-6 backdrop-blur-xl shadow-xl shadow-purple-950/5">
                <h3 class="text-base font-bold text-white mb-5 border-b border-indigo-950/40 pb-3 flex items-center gap-2">
                    <svg class="h-4.5 w-4.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    Add Administrator User
                </h3>
                
                <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Full Name *</label>
                        <input type="text" name="name" required class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-xs text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" placeholder="e.g. John Doe">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Email Address *</label>
                        <input type="email" name="email" required class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-xs text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" placeholder="e.g. john@nexcommerce.com">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Security Password *</label>
                        <input type="password" name="password" required class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-xs text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" placeholder="••••••••">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Administrative Role *</label>
                        <select name="role" required class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-xs text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500">
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Direct Permission Overrides</label>
                        <div class="space-y-2 bg-slate-950/40 rounded-xl p-3.5 border border-indigo-950/40">
                            @php
                                $availablePermissions = [
                                    'manage_products' => 'Catalog Access (Products, Coupons, Reviews)',
                                    'manage_orders' => 'Sales Access (Orders, Invoices)',
                                    'manage_users' => 'User Management Access',
                                    'manage_roles' => 'Spatie Security Desk overrides',
                                    'manage_settings' => 'System Settings Access',
                                    'view_reports' => 'Reports & Dashboard Audits',
                                ];
                            @endphp
                            
                            @foreach($availablePermissions as $permKey => $permLabel)
                                <div class="flex items-center gap-2.5">
                                    <input type="checkbox" name="permissions[]" value="{{ $permKey }}" id="perm_{{ $permKey }}" class="h-4 w-4 rounded border-indigo-950 bg-slate-950 text-purple-600 focus:ring-purple-500">
                                    <label for="perm_{{ $permKey }}" class="text-[11px] font-semibold text-slate-300 cursor-pointer select-none">{{ $permLabel }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-purple-600 via-purple-500 to-indigo-500 py-3 text-xs font-bold text-white shadow-lg shadow-purple-500/10 hover:from-purple-500 hover:to-indigo-500 transition-all duration-200 uppercase tracking-wider">
                            Register Admin User
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
