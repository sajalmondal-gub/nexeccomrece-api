<?php

namespace App\Repositories\Eloquent;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Repositories\Contracts\RolePermissionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

use Illuminate\Support\Facades\Hash;

class RolePermissionRepository implements RolePermissionRepositoryInterface
{
    public function allRoles(): Collection
    {
        return Role::with('permissions')->get();
    }

    public function allPermissions(): Collection
    {
        return Permission::all();
    }

    public function findRole(int $id): ?Role
    {
        return Role::findById($id);
    }

    public function createRole(string $name): Role
    {
        return Role::create(['name' => $name]);
    }

    public function deleteRole(int $id): bool
    {
        $role = $this->findRole($id);
        if ($role) {
            // Guard against deleting core seeding roles for safety
            if (in_array($role->name, ['Super Admin', 'Admin', 'Customer'])) {
                return false;
            }
            return $role->delete();
        }
        return false;
    }

    public function allUsers(): Collection
    {
        return User::with('roles')->get();
    }

    public function findUser(int $id): ?User
    {
        return User::find($id);
    }

    public function createUserWithRoleAndPermissions(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $user->assignRole($data['role']);

        if (!empty($data['permissions'])) {
            $user->givePermissionTo($data['permissions']);
        }

        return $user;
    }
}
