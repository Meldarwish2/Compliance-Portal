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
        // Create roles
        $admin = Role::create(['name' => 'admin']);
        $auditor = Role::create(['name' => 'auditor']);
        $client = Role::create(['name' => 'client']);

        // Create permissions
        Permission::create(['name' => 'assign projects']);
        Permission::create(['name' => 'upload evidence']);
        Permission::create(['name' => 'upload statements']);
        Permission::create(['name' => 'approve evidence']);
        Permission::create(['name' => 'reject evidence']);

        // Assign permissions to roles
        $admin->givePermissionTo(['assign projects']);
        $auditor->givePermissionTo(['upload statements', 'approve evidence', 'reject evidence']);
        $client->givePermissionTo(['upload evidence']);
    }
}
