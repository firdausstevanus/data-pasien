<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\DokterSeeder;
use Database\Seeders\PasienSeeder;
use Database\Seeders\RekamMedisSeeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run role seeder first to create the roles
        $this->call([
            RoleSeeder::class,
            DokterSeeder::class,
            PasienSeeder::class,
            RekamMedisSeeder::class,
        ]);

        // User::factory(10)->create();

        $user = \App\Models\User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
        ]);

        $user->assignRole('super_admin');
    }
}
