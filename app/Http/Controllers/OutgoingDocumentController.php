<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OutgoingDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Document::with('creator')->where('type', 'outgoing');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhere('document_number', 'like', "%{$search}%")
                  ->orWhere('sender_or_receiver', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        $documents = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        return Inertia::render('Outgoing/Index', [
            'documents' => $documents,
            'filters' => $request->only(['search'])
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'document_number' => 'required|string|max:255',
            'document_date' => 'required|date',
            'received_date' => 'required|date',
            'sender_or_receiver' => 'required|string|max:255',
            'subject' => 'required|string',
            'file_document' => 'nullable|file|mimes:pdf,doc,docx|max:10240', // Maksimal 10MB
        ]);

        // Generate Nomor Referensi Otomatis untuk Surat Keluar
        // Format: ARSIP/SK/{BulanRomawi}/{Tahun}/{AutoIncrement}
        // Berdasarkan Tanggal Input Sistem
        $now = now();
        $year = $now->year;
        $month = $now->month;
        $romawi = [1=>'I', 2=>'II', 3=>'III', 4=>'IV', 5=>'V', 6=>'VI', 7=>'VII', 8=>'VIII', 9=>'IX', 10=>'X', 11=>'XI', 12=>'XII'];
        
        $latestDoc = Document::where('type', 'outgoing')
                             ->whereYear('created_at', $year)
                             ->withTrashed()
                             ->orderBy('id', 'desc')
                             ->first();
                             
        $nextNumber = 1;
        if ($latestDoc && $latestDoc->reference_number) {
            $parts = explode('/', $latestDoc->reference_number);
            $lastNum = (int) end($parts);
            if ($lastNum > 0) {
                $nextNumber = $lastNum + 1;
            }
        }
        
        $referenceNumber = 'ARSIP/SK/' . $romawi[$month] . '/' . $year . '/' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        $filePath = null;
        if ($request->hasFile('file_document')) {
            $file = $request->file('file_document');
            $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('documents', $filename, 'public');
        }

        Document::create([
            'type' => 'outgoing',
            'reference_number' => $referenceNumber,
            'document_number' => $validated['document_number'],
            'document_date' => $validated['document_date'],
            'received_date' => $validated['received_date'],
            'sender_or_receiver' => $validated['sender_or_receiver'],
            'subject' => $validated['subject'],
            'file_path' => $filePath,
            'created_by' => auth()->id(),
        ]);

        return redirect()->back()->with('message', 'Surat Keluar berhasil ditambahkan dengan No Referensi: ' . $referenceNumber);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Document $outgoing)
    {
        if ($outgoing->type !== 'outgoing') {
            abort(404);
        }

        $validated = $request->validate([
            'document_number' => 'required|string|max:255',
            'document_date' => 'required|date',
            'received_date' => 'required|date',
            'sender_or_receiver' => 'required|string|max:255',
            'subject' => 'required|string',
            'file_document' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        $updateData = [
            'document_number' => $validated['document_number'],
            'document_date' => $validated['document_date'],
            'received_date' => $validated['received_date'],
            'sender_or_receiver' => $validated['sender_or_receiver'],
            'subject' => $validated['subject'],
        ];

        if ($request->hasFile('file_document')) {
            if ($outgoing->file_path && Storage::disk('public')->exists($outgoing->file_path)) {
                Storage::disk('public')->delete($outgoing->file_path);
            }

            $file = $request->file('file_document');
            $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
            $updateData['file_path'] = $file->storeAs('documents', $filename, 'public');
        }

        $outgoing->update($updateData);

        return redirect()->back()->with('message', 'Data Surat Keluar berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Document $outgoing)
    {
        if ($outgoing->type !== 'outgoing') {
            abort(404);
        }

        $outgoing->delete();

        return redirect()->back()->with('message', 'Data Surat Keluar berhasil dihapus.');
    }
}
