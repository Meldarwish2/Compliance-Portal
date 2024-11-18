<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
//        // Create roles
//        $admin = Role::create(['name' => 'admin']);
//        $auditor = Role::create(['name' => 'auditor']);
//        $client = Role::create(['name' => 'client']);
//
//        // Create permissions
//        Permission::create(['name' => 'assign projects']);
//        Permission::create(['name' => 'upload evidence']);
//        Permission::create(['name' => 'upload statements']);
//        Permission::create(['name' => 'approve evidence']);
//        Permission::create(['name' => 'reject evidence']);
//
//        // Assign permissions to roles
//        $admin->givePermissionTo(['assign projects']);
//        $auditor->givePermissionTo(['upload statements', 'approve evidence', 'reject evidence']);
//        $client->givePermissionTo(['upload evidence']);
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions
        $permissions = [
            'upload evidence',
            'upload statements',
            'approve evidence',
            'reject evidence',
            'Add User',
            'Edit User',
            'Delete user',
            'create project',
            'edit project',
            'delete project',
            'assign project',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Define roles and assign permissions
        $roles = [
            'admin' => [
                'upload evidence',
                'upload statements',
                'approve evidence',
                'reject evidence',
                'Add User',
                'Edit User',
                'Delete user',
                'create project',
                'edit project',
                'delete project',
                'assign project',
            ],
            'auditor' => [
                'upload statements',
                'approve evidence',
                'reject evidence',
            ],
            'client' => [
                'upload evidence',
                'upload statements',
            ],
        ];

        // Create roles and assign permissions
        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::create(['name' => $roleName]);
            foreach ($rolePermissions as $permission) {
                $role->givePermissionTo($permission);
            }
        }
    }
}
