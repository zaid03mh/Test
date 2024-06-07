<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Ensure the table is empty before seeding
        Role::truncate();

        Role::create(['name' => 'admin']);
        Role::create(['name' => 'user']);
    }
}

