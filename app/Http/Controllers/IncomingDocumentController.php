<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class IncomingDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Document::with('creator')->where('type', 'incoming');

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

        return Inertia::render('Incoming/Index', [
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

        // Generate Nomor Referensi Otomatis untuk Surat Masuk
        // Format: ARSIP/SM/{BulanRomawi}/{Tahun}/{AutoIncrement}
        // Berdasarkan Tanggal Input Sistem
        $now = now();
        $year = $now->year;
        $month = $now->month;
        $romawi = [1=>'I', 2=>'II', 3=>'III', 4=>'IV', 5=>'V', 6=>'VI', 7=>'VII', 8=>'VIII', 9=>'IX', 10=>'X', 11=>'XI', 12=>'XII'];
        
        $latestDoc = Document::where('type', 'incoming')
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
        
        $referenceNumber = 'ARSIP/SM/' . $romawi[$month] . '/' . $year . '/' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        $filePath = null;
        if ($request->hasFile('file_document')) {
            $file = $request->file('file_document');
            $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('documents', $filename, 'public');
        }

        Document::create([
            'type' => 'incoming',
            'reference_number' => $referenceNumber,
            'document_number' => $validated['document_number'],
            'document_date' => $validated['document_date'],
            'received_date' => $validated['received_date'],
            'sender_or_receiver' => $validated['sender_or_receiver'],
            'subject' => $validated['subject'],
            'file_path' => $filePath,
            'created_by' => auth()->id(),
        ]);

        return redirect()->back()->with('message', 'Surat Masuk berhasil ditambahkan dengan No Referensi: ' . $referenceNumber);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Document $incoming)
    {
        // Pastikan ini adalah surat masuk
        if ($incoming->type !== 'incoming') {
            abort(404);
        }

        $validated = $request->validate([
            'reference_number' => 'nullable|string|max:255',
            'document_number' => 'required|string|max:255',
            'document_date' => 'required|date',
            'received_date' => 'required|date',
            'sender_or_receiver' => 'required|string|max:255',
            'subject' => 'required|string',
            'file_document' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        $updateData = [
            'reference_number' => $validated['reference_number'] ?? null,
            'document_number' => $validated['document_number'],
            'document_date' => $validated['document_date'],
            'received_date' => $validated['received_date'],
            'sender_or_receiver' => $validated['sender_or_receiver'],
            'subject' => $validated['subject'],
        ];

        if ($request->hasFile('file_document')) {
            // Hapus file lama jika ada
            if ($incoming->file_path && Storage::disk('public')->exists($incoming->file_path)) {
                Storage::disk('public')->delete($incoming->file_path);
            }

            $file = $request->file('file_document');
            $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
            $updateData['file_path'] = $file->storeAs('documents', $filename, 'public');
        }

        $incoming->update($updateData);

        return redirect()->back()->with('message', 'Data Surat Masuk berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Document $incoming)
    {
        if ($incoming->type !== 'incoming') {
            abort(404);
        }

        // Soft delete datanya (file tidak dihapus secara fisik untuk keamanan arsip)
        $incoming->delete();

        return redirect()->back()->with('message', 'Data Surat Masuk berhasil dihapus.');
    }
}
