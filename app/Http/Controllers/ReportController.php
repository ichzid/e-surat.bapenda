<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DocumentsExport;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Document::query();

        // Filter Tahun
        if ($request->has('year') && $request->year != 'all') {
            $query->whereYear('document_date', $request->year);
        }

        // Filter Bulan
        if ($request->has('month') && $request->month != 'all') {
            $query->whereMonth('document_date', $request->month);
        }

        // Filter Kategori (Masuk/Keluar)
        if ($request->has('type') && $request->type != 'all') {
            $query->where('type', $request->type);
        }

        $documents = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();

        return Inertia::render('Reports/Index', [
            'documents' => $documents,
            'filters' => $request->only(['year', 'month', 'type'])
        ]);
    }

    public function export(Request $request)
    {
        $year = $request->query('year', 'all');
        $month = $request->query('month', 'all');
        $type = $request->query('type', 'all');

        $fileName = 'Laporan_Arsip_Surat';
        if ($type != 'all') {
            $fileName .= '_' . ($type == 'incoming' ? 'Masuk' : 'Keluar');
        }
        if ($month != 'all') {
            $fileName .= '_' . date('F', mktime(0, 0, 0, $month, 10));
        }
        if ($year != 'all') {
            $fileName .= '_' . $year;
        }
        $fileName .= '.xlsx';

        return Excel::download(new DocumentsExport($year, $month, $type), $fileName);
    }
}
