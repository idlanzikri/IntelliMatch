<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class IndustrySeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            return;
        }
        $user = SeedAccount::save('industry@intellimatch.test', 'Demo Employer', 'industry', config('seed.demo_password'));
        if (! Company::where('user_id', $user->id)->exists()) {
            $company = new Company;
            $company->user_id = $user->id;
            $company->fill(['name' => 'Demo Industry Partner', 'industry' => 'Manufacturing', 'location' => 'Melaka', 'description' => 'Demo company profile for reviewing the employer workflow.']);
            $company->save();
        }
    }
}
