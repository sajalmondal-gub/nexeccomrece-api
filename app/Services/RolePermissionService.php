<?php

namespace App\Services;

use App\Repositories\Contracts\RolePermissionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class RolePermissionService
{
    protected $rolePermissionRepository;

    public function __construct(RolePermissionRepositoryInterface $rolePermissionRepository)
    {
        $this->rolePermissionRepository = $rolePermissionRepository;
    }

    public function getRoles(): Collection
    {
        return $this->rolePermissionRepository->allRoles();
    }

    public function getPermissions(): Collection
    {
        return $this->rolePermissionRepository->allPermissions();
    }

    public function createRoleWithPermissions(string $name, array $permissions): void
    {
        $role = $this->rolePermissionRepository->createRole($name);
        $role->syncPermissions($permissions);
    }

    public function updateRolePermissions(int $roleId, array $permissions): void
    {
        $role = $this->rolePermissionRepository->findRole($roleId);
        if ($role) {
            $role->syncPermissions($permissions);
        }
    }

    public function deleteRole(int $roleId): bool
    {
        return $this->rolePermissionRepository->deleteRole($roleId);
    }

    public function getUsers(): Collection
    {
        return $this->rolePermissionRepository->allUsers();
    }

    public function assignUserRole(int $userId, string $roleName): void
    {
        $user = $this->rolePermissionRepository->findUser($userId);
        if ($user) {
            // Sync user roles (clears existing and assigns new)
            $user->syncRoles([$roleName]);
        }
    }

    public function createUserWithRoleAndPermissions(array $data)
    {
        return $this->rolePermissionRepository->createUserWithRoleAndPermissions($data);
    }
}
