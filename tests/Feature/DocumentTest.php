<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_incoming_documents()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/incoming');
        $response->assertStatus(200);
    }

    public function test_user_can_view_incoming_create_page()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/incoming/create');
        $response->assertStatus(200);
    }

    public function test_user_can_view_outgoing_documents()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/outgoing');
        $response->assertStatus(200);
    }

    public function test_user_can_view_outgoing_create_page()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/outgoing/create');
        $response->assertStatus(200);
    }

    public function test_guest_cannot_view_incoming_documents()
    {
        $response = $this->get('/incoming');
        $response->assertRedirect('/login');
    }

    public function test_guest_cannot_view_outgoing_documents()
    {
        $response = $this->get('/outgoing');
        $response->assertRedirect('/login');
    }
}
