<?php

namespace Database\Seeders;

use App\Models\Career;
use App\Models\Company;
use App\Models\Vacancy;
use Illuminate\Database\Seeder;
use RuntimeException;

class IndustrySeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        $careers = Career::orderBy('id')->get();
        if ($careers->isEmpty()) {
            throw new RuntimeException('The career catalogue must be populated before IndustrySeeder.');
        }

        $names = [
            'Melaka Precision Engineering', 
            'Nusa Digital Solutions', 
            'Maju Automotive Services', 
            'Penang Advanced Manufacturing', 
            'Southern Electrical Systems', 
            'Ceria Software Studio', 
            'Perak Mobility Services', 
            'Selangor Industrial Technologies', 
            'Straits Electronics', 
            'Rimba Digital Labs', 
            'Wawasan Auto Care', 
            'Johor Production Systems', 
            'Northern Power Engineering', 
            'Bayu Web Technologies', 
            'Sentral Technical Services'
        ];

        $locations = [
            'Melaka', 
            'Shah Alam', 
            'George Town', 
            'Johor Bahru', 
            'Ipoh'
        ];

        foreach ($names as $index => $name) {
            $number = $index + 1;
            $career = $careers[$index % $careers->count()];
            $email = $number === 1 ? 'industry@intellimatch.test' : 'industry'.$number.'@intellimatch.test';
            $user = SeedAccount::save($email, $name, 'industry', config('seed.demo_password'));

            $company = Company::firstOrNew(['user_id' => $user->id]);
            $company->fill([
                'name' => $name, 
                'industry' => $career->sector, 
                'location' => $locations[$index % 5], 
                'description' => $name.' provides technical services and practical career opportunities for TVET graduates.'
            ]);
            $company->is_approved = true;
            $company->save();

            for ($position = 1; $position <= 3 + $index % 3; $position++) {
                $title = $career->title.' - Opening '.$position;
                
                $vacancy = Vacancy::firstOrNew([
                    'company_id' => $company->id, 
                    'title' => $title
                ]);

                $vacancy->fill([
                    'career_id' => $career->id,
                    'description' => $career->description.' Join '.$name.' with mentoring, hands-on projects and opportunities to develop your technical skills.',
                    'type' => $position === 3 ? 'internship' : 'full-time',
                    'location' => $company->location,
                    'arrangement' => $career->sector === 'Information Technology' ? 'hybrid' : 'on-site',
                    'salary_min' => $position === 3 ? 1000 : 2200 + $index * 100,
                    'salary_max' => $position === 3 ? 1500 : 3200 + $index * 100,
                    'qualifications' => 'Relevant TVET certificate or diploma. Fresh graduates are welcome to apply.',
                    'skills' => $career->skills,
                    'closing_date' => now('Asia/Kuala_Lumpur')->addDays(30 + $number + $position)->toDateString(),
                    'application_url' => 'mailto:'.$email,
                    'status' => 'published',
                ]);
                $vacancy->save();
            }
        }
    }
}
