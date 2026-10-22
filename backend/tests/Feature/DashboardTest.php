<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_open_the_dashboard(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/dashboard')
            ->assertOk()
            ->assertViewIs('dashboard');
    }

    public function test_dashboard_shows_only_the_current_users_account_data(): void
    {
        $user = User::factory()->create([
            'name' => 'Ana López',
            'email' => 'ana@example.test',
        ]);
        User::factory()->create([
            'email' => 'privado@example.test',
        ]);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Ana López')
            ->assertSee('ana@example.test')
            ->assertDontSee('privado@example.test');
    }
}
