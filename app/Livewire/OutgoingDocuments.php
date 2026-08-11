<?php

namespace App\Livewire;

use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class OutgoingDocuments extends Component
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

    public function mount()
    {
        $this->resetForm();
    }

    public function render()
    {
        $documents = Document::where('type', 'outgoing')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('document_number', 'like', '%' . $this->search . '%')
                        ->orWhere('reference_number', 'like', '%' . $this->search . '%')
                        ->orWhere('sender_or_receiver', 'like', '%' . $this->search . '%')
                        ->orWhere('subject', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        return view('livewire.outgoing-documents', [
            'documents' => $documents,
        ])->layout('layouts.app');
    }

    protected function rules()
    {
        return [
            'document_number' => 'required|string|max:255',
            'document_date' => 'required|date',
            'received_date' => 'required|date',
            'sender_or_receiver' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'file_document' => ($this->showEditModal ? 'nullable' : 'required') . '|file|mimes:pdf|max:3072',
            'reference_number' => 'nullable|string|max:255',
        ];
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function create()
    {
        $this->validate();

        $data = [
            'type' => 'outgoing',
            'document_number' => $this->document_number,
            'document_date' => $this->document_date,
            'received_date' => $this->received_date,
            'sender_or_receiver' => $this->sender_or_receiver,
            'subject' => $this->subject,
            'created_by' => auth()->id(),
        ];

        if ($this->file_document) {
            $file = $this->file_document;
            $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('documents', $filename, 'public');
            $data['file_path'] = $filePath;
        }

        Document::create($data);

        $this->resetForm();
        $this->showCreateModal = false;
        $this->dispatch('toast', type: 'success', message: 'Surat keluar berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $document = Document::findOrFail($id);

        $this->editId = $document->id;
        $this->document_number = $document->document_number;
        $this->document_date = $document->document_date->format('Y-m-d');
        $this->received_date = $document->received_date ? $document->received_date->format('Y-m-d') : null;
        $this->sender_or_receiver = $document->sender_or_receiver;
        $this->subject = $document->subject;
        $this->reference_number = $document->reference_number;
        $this->file_document = null;

        $this->showEditModal = true;
    }

    public function update()
    {
        $this->validate();

        $document = Document::findOrFail($this->editId);

        $document->document_number = $this->document_number;
        $document->document_date = $this->document_date;
        $document->received_date = $this->received_date;
        $document->sender_or_receiver = $this->sender_or_receiver;
        $document->subject = $this->subject;

        if ($this->file_document) {
            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            $file = $this->file_document;
            $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
            $document->file_path = $file->storeAs('documents', $filename, 'public');
        }

        $document->save();

        $this->resetForm();
        $this->showEditModal = false;
        $this->dispatch('toast', type: 'success', message: 'Surat keluar berhasil diperbarui.');
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        $document = Document::findOrFail($this->deleteId);

        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        $this->showDeleteModal = false;
        $this->deleteId = null;
        $this->dispatch('toast', type: 'success', message: 'Surat keluar berhasil dihapus.');
    }

    public function resetForm()
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->editId = null;
        $this->document_number = '';
        $this->document_date = '';
        $this->received_date = '';
        $this->sender_or_receiver = '';
        $this->subject = '';
        $this->reset('file_document');
        $this->current_file_path = '';
        $this->reference_number = '';
        $this->resetValidation();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }
}
