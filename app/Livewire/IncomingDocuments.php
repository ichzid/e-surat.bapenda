<?php

namespace App\Livewire;

use App\Models\Document;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class IncomingDocuments extends Component
{
    use WithFileUploads, WithPagination;

    public $search = '';
    public $perPage = 10;

    public $showCreateModal = false;
    public $showEditModal = false;
    public $editId;
    public $showDeleteModal = false;
    public $deleteId;

    public $document_number;
    public $document_date;
    public $received_date;
    public $sender_or_receiver;
    public $subject;
    public $file_document;
    public $current_file_path;
    public $reference_number;
    public $requires_disposition = true;

    public function mount()
    {
        $this->resetForm();
    }

    public function render()
    {
        $query = Document::query()->where('type', 'incoming');

        if (auth()->user()->role === 'operator' && !auth()->user()->isSekretariatOperator()) {
            $query->where('created_by', auth()->id());
        }

        if (!empty(trim($this->search))) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhere('document_number', 'like', "%{$search}%")
                  ->orWhere('sender_or_receiver', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        $documents = $query->orderBy('id', 'desc')->paginate($this->perPage);

        return view('livewire.incoming-documents', [
            'documents' => $documents,
            'canManageIncoming' => $this->canManageIncoming(),
        ]);
    }

    public function create()
    {
        abort_unless($this->canManageIncoming(), 403);
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function store()
    {
        abort_unless($this->canManageIncoming(), 403);

        $validated = $this->validate([
            'document_number' => 'required|string|max:255',
            'document_date' => 'required|date',
            'received_date' => 'required|date',
            'sender_or_receiver' => 'required|string|max:255',
            'subject' => 'required|string',
            'requires_disposition' => 'required|boolean',
            'file_document' => 'required|file|mimes:pdf|max:2048',
        ]);

        $filePath = null;
        if ($this->file_document) {
            $file = $this->file_document;
            $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('documents', $filename, 'public');
        }

        Document::create([
            'type' => 'incoming',
            'document_number' => $validated['document_number'],
            'document_date' => $validated['document_date'],
            'received_date' => $validated['received_date'],
            'sender_or_receiver' => $validated['sender_or_receiver'],
            'subject' => $validated['subject'],
            'file_path' => $filePath,
            'status' => $validated['requires_disposition'] ? 'menunggu_disposisi' : 'selesai',
            'department_id' => auth()->user()->department_id,
            'created_by' => auth()->id(),
        ]);

        $this->showCreateModal = false;
        $this->resetForm();
        $this->dispatch('toast', type: 'success', message: 'Surat Masuk berhasil ditambahkan.');
    }

    public function edit($id)
    {
        abort_unless($this->canManageIncoming(), 403);
        $this->resetForm();
        $this->editId = $id;

        $document = Document::where('type', 'incoming')->findOrFail($id);

        $this->document_number = $document->document_number;
        $this->document_date = $document->document_date->format('Y-m-d');
        $this->received_date = $document->received_date->format('Y-m-d');
        $this->sender_or_receiver = $document->sender_or_receiver;
        $this->subject = $document->subject;
        $this->reference_number = $document->reference_number;
        $this->requires_disposition = $document->status !== 'selesai';

        $this->showEditModal = true;
    }

    public function update()
    {
        abort_unless($this->canManageIncoming(), 403);

        $validated = $this->validate([
            'document_number' => 'required|string|max:255',
            'document_date' => 'required|date',
            'received_date' => 'required|date',
            'sender_or_receiver' => 'required|string|max:255',
            'subject' => 'required|string',
            'requires_disposition' => 'required|boolean',
            'file_document' => 'nullable|file|mimes:pdf|max:3072',
        ]);

        $document = Document::where('type', 'incoming')->findOrFail($this->editId);

        $updateData = [
            'document_number' => $validated['document_number'],
            'document_date' => $validated['document_date'],
            'received_date' => $validated['received_date'],
            'sender_or_receiver' => $validated['sender_or_receiver'],
            'subject' => $validated['subject'],
            'status' => $document->status === 'sudah_disposisi'
                ? 'sudah_disposisi'
                : ($validated['requires_disposition'] ? 'menunggu_disposisi' : 'selesai'),
        ];

        if ($this->file_document) {
            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            $file = $this->file_document;
            $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
            $updateData['file_path'] = $file->storeAs('documents', $filename, 'public');
        }

        $document->update($updateData);

        $this->showEditModal = false;
        $this->resetForm();
        $this->dispatch('toast', type: 'success', message: 'Data Surat Masuk berhasil diperbarui.');
    }

    public function confirmDelete($id)
    {
        abort_unless($this->canManageIncoming(), 403);
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        abort_unless($this->canManageIncoming(), 403);
        $document = Document::where('type', 'incoming')->findOrFail($this->deleteId);

        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        $this->showDeleteModal = false;
        $this->deleteId = null;
        $this->dispatch('toast', type: 'success', message: 'Data Surat Masuk berhasil dihapus.');
    }

    public function resetForm()
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->editId = null;
        $this->deleteId = null;
        $this->document_number = '';
        $this->document_date = '';
        $this->received_date = '';
        $this->sender_or_receiver = '';
        $this->subject = '';
        $this->requires_disposition = true;
        $this->file_document = null;
        $this->current_file_path = null;
        $this->reference_number = null;
        $this->resetValidation();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    private function canManageIncoming(): bool
    {
        $user = auth()->user();

        return $user->role === 'administrator' || $user->isSekretariatOperator();
    }
}
