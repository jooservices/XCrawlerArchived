<?php

namespace Tests\Unit\User;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Tests\TestCase;

class UserRolePermissionTest extends TestCase
{
    public function testRoleAndPermissions(): void
    {
        $profileView = Permission::create(['name' => 'profile-view']);
        $profileEdit = Permission::create(['name' => 'profile-edit']);
        $createUser = Permission::create(['name' => 'user-create']);
        $deleteUser = Permission::create(['name' => 'user-delete']);

        $adminRole = Role::create(['name' => 'admin']);
        $userRole = Role::create(['name' => 'user']);

        $adminRole->attachPermissions([$profileView, $profileEdit, $createUser, $deleteUser]);
        $userRole->attachPermissions([$profileView, $profileEdit]);

        /** @var User $admin */
        $admin = User::Factory()->create();
        $admin->attachRole($adminRole);
        $this->assertTrue($admin->isAbleTo(['profile-view', 'profile-edit', 'user-create', 'user-delete'], null, true));
        $this->assertFalse($admin->isAbleTo(['profile-view', 'profile-edit', 'user-create', 'user-delete', 'foo'], null, true));

        /** @var User $user */
        $user = User::Factory()->create()->attachRole($userRole);
        $this->assertTrue($admin->isAbleTo(['profile-view', 'profile-edit'], null, true));
        $this->assertFalse($user->hasPermission(['user-create', 'user-edit']));
    }
}
