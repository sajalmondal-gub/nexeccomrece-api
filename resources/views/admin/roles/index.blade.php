@extends('layouts.admin')

@section('title', 'Roles & Permissions - NexCommerce')
@section('page_title', 'Roles & Spatie Permissions Matrix')

@section('content')
<div class="space-y-10">
    <!-- Summary Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <p class="text-slate-400 text-sm font-medium">Create custom administrative roles, configure Spatie permissions, and update role bindings dynamically.</p>
        
        <a href="{{ route('admin.users.index') }}" class="inline-flex h-11 items-center justify-center px-5 rounded-xl border border-purple-500/40 bg-purple-500/5 text-sm font-semibold text-purple-400 hover:bg-purple-500/10 transition-colors">
            <svg class="h-4.5 w-4.5 text-purple-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            Manage User Roles
        </a>
    </div>

    <!-- Matrix & Creation Cards Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left: Spatie Roles Matrix (2 Columns Span) -->
        <div class="lg:col-span-2 space-y-6">
            @foreach($roles as $role)
                <div class="rounded-3xl border border-indigo-950/40 bg-slate-900/60 p-6 backdrop-blur-xl shadow-xl shadow-purple-950/5">
                    <div class="flex items-center justify-between border-b border-slate-800/60 pb-4 mb-5">
                        <div>
                            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-purple-500 shadow-md shadow-purple-500"></span>
                                {{ $role->name }}
                            </h3>
                            <p class="text-xs text-slate-400 font-medium mt-1">Spatie Active Bindings</p>
                        </div>

                        <!-- Core roles guard warning -->
                        @if(in_array($role->name, ['Super Admin', 'Admin', 'Customer']))
                            <span class="inline-flex items-center rounded-full bg-slate-950 px-3 py-1 text-xs font-semibold text-purple-400 border border-indigo-950/60" title="Core system roles cannot be deleted.">
                                Core Guard
                            </span>
                        @else
                            <form method="POST" action="{{ route('admin.roles.destroy', $role->id) }}" class="m-0" onsubmit="return confirm('Are you sure you want to delete this dynamic role? This action cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-red-500/20 bg-red-500/5 text-xs font-semibold text-red-400 hover:bg-red-500/10 transition-colors">
                                    Delete Role
                                </button>
                            </form>
                        @endif
                    </div>

                    <!-- Permissions checkbox list form -->
                    <form method="POST" action="{{ route('admin.roles.update', $role->id) }}">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach($permissions as $permission)
                                @php
                                    $hasPermission = $role->hasPermissionTo($permission->name);
                                    $isSuperAdmin = $role->name === 'Super Admin';
                                @endphp
                                <label class="relative flex items-start p-3 rounded-xl border border-indigo-950/20 bg-slate-950/20 cursor-pointer select-none transition-all {{ $hasPermission ? 'border-purple-500/20 bg-purple-500/5' : 'hover:bg-slate-950/40' }}">
                                    <div class="flex h-5 items-center">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                            {{ $hasPermission ? 'checked' : '' }}
                                            {{ $isSuperAdmin ? 'disabled checked' : '' }}
                                            class="h-4 w-4 rounded border-indigo-950/60 bg-slate-950 text-purple-600 focus:ring-purple-500 focus:ring-offset-slate-900 focus:ring-offset-2">
                                    </div>
                                    <div class="ml-3 text-xs">
                                        <span class="font-bold text-slate-200 block">{{ str_replace('_', ' ', strtoupper($permission->name)) }}</span>
                                        <span class="text-[10px] text-slate-500 font-medium block mt-0.5">Spatie Node</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        <!-- Update button (Disabled for Super Admin) -->
                        @if($role->name !== 'Super Admin')
                            <div class="mt-6 flex justify-end">
                                <button type="submit" class="flex h-9 items-center justify-center px-5 rounded-lg bg-gradient-to-r from-purple-600 to-indigo-600 text-xs font-bold text-white shadow-lg hover:from-purple-500 hover:to-indigo-500 transition-all">
                                    Save Bindings
                                </button>
                            </div>
                        @else
                            <div class="mt-6 flex justify-end">
                                <span class="text-[11px] text-slate-500 font-semibold italic">Super Admin retains all permissions automatically.</span>
                            </div>
                        @endif
                    </form>
                </div>
            @endforeach
        </div>

        <!-- Right: Create Role Form -->
        <div class="space-y-6">
            <div class="rounded-3xl border border-indigo-950/40 bg-slate-900/60 p-6 backdrop-blur-xl shadow-xl shadow-purple-950/5 sticky top-28">
                <h3 class="text-sm font-bold uppercase tracking-wider text-purple-400 mb-6 flex items-center gap-2">
                    <svg class="h-4.5 w-4.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Add Dynamic Role
                </h3>

                <form method="POST" action="{{ route('admin.roles.store') }}" class="space-y-6">
                    @csrf

                    <!-- Role Name -->
                    <div>
                        <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Role Name</label>
                        <input type="text" name="name" id="name" required placeholder="e.g. Moderator"
                            class="block w-full rounded-xl border border-indigo-950/60 bg-slate-950 px-4 py-3 text-sm text-slate-100 placeholder-slate-600 focus:border-purple-500 focus:outline-none focus:ring-1 focus:ring-purple-500 transition-all font-medium">
                        @error('name')
                            <p class="mt-1.5 text-xs text-red-400 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Role Permissions Selection Checklist -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-3">Grant Initial Permissions</label>
                        <div class="space-y-2.5 max-h-60 overflow-y-auto pr-2">
                            @foreach($permissions as $permission)
                                <label class="flex items-center p-2.5 rounded-lg bg-slate-950/40 hover:bg-slate-950/80 cursor-pointer select-none border border-indigo-950/30 transition-colors">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                        class="h-4 w-4 rounded border-indigo-950/60 bg-slate-950 text-purple-600 focus:ring-purple-500">
                                    <span class="ml-3 text-xs font-semibold text-slate-300">{{ str_replace('_', ' ', strtoupper($permission->name)) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Submit button -->
                    <button type="submit"
                        class="w-full flex h-11 items-center justify-center rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 font-bold text-white shadow-lg shadow-purple-500/10 hover:from-purple-500 hover:to-indigo-500 focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all text-xs tracking-wide">
                        Create & Seed Role
                    </button>

                </form>
            </div>
        </div>

    </div>
</div>
@endsection
