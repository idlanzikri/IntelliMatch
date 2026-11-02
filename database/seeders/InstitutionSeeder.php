<?php

namespace Database\Seeders;

use App\Models\Institution;
use App\Models\User;
use Illuminate\Database\Seeder;

class InstitutionSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            return;
        }
        $institution = Institution::firstOrCreate(['code' => 'DEMO-TVET'], ['name' => 'Demo TVET Institution']);
        $staff = SeedAccount::save(config('seed.institution_email'), 'Demo TVET Institution', 'institution', config('seed.demo_password'));
        $staff->institution_id = $institution->id;
        $staff->save();
        $student = User::where('email', config('seed.student_email'))->where('role', 'student')->first();
        if ($student && $student->institution_id === null) {
            $student->institution_id = $institution->id;
            $student->save();
        }
    }
}
