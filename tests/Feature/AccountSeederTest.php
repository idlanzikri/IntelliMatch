<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccountSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeders_are_repeatable_reset_passwords_and_assign_access(): void
    {
        $user = User::factory()->unverified()->create(['email' => 'idlan.skyrem@gmail.com', 'password' => 'ExistingPassword!123']);
        config(['seed.demo_password' => 'P@ssw0rd', 'seed.superadmin_password' => 'P@ssw0rd']);
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);
        $this->assertSame(51, User::count());
        $user->refresh();
        $this->assertTrue(Hash::check('P@ssw0rd', $user->password));
        $this->assertSame('superadmin', $user->role);
        $this->assertTrue($user->is_admin);
        $this->assertNotNull($user->email_verified_at);
        $this->actingAs($user)->get('/admin')->assertOk();
        foreach (['student', 'institution'] as $role) {
            $account = User::where('role', $role)->firstOrFail();
            $this->assertTrue(Hash::check('P@ssw0rd', $account->password));
            $this->assertFalse($account->is_admin);
            $this->assertNotNull($account->email_verified_at);
            $this->actingAs($account)->get('/admin')->assertForbidden();
        }
    }
}
