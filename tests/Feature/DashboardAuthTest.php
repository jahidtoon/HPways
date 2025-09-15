<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class DashboardAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_requires_auth(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login'); // default redirect
    }

    public function test_dashboard_loads_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->get('/dashboard');
        $response->assertStatus(200);
    }
}
