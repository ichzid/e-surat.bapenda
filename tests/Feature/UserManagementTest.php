<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_users_list()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        $response = $this->actingAs($admin)->get('/users');
        
        $response->assertStatus(200);
    }

    public function test_regular_user_cannot_view_users_list()
    {
        $user = User::factory()->create(['role' => 'operator']);
        
        $response = $this->actingAs($user)->get('/users');
        
        $response->assertStatus(403);
    }

    public function test_guest_cannot_view_users_list()
    {
        $response = $this->get('/users');
        
        $response->assertRedirect('/login');
    }
}
