<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run()
    {
        // Create Permissions
        Permission::create(['name' => 'view users']);
        Permission::create(['name' => 'edit users']);
        Permission::create(['name' => 'delete users']);

        // Create Roles and Assign Permissions
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo(['view users', 'edit users', 'delete users']);

        $editor = Role::create(['name' => 'editor']);
        $editor->givePermissionTo(['view users', 'edit users']);

        $viewer = Role::create(['name' => 'viewer']);
        $viewer->givePermissionTo(['view users']);
    }

}
