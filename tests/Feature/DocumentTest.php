<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Document;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

    public function test_user_can_view_outgoing_documents()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/outgoing');
        $response->assertStatus(200);
    }

    public function test_user_can_create_incoming_document_with_file()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $file = UploadedFile::fake()->create('document.pdf', 1000, 'application/pdf');

        $response = $this->actingAs($user)->post('/incoming', [
            'document_number' => 'INC/2023/001',
            'document_date' => '2023-10-01',
            'received_date' => '2023-10-02',
            'sender_or_receiver' => 'Dinas Pendidikan',
            'subject' => 'Undangan Rapat',
            'file_document' => $file,
        ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('documents', [
            'document_number' => 'INC/2023/001',
            'type' => 'incoming',
            'subject' => 'Undangan Rapat'
        ]);
        
        $document = Document::first();
        Storage::disk('public')->assertExists($document->file_path);
    }

    public function test_user_can_create_outgoing_document()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/outgoing', [
            'document_number' => 'OUT/2023/001',
            'document_date' => '2023-10-05',
            'received_date' => '2023-10-05',
            'sender_or_receiver' => 'Kementerian Sosial',
            'subject' => 'Laporan Bulanan',
        ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('documents', [
            'document_number' => 'OUT/2023/001',
            'type' => 'outgoing',
            'subject' => 'Laporan Bulanan'
        ]);
    }
}
