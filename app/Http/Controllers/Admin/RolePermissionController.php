<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\RolePermissionService;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    protected $rolePermissionService;

    public function __construct(RolePermissionService $rolePermissionService)
    {
        $this->rolePermissionService = $rolePermissionService;
    }

    /**
     * Display Roles & Permissions dynamic matrix dashboard
     */
    public function index()
    {
        if (!auth()->user()->hasPermissionTo('manage_roles') && !auth()->user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized access.');
        }

        $roles = $this->rolePermissionService->getRoles();
        $permissions = $this->rolePermissionService->getPermissions();

        return view('admin.roles.index', compact('roles', 'permissions'));
    }

    /**
     * Store a brand new role with selected Spatie permissions
     */
    public function storeRole(Request $request)
    {
        if (!auth()->user()->hasPermissionTo('manage_roles') && !auth()->user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'name' => 'required|string|max:50|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $this->rolePermissionService->createRoleWithPermissions($request->name, $request->input('permissions', []));

        return redirect()->back()->with('success', 'Dynamic Role "' . $request->name . '" created and configured successfully!');
    }

    /**
     * Update Spatie permissions assigned to an existing role
     */
    public function updateRole(Request $request, $id)
    {
        if (!auth()->user()->hasPermissionTo('manage_roles') && !auth()->user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $this->rolePermissionService->updateRolePermissions($id, $request->input('permissions', []));

        return redirect()->back()->with('success', 'Role permission bindings updated successfully!');
    }

    /**
     * Delete user-defined roles dynamically
     */
    public function destroyRole($id)
    {
        if (!auth()->user()->hasPermissionTo('manage_roles') && !auth()->user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized access.');
        }

        $success = $this->rolePermissionService->deleteRole($id);

        if ($success) {
            return redirect()->back()->with('success', 'Role deleted successfully!');
        }

        return redirect()->back()->with('error', 'Action Aborted: Protected system seeding roles cannot be deleted.');
    }

    /**
     * Display accounts and role controls
     */
    public function users()
    {
        if (!auth()->user()->hasPermissionTo('manage_users') && !auth()->user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized access.');
        }

        $users = $this->rolePermissionService->getUsers();
        $roles = $this->rolePermissionService->getRoles();

        return view('admin.roles.users', compact('users', 'roles'));
    }

    /**
     * Reassign User Role
     */
    public function updateUserRole(Request $request, $id)
    {
        if (!auth()->user()->hasPermissionTo('manage_users') && !auth()->user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'role' => 'required|string|exists:roles,name',
        ]);

        $this->rolePermissionService->assignUserRole($id, $request->role);

        return redirect()->back()->with('success', 'User role reassigned successfully!');
    }

    /**
     * Store new administrative user
     */
    public function storeUser(Request $request)
    {
        if (!auth()->user()->hasPermissionTo('manage_users') && !auth()->user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|string|exists:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $this->rolePermissionService->createUserWithRoleAndPermissions($request->only('name', 'email', 'password', 'role', 'permissions'));

        return redirect()->back()->with('success', 'New Administrator account created successfully!');
    }
}
