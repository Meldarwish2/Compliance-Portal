<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
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

        // Create users and assign roles
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ],
            [
                'name' => 'Auditor User',
                'email' => 'auditor@example.com',
                'password' => Hash::make('password'),
                'role' => 'auditor',
            ],
            [
                'name' => 'Client User',
                'email' => 'client@example.com',
                'password' => Hash::make('password'),
                'role' => 'client',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => $userData['password'],
            ]);

            $user->assignRole($userData['role']);
        }
    }
}
