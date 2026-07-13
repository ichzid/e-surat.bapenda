<?php

namespace App\Livewire;

use App\Models\Department;
use App\Models\Disposition;
use App\Models\Document;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Dispositions extends Component
{
    use WithPagination;

    public $pendingSearch = '';
    public $dispositionSearch = '';
    public $pendingPerPage = 10;
    public $dispositionPerPage = 10;
    public $activeTab = 'antrian';

    // Modal disposisi (sekretaris/admin)
    public $showDispositionModal = false;
    public $selectedDocumentId;
    public $departmentIds = [];
    public $instructions = [];
    public $notes = [];

    // Tindak lanjut operator bidang
    public $followUpStatus = [];
    public $followUpNote = [];

    public function render()
    {
        $user = auth()->user();
        $isSecretary = in_array($user->role, ['administrator', 'sekretaris'], true)
                       || $user->isSekretariatOperator();
        $isOperator = $user->role === 'operator' && !$user->isSekretariatOperator();
        $isKepalaBadan = $user->role === 'kepala_badan';

        // Default active tab
        if ($isSecretary && !in_array($this->activeTab, ['antrian', 'riwayat'])) {
            $this->activeTab = 'antrian';
        }
        if ($isOperator && !in_array($this->activeTab, ['baru', 'riwayat'])) {
            $this->activeTab = 'baru';
        }
        if ($isKepalaBadan) {
            $this->activeTab = 'riwayat';
        }

        // Tabel surat menunggu disposisi (hanya untuk sekretaris/admin)
        $pendingDocuments = Document::with('creator')
            ->where('type', 'incoming')
            ->where('status', 'menunggu_disposisi')
            ->when($isSecretary && $this->pendingSearch, function ($query) {
                $query->where(function ($q) {
                    $q->where('document_number', 'like', '%' . $this->pendingSearch . '%')
                        ->orWhere('reference_number', 'like', '%' . $this->pendingSearch . '%')
                        ->orWhere('subject', 'like', '%' . $this->pendingSearch . '%')
                        ->orWhere('sender_or_receiver', 'like', '%' . $this->pendingSearch . '%');
                });
            })
            ->orderBy('id', 'desc')
            ->paginate($this->pendingPerPage, pageName: 'pendingPage');

        // Disposisi untuk operator bidang: dipisah baru vs riwayat
        $baseOperatorQuery = Disposition::query()
            ->with(['document', 'department', 'creator'])
            ->where('department_id', $user->department_id)
            ->where('target_role', 'department');

        $dispositionBaru = null;
        $dispositionRiwayat = null;
        $countBaru = 0;

        if ($isOperator) {
            $countBaru = (clone $baseOperatorQuery)->whereNull('follow_up_status')->count();

            $operatorQuery = clone $baseOperatorQuery;

            if ($this->activeTab === 'baru') {
                $operatorQuery->whereNull('follow_up_status');
            } else {
                $operatorQuery->whereNotNull('follow_up_status');
            }

            $operatorQuery->when($this->dispositionSearch, function ($query) {
                $query->where(function ($q) {
                    $q->where('note', 'like', '%' . $this->dispositionSearch . '%')
                        ->orWhereHas('department', fn ($department) => $department->where('name', 'like', '%' . $this->dispositionSearch . '%'))
                        ->orWhereHas('document', function ($document) {
                            $document->where('document_number', 'like', '%' . $this->dispositionSearch . '%')
                                ->orWhere('reference_number', 'like', '%' . $this->dispositionSearch . '%')
                                ->orWhere('subject', 'like', '%' . $this->dispositionSearch . '%')
                                ->orWhere('sender_or_receiver', 'like', '%' . $this->dispositionSearch . '%');
                        });
                });
            });

            $dispositions = $operatorQuery->orderBy('id', 'desc')
                ->paginate($this->dispositionPerPage, pageName: 'dispPage');

            foreach ($dispositions as $disposition) {
                $this->followUpStatus[$disposition->id] ??= $disposition->follow_up_status;
                $this->followUpNote[$disposition->id] ??= $disposition->follow_up_note;
            }
        } else {
            // Sekretaris/Admin/Kepala Badan: tampilkan semua disposisi
            $dispositions = Disposition::query()
                ->with(['document', 'department', 'creator'])
                ->when($this->dispositionSearch, function ($query) {
                    $query->where(function ($q) {
                        $q->where('note', 'like', '%' . $this->dispositionSearch . '%')
                            ->orWhereHas('department', fn ($department) => $department->where('name', 'like', '%' . $this->dispositionSearch . '%'))
                            ->orWhereHas('document', function ($document) {
                                $document->where('document_number', 'like', '%' . $this->dispositionSearch . '%')
                                    ->orWhere('reference_number', 'like', '%' . $this->dispositionSearch . '%')
                                    ->orWhere('subject', 'like', '%' . $this->dispositionSearch . '%')
                                    ->orWhere('sender_or_receiver', 'like', '%' . $this->dispositionSearch . '%');
                            });
                    });
                })
                ->orderBy('id', 'desc')
                ->paginate($this->dispositionPerPage, pageName: 'dispPage');

            foreach ($dispositions as $disposition) {
                $this->followUpStatus[$disposition->id] ??= $disposition->follow_up_status;
                $this->followUpNote[$disposition->id] ??= $disposition->follow_up_note;
            }
        }

        return view('livewire.dispositions', [
            'dispositions' => $dispositions,
            'pendingDocuments' => $isSecretary ? $pendingDocuments : null,
            'departments' => Department::where('name', '!=', 'SEKRETARIAT')->orderBy('name')->get(),
            'isSecretary' => $isSecretary,
            'isOperator' => $isOperator,
            'isKepalaBadan' => $isKepalaBadan,
            'countBaru' => $countBaru,
        ]);
    }

    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage($tab === 'antrian' ? 'pendingPage' : 'dispPage');
    }

    // ---- Modal Disposisi ----

    public function openDispositionModal($documentId): void
    {
        abort_unless(in_array(auth()->user()->role, ['administrator', 'sekretaris'], true), 403);

        $this->reset(['selectedDocumentId', 'departmentIds', 'instructions', 'notes']);
        $this->selectedDocumentId = $documentId;
        $this->showDispositionModal = true;
    }

    public function closeDispositionModal(): void
    {
        $this->showDispositionModal = false;
        $this->reset(['selectedDocumentId', 'departmentIds', 'instructions', 'notes']);
    }

    public function updatedDepartmentIds(): void
    {
        foreach ($this->departmentIds as $departmentId) {
            $this->instructions[$departmentId] ??= '';
            $this->notes[$departmentId] ??= '';
        }

        $selected = array_map('strval', $this->departmentIds);
        $this->instructions = array_intersect_key($this->instructions, array_flip($selected));
        $this->notes = array_intersect_key($this->notes, array_flip($selected));
    }

    public function createDisposition(): void
    {
        abort_unless(in_array(auth()->user()->role, ['administrator', 'sekretaris'], true), 403);

        $rules = [
            'selectedDocumentId' => 'required|exists:documents,id',
            'departmentIds' => 'required|array|min:1',
            'departmentIds.*' => 'exists:departments,id',
            'notes' => 'nullable|array',
            'notes.*' => 'nullable|string',
        ];

        foreach ($this->departmentIds as $departmentId) {
            $rules["instructions.$departmentId"] = 'required|string';
        }

        $validated = $this->validate($rules, [
            'selectedDocumentId.required' => 'Surat wajib dipilih.',
            'selectedDocumentId.exists' => 'Surat yang dipilih tidak valid.',
            'departmentIds.required' => 'Bidang tujuan wajib dipilih.',
            'departmentIds.array' => 'Format bidang tujuan tidak valid.',
            'departmentIds.min' => 'Pilih minimal satu bidang tujuan.',
            'departmentIds.*.exists' => 'Bidang tujuan yang dipilih tidak valid.',
            'instructions.*.required' => 'Instruksi wajib dipilih.',
            'instructions.*.string' => 'Instruksi harus berupa teks.',
            'notes.*.string' => 'Catatan harus berupa teks.',
        ]);

        DB::transaction(function () use ($validated) {
            $document = Document::where('type', 'incoming')->findOrFail($validated['selectedDocumentId']);

            foreach ($validated['departmentIds'] as $departmentId) {
                $instruction = $validated['instructions'][$departmentId] ?? '';
                $note = $validated['notes'][$departmentId] ?? '';
                $dispositionNote = $instruction . ($note ? ' — ' . $note : '');

                Disposition::firstOrCreate([
                    'document_id' => $document->id,
                    'department_id' => $departmentId,
                    'target_role' => 'department',
                ], [
                    'created_by' => auth()->id(),
                    'note' => $dispositionNote,
                ]);
            }

            $document->update(['status' => 'sudah_disposisi']);
        });

        $this->closeDispositionModal();
        $this->dispatch('toast', type: 'success', message: 'Disposisi berhasil dibuat.');
        $this->dispatch('refresh-sidebar-badge');
    }

    // ---- Tindak Lanjut Operator ----

    public function saveFollowUp($id): void
    {
        $disposition = Disposition::findOrFail($id);
        abort_unless(auth()->user()->role === 'operator' && $disposition->department_id === auth()->user()->department_id, 403);

        $this->validate([
            "followUpStatus.$id" => 'required|string|max:255',
            "followUpNote.$id" => 'nullable|string',
        ], [
            "followUpStatus.$id.required" => 'Status tindak lanjut wajib dipilih.',
            "followUpStatus.$id.string" => 'Status tindak lanjut harus berupa teks.',
            "followUpStatus.$id.max" => 'Status tindak lanjut maksimal 255 karakter.',
            "followUpNote.$id.string" => 'Keterangan tindak lanjut harus berupa teks.',
        ]);

        $disposition->update([
            'follow_up_status' => $this->followUpStatus[$id],
            'follow_up_note' => $this->followUpNote[$id] ?? null,
            'followed_up_at' => now(),
        ]);

        $this->dispatch('toast', type: 'success', message: 'Tindak lanjut berhasil disimpan.');
        $this->dispatch('refresh-sidebar-badge');
    }

    public function updatingPendingSearch()
    {
        $this->resetPage('pendingPage');
    }

    public function updatingDispositionSearch()
    {
        $this->resetPage('dispPage');
    }

    public function updatingPendingPerPage()
    {
        $this->resetPage('pendingPage');
    }

    public function updatingDispositionPerPage()
    {
        $this->resetPage('dispPage');
    }
}
