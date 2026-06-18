<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;

class GlobalSearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q');

        if (!$query) {
            return response()->json([]);
        }

        $results = Document::where('document_number', 'like', "%{$query}%")
            ->orWhere('reference_number', 'like', "%{$query}%")
            ->orWhere('subject', 'like', "%{$query}%")
            ->orWhere('sender_or_receiver', 'like', "%{$query}%")
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function($doc) {
                return [
                    'id' => $doc->id,
                    'title' => $doc->subject,
                    'subtitle' => $doc->document_number . ' - ' . ($doc->reference_number ?? 'No Ref'),
                    'type' => $doc->type,
                    'url' => $doc->type === 'incoming' 
                        ? route('incoming.index', ['search' => $doc->document_number])
                        : route('outgoing.index', ['search' => $doc->document_number])
                ];
            });

        return response()->json($results);
    }
}
