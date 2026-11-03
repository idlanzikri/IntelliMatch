<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Company;
use App\Models\Institution;
use App\Models\InstitutionCourse;
use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class TvetMapTest extends TestCase
{
    use RefreshDatabase;

    public function test_pin_filters_keep_other_markers_and_include_all_records_at_the_location(): void
    {
        $user = User::factory()->create();
        $agency = Agency::create(['name' => 'Agency', 'source_id' => '1']);
        $campuses = collect();
        foreach ([101.5, 101.5, 102.5] as $index => $longitude) {
            $campuses->push(Institution::create(['name' => 'Campus '.$index, 'code' => 'PIN-'.$index, 'agency_id' => $agency->id, 'latitude' => 3, 'longitude' => $longitude]));
        }
        InstitutionCourse::create(['institution_id' => $campuses[0]->id, 'source_id' => 'PIN-COURSE', 'name' => 'Welding', 'source_url' => 'https://www.tvet.gov.my/']);
        $this->actingAs($user)->get('/training?pin='.$campuses[0]->id)->assertInertia(fn (Assert $p) => $p
            ->has('markers', 3)->has('institutions.data', 2)->where('institutions.total', 2)
            ->has('institutions.data.0.courses', 1));
        $this->get('/training?pin=99999')->assertInertia(fn (Assert $p) => $p->has('markers', 3)->has('institutions.data', 0));
        $company = new Company;
        $company->forceFill(['user_id' => $user->id, 'name' => 'Company', 'is_approved' => true])->save();
        $vacancies = collect();
        foreach ([101.5, 101.5, 102.5] as $longitude) {
            $v = new Vacancy;
            $v->forceFill(['company_id' => $company->id, 'title' => 'Technician', 'description' => 'Details', 'type' => 'internship', 'arrangement' => 'on-site', 'location' => 'Selangor', 'latitude' => 3, 'longitude' => $longitude, 'skills' => [], 'closing_date' => now()->addWeek(), 'application_url' => '', 'status' => 'published'])->save();
            $vacancies->push($v);
        }
        $this->get('/opportunities?pin='.$vacancies[0]->id)->assertInertia(fn (Assert $p) => $p
            ->has('markers', 3)->has('vacancies.data', 2)->where('vacancies.total', 2));
        $company->forceFill(['is_approved' => false])->save();
        $this->get('/opportunities?pin='.$vacancies[0]->id)->assertInertia(fn (Assert $p) => $p->has('markers', 0)->has('vacancies.data', 0));
    }

    public function test_import_is_idempotent_and_preserves_memberships_and_confirmed_pins(): void
    {
        $existing = Institution::create(['name' => 'Existing campus', 'code' => 'EXISTING']);
        $student = User::factory()->create(['institution_id' => $existing->id]);
        $this->artisan('tvet:import')->assertSuccessful();
        $campus = Institution::whereNotNull('agency_id')->firstOrFail();
        $campus->update(['latitude' => 3.1, 'longitude' => 101.6]);
        $counts = [Agency::count(), Institution::count(), InstitutionCourse::count()];
        $this->artisan('tvet:import')->assertSuccessful();
        $this->assertSame($counts, [Agency::count(), Institution::count(), InstitutionCourse::count()]);
        $this->assertSame($existing->id, $student->fresh()->institution_id);
        $this->assertEquals(3.1, $campus->fresh()->latitude);
        $this->assertSame(14, Agency::count());
    }

    public function test_directory_searches_course_names_and_only_admin_can_place_campus_pin(): void
    {
        $agency = Agency::create(['name' => 'Agency', 'source_id' => '1']);
        $campus = Institution::create(['name' => 'Campus', 'code' => 'ONE', 'agency_id' => $agency->id]);
        InstitutionCourse::create(['institution_id' => $campus->id, 'source_id' => '123', 'name' => 'Welding', 'source_url' => 'https://www.tvet.gov.my/awam-kursus/kursus?id_kursus=123']);
        $student = User::factory()->create();
        $this->actingAs($student)->get('/training?search=Welding')->assertOk()->assertInertia(fn (Assert $p) => $p->has('institutions.data', 1)->has('markers', 0));
        $this->put('/training/'.$campus->id.'/location', ['latitude' => 3, 'longitude' => 101])->assertForbidden();
        $student->forceFill(['is_admin' => true])->save();
        $this->put('/training/'.$campus->id.'/location', ['latitude' => 91, 'longitude' => 101])->assertSessionHasErrors('latitude');
        $this->put('/training/'.$campus->id.'/location', ['latitude' => 3, 'longitude' => 101])->assertRedirect();
        $this->get('/training')->assertInertia(fn (Assert $p) => $p->has('markers', 1));
    }

    public function test_workplace_publication_validation_and_map_visibility(): void
    {
        $employer = User::factory()->create(['role' => 'industry']);
        $company = new Company;
        $company->forceFill(['user_id' => $employer->id, 'name' => 'Employer', 'is_approved' => true])->save();
        $payload = ['title' => 'Technician', 'description' => 'Work', 'type' => 'internship', 'arrangement' => 'on-site', 'location' => 'Melaka', 'skills' => ['Welding'], 'closing_date' => now()->addWeek()->toDateString(), 'status' => 'published'];
        $this->actingAs($employer)->post('/industry/vacancies', $payload)->assertSessionHasErrors('latitude');
        $this->post('/industry/vacancies', [...$payload, 'latitude' => 2.2])->assertSessionHasErrors('longitude');
        $this->post('/industry/vacancies', [...$payload, 'latitude' => 2.2, 'longitude' => 102.2])->assertRedirect();
        $this->get('/opportunities')->assertInertia(fn (Assert $p) => $p->has('markers', 1));
        $company->forceFill(['is_approved' => false])->save();
        $this->get('/opportunities')->assertInertia(fn (Assert $p) => $p->has('markers', 0));
        $company->forceFill(['is_approved' => true])->save();
        $this->post('/industry/vacancies', [...$payload, 'arrangement' => 'remote', 'latitude' => 2.2, 'longitude' => 102.2])->assertRedirect();
        $this->assertNull(Vacancy::latest('id')->first()->latitude);
    }
}
