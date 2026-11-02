<?php

namespace Tests\Feature;

use App\Models\Institution;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class InstitutionTest extends TestCase
{
    use RefreshDatabase;

    public function test_institution_is_scoped_and_cannot_open_other_students(): void
    {
        $one = Institution::create(['name' => 'One', 'code' => 'ONE']);
        $two = Institution::create(['name' => 'Two', 'code' => 'TWO']);
        $staff = User::factory()->create();
        $staff->role = 'institution';
        $staff->institution_id = $one->id;
        $staff->save();
        $own = User::factory()->create();
        $own->institution_id = $one->id;
        $own->save();
        $other = User::factory()->create();
        $other->institution_id = $two->id;
        $other->save();
        $profile = new StudentProfile;
        $profile->user_id = $own->id;
        $profile->skills = ['Troubleshooting'];
        $profile->save();
        $this->actingAs($staff)->get('/institution')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('institution/Overview')->where('stats.students', 1)->has('students.data', 1)->where('students.data.0.id', $own->id));
        $this->get('/institution/students/'.$own->id)->assertInertia(fn (Assert $page) => $page
            ->component('institution/Student')->where('student.id', $own->id)->has('careers', 4));
        $this->get('/institution/students/'.$other->id)->assertNotFound();
        $this->get('/institution?search='.$other->email)->assertInertia(fn (Assert $page) => $page->has('students.data', 0));
        $this->get('/dashboard')->assertRedirect('/institution');
        $this->get('/admin')->assertForbidden();
    }

    public function test_student_and_unlinked_staff_cannot_enter_institution_workspace(): void
    {
        $this->get('/institution')->assertRedirect('/login');
        $student = User::factory()->create();
        $this->actingAs($student)->get('/institution')->assertForbidden();
        $this->put('/student/profile', ['skills' => [], 'institution_id' => 999])->assertRedirect();
        $this->assertNull($student->fresh()->institution_id);
        $student->role = 'institution';
        $student->save();
        $this->get('/institution')->assertForbidden();
    }
}

