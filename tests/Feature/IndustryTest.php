<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class IndustryTest extends TestCase
{
    use RefreshDatabase;

    public function test_approval_publication_and_student_visibility(): void
    {
        [$employer, $company] = $this->employer();
        $this->actingAs($employer)->post('/industry/vacancies', $this->data('published'))->assertSessionHasErrors('status');
        $this->post('/industry/vacancies', $this->data('draft'))->assertRedirect();
        $vacancy = Vacancy::firstOrFail();
        $this->get('/opportunities')->assertInertia(fn (Assert $page) => $page->has('vacancies.data', 0));
        $admin = User::factory()->create();
        $admin->is_admin = true;
        $admin->save();
        $this->actingAs($admin)->put('/admin/companies/'.$company->id, ['is_approved' => true])->assertRedirect();
        $this->actingAs($employer)->put('/industry/vacancies/'.$vacancy->id, $this->data('published'))->assertRedirect();
        $this->get('/opportunities')->assertInertia(fn (Assert $page) => $page->has('vacancies.data', 1));
        $vacancy->closing_date = now()->subDay()->toDateString();
        $vacancy->save();
        $this->get('/opportunities')->assertInertia(fn (Assert $page) => $page->has('vacancies.data', 0));
        $this->put('/industry/vacancies/'.$vacancy->id, $this->data('published'))->assertRedirect();
        $this->actingAs($admin)->put('/admin/companies/'.$company->id, ['is_approved' => false])->assertRedirect();
        $this->get('/opportunities')->assertInertia(fn (Assert $page) => $page->has('vacancies.data', 0));
    }

    public function test_ownership_and_role_boundaries_and_url_validation(): void
    {
        [$one, $company] = $this->employer();
        [$two] = $this->employer();
        $this->actingAs($one)->post('/industry/vacancies', $this->data('draft'))->assertRedirect();
        $vacancy = Vacancy::firstOrFail();
        $this->actingAs($two)->put('/industry/vacancies/'.$vacancy->id, $this->data('draft'))->assertNotFound();
        $this->put('/admin/companies/'.$company->id, ['is_approved' => true])->assertForbidden();
        $this->post('/industry/vacancies', [...$this->data('draft'), 'application_url' => 'javascript:alert(1)'])->assertSessionHasErrors('application_url');
        $this->actingAs(User::factory()->create())->get('/industry')->assertForbidden();
        $this->post('/industry/vacancies', $this->data('draft'))->assertForbidden();
    }

    public function test_registration_creates_only_unprivileged_employer_and_profile_changes_revoke_approval(): void
    {
        $this->post('/register', ['name' => 'Employer', 'email' => 'employer@example.com', 'password' => 'LongPassword!123', 'password_confirmation' => 'LongPassword!123', 'account_type' => 'industry', 'is_admin' => true, 'role' => 'superadmin']);
        $user = User::where('email', 'employer@example.com')->firstOrFail();
        $this->assertSame('industry', $user->role);
        $this->assertFalse($user->is_admin);
        [$employer, $company] = $this->employer();
        $company->is_approved = true;
        $company->save();
        $this->actingAs($employer)->put('/industry/company', ['name' => 'Updated company', 'industry' => 'IT', 'location' => 'Melaka', 'description' => 'Updated description', 'is_approved' => true])->assertRedirect();
        $this->assertFalse($company->fresh()->is_approved);
        $this->get('/dashboard')->assertRedirect('/industry');
    }

    private function employer(): array
    {
        $user = User::factory()->create();
        $user->role = 'industry';
        $user->save();
        $company = new Company;
        $company->user_id = $user->id;
        $company->name = 'Test company';
        $company->save();

        return [$user, $company];
    }

    private function data(string $status): array
    {
        return ['title' => 'Technician', 'description' => 'Maintain equipment.', 'type' => 'full-time', 'arrangement' => 'on-site', 'location' => 'Melaka', 'skills' => ['Troubleshooting'], 'closing_date' => now()->addDays(7)->toDateString(), 'application_url' => 'https://example.com/apply', 'status' => $status];
    }
}
