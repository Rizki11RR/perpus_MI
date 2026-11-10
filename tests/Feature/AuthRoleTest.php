<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_username(): void
    {
        $user = User::factory()->create(['username' => 'petugas', 'password' => Hash::make('secret')]);
        $this->post('/login', ['username' => 'petugas', 'password' => 'secret'])->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    public function test_inactive_user_cannot_login(): void
    {
        User::factory()->create(['username' => 'nonaktif', 'password' => Hash::make('secret'), 'is_active' => false]);
        $this->from('/login')->post('/login', ['username' => 'nonaktif', 'password' => 'secret'])->assertRedirect('/login')->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    public function test_petugas_cannot_open_admin_route(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'petugas']))->get('/admin-only')->assertForbidden();
    }

    public function test_admin_can_open_admin_route(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']))->get('/admin-only')->assertOk();
    }
}
