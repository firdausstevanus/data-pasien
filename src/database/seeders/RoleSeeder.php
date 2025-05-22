<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create super_admin role
        $superAdminRole = Role::create(['name' => 'super_admin']);
        
        // Optional: Create other roles if needed
        // Role::create(['name' => 'admin']);
        // Role::create(['name' => 'user']);
    }
}
