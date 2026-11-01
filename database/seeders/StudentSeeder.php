<?php

namespace Database\Seeders;

use App\Models\StudentProfile;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            return;
        }
        $user = SeedAccount::save(config('seed.student_email'), 'Demo Student', 'student', config('seed.demo_password'));
        if (! StudentProfile::where('user_id', $user->id)->exists()) {
            $profile = new StudentProfile;
            $profile->user_id = $user->id;
            $profile->fill(['institution' => 'Demo TVET Institution', 'programme' => 'Electrical Technology', 'skills' => ['Troubleshooting', 'Safety procedures']]);
            $profile->save();
        }
    }
}
