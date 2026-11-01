<?php

namespace Tests\Feature;

use App\Models\Career;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_students_cannot_read_or_write_admin_endpoints(): void
    {
        $this->actingAs(User::factory()->create());
        foreach (['/admin', '/admin/students', '/admin/careers'] as $url) {
            $this->get($url)->assertForbidden();
        }
        $this->post('/admin/careers', $this->careerData())->assertForbidden();
        $this->put('/admin/careers/1', $this->careerData())->assertForbidden();
        $this->assertSame(4, Career::count());
    }

    public function test_admin_can_create_update_and_publish_careers(): void
    {
        $admin = User::factory()->create();
        $admin->is_admin = true;
        $admin->save();
        $this->actingAs($admin)->get('/admin')->assertOk();
        $this->post('/admin/careers', $this->careerData())->assertRedirect('/admin/careers');
        $career = Career::where('title', 'Test technician')->firstOrFail();
        $this->get('/dashboard')->assertInertia(fn (Assert $page) => $page->has('careers', 4));
        $this->put('/admin/careers/'.$career->id, [...$this->careerData(), 'is_active' => true])->assertRedirect();
        $this->get('/dashboard')->assertInertia(fn (Assert $page) => $page->has('careers', 5));
        $this->put('/student/profile', ['skills' => ['New practical skill']])->assertRedirect('/dashboard');
        $this->get('/dashboard')->assertInertia(fn (Assert $page) => $page->where('careers.0.title', 'Test technician')->where('careers.0.score', 100));
    }

    public function test_admin_search_and_validation(): void
    {
        $admin = User::factory()->create();
        $admin->is_admin = true;
        $admin->save();
        User::factory()->create(['name' => 'Searchable Student']);
        $this->actingAs($admin)->get('/admin/students?search=Searchable')->assertInertia(fn (Assert $page) => $page->has('students.data', 1));
        $this->post('/admin/careers', [...$this->careerData(), 'skills' => []])->assertSessionHasErrors('skills');
    }

    public function test_registration_cannot_grant_admin_privileges(): void
    {
        $this->post('/register', [
            'name' => 'Student', 'email' => 'student@example.com',
            'password' => 'A-long-password-123!', 'password_confirmation' => 'A-long-password-123!',
            'is_admin' => true,
        ]);
        $this->assertFalse(User::where('email', 'student@example.com')->firstOrFail()->is_admin);
    }

    private function careerData(): array
    {
        return ['title' => 'Test technician', 'sector' => 'Testing', 'description' => 'A test career.', 'skills' => ['New practical skill'], 'is_active' => false];
    }
}
