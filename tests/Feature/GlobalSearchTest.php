<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Document;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GlobalSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_search_documents()
    {
        $user = User::factory()->create();
        
        Document::create([
            'type' => 'incoming',
            'reference_number' => 'REF/123',
            'document_number' => 'DOC/123',
            'document_date' => '2023-10-01',
            'received_date' => '2023-10-02',
            'sender_or_receiver' => 'Test Sender',
            'subject' => 'Keyword123 Document',
        ]);

        $response = $this->actingAs($user)->get('/search?q=Keyword123');
        
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'title' => 'Keyword123 Document'
        ]);
    }

    public function test_empty_search_returns_empty_array()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get('/search?q=');
        
        $response->assertStatus(200);
        $response->assertExactJson([]);
    }
}
