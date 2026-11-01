<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        SeedAccount::save(config('seed.superadmin_email'), 'IntelliMatch Superadmin', 'superadmin', config('seed.superadmin_password'));
    }
}
