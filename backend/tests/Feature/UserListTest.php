<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class UserListTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_open_the_user_list(): void
    {
        $this->get('/users')->assertRedirect(route('login'));
    }

    public function test_dashboard_links_to_the_user_list(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/dashboard')
            ->assertOk()
            ->assertSee(route('users.index'));
    }

    public function test_users_are_paginated_ten_at_a_time_in_a_stable_order(): void
    {
        $users = User::factory()->count(23)->create();
        $this->actingAs($users->first());

        $this->get(route('users.index'))
            ->assertOk()
            ->assertViewIs('users.index')
            ->assertViewHas('users', function (LengthAwarePaginator $page) use ($users): bool {
                return $page->perPage() === 10
                    && $page->total() === 23
                    && $page->lastPage() === 3
                    && $page->getCollection()->pluck('id')->all() === $users->take(10)->pluck('id')->all();
            });

        $this->get(route('users.index', ['page' => 2]))
            ->assertOk()
            ->assertViewHas('users', function (LengthAwarePaginator $page) use ($users): bool {
                return $page->currentPage() === 2
                    && $page->getCollection()->pluck('id')->all() === $users->slice(10, 10)->pluck('id')->all();
            });

        $this->get(route('users.index', ['page' => 3]))
            ->assertOk()
            ->assertViewHas('users', function (LengthAwarePaginator $page) use ($users): bool {
                return $page->count() === 3
                    && ! $page->hasMorePages()
                    && $page->getCollection()->pluck('id')->all() === $users->slice(20)->pluck('id')->all();
            });
    }

    public function test_user_list_does_not_expose_other_users_email_or_password(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create(['email' => 'privado@example.test']);

        $this->actingAs($user)
            ->get(route('users.index'))
            ->assertOk()
            ->assertSee($otherUser->name)
            ->assertDontSee($otherUser->email)
            ->assertDontSee($otherUser->password)
            ->assertViewHas('users', fn (LengthAwarePaginator $users): bool => $users->every(fn (User $listedUser): bool => ! array_key_exists('email', $listedUser->getAttributes())
                && ! array_key_exists('password', $listedUser->getAttributes())));
    }
}
