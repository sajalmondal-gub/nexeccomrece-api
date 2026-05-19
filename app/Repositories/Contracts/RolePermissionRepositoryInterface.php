<?php

namespace App\Repositories\Contracts;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface RolePermissionRepositoryInterface
{
    public function allRoles(): Collection;

    public function allPermissions(): Collection;

    public function findRole(int $id): ?Role;

    public function createRole(string $name): Role;

    public function deleteRole(int $id): bool;

    public function allUsers(): Collection;

    public function findUser(int $id): ?User;

    public function createUserWithRoleAndPermissions(array $data): User;
}
