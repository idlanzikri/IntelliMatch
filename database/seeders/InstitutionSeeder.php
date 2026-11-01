<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class InstitutionSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            return;
        }
        SeedAccount::save(config('seed.institution_email'), 'Demo TVET Institution', 'institution', config('seed.demo_password'));
    }
}
