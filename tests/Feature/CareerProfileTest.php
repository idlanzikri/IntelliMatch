<?php

namespace Tests\Feature;

use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class CareerProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_is_saved_for_authenticated_student_only(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $this->actingAs($user)->put('/student/profile', [
            'user_id' => $other->id,
            'institution' => 'Test College',
            'skills' => ['Troubleshooting', 'Safety procedures'],
        ])->assertRedirect('/dashboard');

        $this->assertDatabaseHas('student_profiles', ['user_id' => $user->id, 'institution' => 'Test College']);
        $this->assertDatabaseMissing('student_profiles', ['user_id' => $other->id]);

        $this->get('/dashboard')->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->where('careers.0.score', 50)
            ->has('careers.0.matched', 2));

        $this->actingAs($other)->get('/student/profile')->assertInertia(fn (Assert $page) => $page
            ->component('student/Profile')->where('profile', null));
    }

    public function test_unknown_and_duplicate_skills_are_rejected(): void
    {
        $this->actingAs(User::factory()->create())
            ->put('/student/profile', ['skills' => ['Made up skill']])
            ->assertSessionHasErrors('skills.0');

        $this->put('/student/profile', ['skills' => ['Troubleshooting', 'Troubleshooting']])
            ->assertSessionHasErrors('skills.0');

        $this->assertSame(0, StudentProfile::count());
    }

    public function test_guests_cannot_save_profiles(): void
    {
        $this->put('/student/profile', ['skills' => []])->assertRedirect('/login');
        $this->assertSame(0, StudentProfile::count());
    }
}
