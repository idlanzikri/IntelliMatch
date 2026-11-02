<?php

namespace Database\Seeders;

use App\Models\Institution;
use App\Models\StudentProfile;
use Illuminate\Database\Seeder;
use RuntimeException;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        $institutions = Institution::whereIn('code', array_map(fn ($n) => 'DEMO-TVET-'.$n, range(1, 5)))->orderBy('code')->get();
        if ($institutions->count() !== 5) {
            throw new RuntimeException('Run InstitutionSeeder before StudentSeeder.');
        }

        $names = [
            'Amir Hakim', 
            'Nur Aisyah', 
            'Daniel Tan', 
            'Priya Devi', 
            'Muhammad Danish', 
            'Siti Hajar', 
            'Lim Wei Jun', 
            'Kavitha Raj', 
            'Ahmad Faris', 
            'Nur Izzati', 
            'Jason Lee', 
            'Thivya Kumar', 
            'Hafiz Rahman', 
            'Siti Balqis', 
            'Chong Xin Yi', 
            'Arun Nathan', 
            'Irfan Azmi', 
            'Nur Sabrina', 
            'Ryan Wong', 
            'Meena Ravi', 
            'Adam Syafiq', 
            'Farah Nabila', 
            'Kevin Yap', 
            'Shalini Mani', 
            'Zul Ariff', 
            'Aina Sofea', 
            'Marcus Goh', 
            'Divya Suresh', 
            'Imran Faiz', 
            'Nadia Amira'];

        $programmes = [
            'Electrical Technology', 
            'Software Development', 
            'Automotive Technology', 
            'Manufacturing Technology'
        ];

        $locations = [
            'Melaka', 
            'Shah Alam', 
            'George Town', 
            'Johor Bahru', 
            'Ipoh'
        ];

        $careers = config('careers');

        foreach ($names as $index => $name) {
            $number = $index + 1;
            $institutionIndex = intdiv($index, 6);
            $institution = $institutions[$institutionIndex];
            $email = 'student'.$number.'@intellimatch.test';

            $user = SeedAccount::save($email, $name, 'student', config('seed.demo_password'));
            $user->institution_id = $institution->id;
            $user->save();
            
            $profile = StudentProfile::firstOrNew(['user_id' => $user->id]);
            $profile->fill([
                'institution' => $institution->name,
                'programme' => $programmes[$index % 4],
                'location' => $locations[$institutionIndex],
                'skills' => array_slice($careers[$index % 4]['skills'], 0, 2 + $index % 3),
                'interests' => 'Interested in practical training and a career in '.$careers[$index % 4]['sector'].'.',
            ]);
            $profile->save();
        }
    }
}

