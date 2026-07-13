<?php

namespace App\Exports;

use App\Models\Document;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DocumentsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $year;
    protected $month;
    protected $type;
    protected $rowNumber = 0;

    public function __construct($year, $month, $type)
    {
        $this->year = $year;
        $this->month = $month;
        $this->type = $type;
    }

    public function collection()
    {
        $query = Document::query();

        if ($this->year != 'all') {
            $query->whereYear('created_at', $this->year);
        }

        if ($this->month != 'all') {
            $query->whereMonth('created_at', $this->month);
        }

        if ($this->type != 'all') {
            $query->where('type', $this->type);
        }

        return $query->orderBy('id', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nomor Referensi',
            'Nomor Surat',
            'Tanggal Surat',
            'Tanggal Input',
            'Kategori',
            'Pengirim/Penerima',
            'Perihal'
        ];
    }

    public function map($document): array
    {
        $this->rowNumber++;
        
        $kategori = $document->type == 'incoming' ? 'Surat Masuk' : 'Surat Keluar';
        
        return [
            $this->rowNumber,
            $document->reference_number ?? '-',
            $document->document_number,
            \Carbon\Carbon::parse($document->document_date)->format('d M Y'),
            \Carbon\Carbon::parse($document->created_at)->format('d M Y H:i'),
            $kategori,
            $document->sender_or_receiver,
            $document->subject
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

