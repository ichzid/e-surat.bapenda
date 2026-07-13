<?php

namespace App\Livewire;

use App\Exports\DocumentsExport;
use App\Models\Document;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class Reports extends Component
{
    use WithPagination;

    public $year;
    public $month = 'all';
    public $type = 'all';
    public $perPage = 25;

    public function mount(): void
    {
        $this->year = date('Y');
    }

    public function updatedYear(): void
    {
        $this->resetPage();
    }

    public function updatedMonth(): void
    {
        $this->resetPage();
    }

    public function updatedType(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Document::query();

        if ($this->year && $this->year !== 'all') {
            $query->whereYear('created_at', $this->year);
        }

        if ($this->month && $this->month !== 'all') {
            $query->whereMonth('created_at', $this->month);
        }

        if ($this->type && $this->type !== 'all') {
            $query->where('type', $this->type);
        }

        $total = $query->count();
        $documents = $query->orderBy('id', 'desc')->paginate($this->perPage);

        return view('livewire.reports', compact('documents', 'total'));
    }

    public function export()
    {
        $year = $this->year ?? 'all';
        $month = $this->month ?? 'all';
        $type = $this->type ?? 'all';

        $fileName = 'Laporan_Arsip_Surat';
        if ($type !== 'all') {
            $fileName .= '_' . ($type === 'incoming' ? 'Masuk' : 'Keluar');
        }
        if ($month !== 'all') {
            $fileName .= '_' . date('F', mktime(0, 0, 0, $month, 10));
        }
        if ($year !== 'all') {
            $fileName .= '_' . $year;
        }
        $fileName .= '.xlsx';

        return Excel::download(new DocumentsExport($year, $month, $type), $fileName);
    }
}
