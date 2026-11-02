<?php

namespace Database\Seeders;

use App\Models\Institution;
use Illuminate\Database\Seeder;

class InstitutionSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        $names = [
            'Melaka Technical Institute', 
            'Selangor Skills College', 
            'Penang Technology Institute', 
            'Johor Vocational College', 
            'Perak Technical College'
        ];
        
        foreach ($names as $index => $name) {
            $number = $index + 1;
            $institution = Institution::updateOrCreate(['code' => 'DEMO-TVET-'.$number], ['name' => $name]);
            $email = 'institution'.$number.'@intellimatch.test';
            $staff = SeedAccount::save($email, $name, 'institution', config('seed.demo_password'));
            $staff->institution_id = $institution->id;
            $staff->save();
        }
    }
}

