<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Institution;
use App\Models\User;
use App\Models\Vacancy;
use App\Support\DemoLocation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoLocationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_city_pool_is_within_state_and_stable(): void
    {
        $locations = [];
        foreach (range(1, 30) as $id) {
            $point = DemoLocation::for('Shah Alam', 'company:'.$id);
            $this->assertSame($point, DemoLocation::for('SELANGOR', 'company:'.$id));
            $this->assertContains($point['city'], array_column(config('demo_cities.selangor'), 0));
            $locations[$point['city']] = true;
        }
        $this->assertGreaterThan(1, count($locations));
        $this->assertNull(DemoLocation::for('Unknown', 'one'));
        foreach (config('demo_cities') as $cities) {
            $this->assertGreaterThanOrEqual(5, count($cities));
            $this->assertLessThanOrEqual(10, count($cities));
        }
    }

    public function test_vacancies_inherit_company_pins_and_exact_pins_survive_reruns(): void
    {
        $company = new Company;
        $company->forceFill(['user_id' => User::factory()->create()->id, 'name' => 'Demo', 'location' => 'Melaka'])->save();
        $institution = Institution::create(['code' => 'TEST', 'name' => 'Campus', 'state' => 'SABAH']);
        $exact = Institution::create(['code' => 'EXACT', 'name' => 'Exact', 'state' => 'SABAH', 'latitude' => 5.9, 'longitude' => 116.1]);
        $vacancy = new Vacancy;
        $vacancy->forceFill(['company_id' => $company->id, 'title' => 'Technician', 'description' => 'Demo', 'type' => 'internship', 'location' => 'Different city', 'arrangement' => 'on-site', 'skills' => [], 'closing_date' => now()->addWeek(), 'application_url' => '', 'status' => 'draft'])->save();
        $this->artisan('tvet:seed-demo-locations')->assertSuccessful();
        $company->refresh();
        $vacancy->refresh();
        $institution->refresh();
        $this->assertTrue($company->location_is_approximate);
        $this->assertTrue($institution->location_is_approximate);
        $this->assertSame($company->latitude, $vacancy->latitude);
        $this->assertSame($company->longitude, $vacancy->longitude);
        $original = [$company->latitude, $company->longitude];
        $this->artisan('tvet:seed-demo-locations')->assertSuccessful();
        $this->assertSame($original, [$company->fresh()->latitude, $company->fresh()->longitude]);
        $this->assertSame(5.9, $exact->fresh()->latitude);
        $this->assertFalse($exact->fresh()->location_is_approximate);
    }

    public function test_new_vacancies_default_to_company_demo_coordinates(): void
    {
        $employer = User::factory()->create(['role' => 'industry']);
        $company = new Company;
        $company->forceFill(['user_id' => $employer->id, 'name' => 'Demo', 'location' => 'Melaka', 'is_approved' => true, 'latitude' => 2.3, 'longitude' => 102.4, 'location_is_approximate' => true])->save();
        $this->actingAs($employer)->post('/industry/vacancies', [
            'title' => 'Technician', 'description' => 'Demo', 'type' => 'internship',
            'location' => 'Melaka', 'arrangement' => 'on-site', 'skills' => ['Welding'],
            'closing_date' => now()->addWeek()->toDateString(), 'status' => 'published',
        ])->assertSessionHasNoErrors()->assertRedirect();
        $vacancy = Vacancy::firstOrFail();
        $this->assertSame($company->latitude, $vacancy->latitude);
        $this->assertSame($company->longitude, $vacancy->longitude);
        $this->assertTrue($vacancy->location_is_approximate);
    }
}
