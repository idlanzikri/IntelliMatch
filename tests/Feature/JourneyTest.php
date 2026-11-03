<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Career;
use App\Models\Company;
use App\Models\StudentProfile;
use App\Models\User;
use App\Models\Vacancy;
use App\Services\ApplicantMatch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class JourneyTest extends TestCase
{
    use RefreshDatabase;

    public function test_applications_are_unique_and_scoped_to_the_employer(): void
    {
        $student = User::factory()->create();
        $student->role = 'student';
        $student->save();
        $profile = new StudentProfile;
        $profile->user_id = $student->id;
        $profile->skills = ['Troubleshooting'];
        $profile->qualification_level = 'diploma';
        $profile->save();
        $employer = User::factory()->create();
        $employer->role = 'industry';
        $employer->save();
        $company = new Company;
        $company->user_id = $employer->id;
        $company->name = 'Test';
        $company->is_approved = true;
        $company->save();
        $vacancy = new Vacancy;
        $vacancy->company_id = $company->id;
        $vacancy->fill(['title' => 'Technician', 'description' => 'Repair equipment', 'type' => 'full-time', 'arrangement' => 'on-site', 'location' => 'Melaka', 'skills' => ['Troubleshooting', 'Safety'], 'qualification_level' => 'certificate', 'closing_date' => now()->addDays(10), 'application_url' => '', 'status' => 'published'])->save();
        $this->actingAs($student)->get('/opportunities?search=Technician&location=Melaka')->assertInertia(fn (Assert $page) => $page->has('vacancies.data', 1)->where('vacancies.per_page', 10)->where('vacancies.data.0.match.score', 65)->where('vacancies.data.0.distance_km', null));
        $vacancy->update(['latitude' => 2.1896, 'longitude' => 102.2501]);
        $this->get('/opportunities?origin=melaka-0')->assertInertia(fn (Assert $page) => $page
            ->where('distanceOrigin', 'melaka-0')->where('vacancies.data.0.distance_km', 0)
            ->where('vacancies.data.0.match.score', 65));
        $this->get('/opportunities?origin=johor-0')->assertInertia(fn (Assert $page) => $page
            ->where('vacancies.data.0.distance_km', fn ($distance) => $distance > 100)
            ->where('vacancies.data.0.match.score', 65));
        $this->get('/opportunities?origin=invalid-location')->assertSessionHasErrors('origin');
        $this->get('/opportunities?search=NoMatchingJob')->assertInertia(fn (Assert $page) => $page->has('vacancies.data', 0));
        $this->actingAs($student)->post('/opportunities/'.$vacancy->id.'/apply')->assertRedirect();
        $this->post('/opportunities/'.$vacancy->id.'/apply')->assertRedirect();
        $this->assertDatabaseCount('applications', 1);
        $this->get('/industry/vacancies/'.$vacancy->id.'/applicants')->assertNotFound();
        $this->actingAs($employer)->get('/industry/vacancies/'.$vacancy->id.'/applicants')->assertInertia(fn (Assert $page) => $page->has('applicants', 1)->where('applicants.0.match.score', 65));
        $application = Application::first();
        $this->put('/industry/vacancies/'.$vacancy->id.'/applicants/'.$application->id, ['status' => 'shortlisted'])->assertRedirect();
        $this->assertSame('shortlisted', $application->fresh()->status);
        $other = User::factory()->create();
        $other->role = 'industry';
        $other->save();
        $this->actingAs($other)->put('/industry/vacancies/'.$vacancy->id.'/applicants/'.$application->id, ['status' => 'rejected'])->assertNotFound();
        $vacancy->status = 'closed';
        $vacancy->save();

        $this->get('/opportunities?search=NoMatchingJob')->assertInertia(fn (Assert $page) => $page->has('vacancies.data', 0));
        $this->actingAs($student)->post('/opportunities/'.$vacancy->id.'/apply')->assertNotFound();
    }

    public function test_career_plan_does_not_grant_skills(): void
    {
        $student = User::factory()->create();
        $student->role = 'student';
        $student->save();
        $career = Career::firstOrFail();
        $this->actingAs($student)->put('/student/career-paths', ['target_career_id' => $career->id, 'completed_steps' => [$career->skills[0]]])->assertRedirect();
        $profile = StudentProfile::where('user_id', $student->id)->firstOrFail();
        $this->assertEquals($career->id, $profile->target_career_id);
        $this->assertEmpty($profile->skills);
        $this->put('/student/career-paths', ['target_career_id' => $career->id, 'completed_steps' => ['invented']])->assertUnprocessable();
    }

    public function test_missing_qualification_is_explicit(): void
    {
        $vacancy = new Vacancy;
        $vacancy->skills = ['Safety'];
        $profile = new StudentProfile;
        $profile->skills = ['safety'];
        $match = (new ApplicantMatch)->score($profile, $vacancy);
        $this->assertSame(70, $match['score']);
        $this->assertNull($match['qualification_score']);
    }
}
