<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        // 1. Dapatkan Total Surat Masuk (Incoming)
        $totalIncomingQuery = Document::where('type', 'incoming');
        
        // 2. Dapatkan Total Surat Keluar (Outgoing)
        $totalOutgoingQuery = Document::where('type', 'outgoing');
        
        // 3. Dapatkan Arsip Bulan Ini
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $documentsThisMonthQuery = Document::whereMonth('created_at', $currentMonth)
                                           ->whereYear('created_at', $currentYear);
        
        // 4. Data Tabel Recent Documents
        $recentDocumentsQuery = Document::with('creator')->orderBy('id', 'desc')->take(5);

        $totalIncoming = $totalIncomingQuery->count();
        $totalOutgoing = $totalOutgoingQuery->count();
        $documentsThisMonth = $documentsThisMonthQuery->count();
        
        // Eksekusi Get Recent Documents dan format datanya
        $recentDocuments = $recentDocumentsQuery->get()->map(function($doc) {
            return [
                'id' => $doc->id,
                'reference_number' => $doc->reference_number,
                'type' => $doc->type, // incoming / outgoing
                'subject' => $doc->subject,
                'sender_or_receiver' => $doc->sender_or_receiver,
                'date' => Carbon::parse($doc->document_date)->format('M d, Y'),
                'created_by' => $doc->creator ? $doc->creator->name : 'System',
            ];
        });

        // 5. Khusus Admin: Total Pengguna
        $totalUsers = 0;
        if ($user->role === 'admin') {
            $totalUsers = User::count();
        }

        // 6. Hitung data statistik 30 hari terakhir untuk Grafik Aktivitas Arsip
        $chartData = [];
        for ($i = 9; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $formattedDate = $date->format('d M');
            $count = Document::whereDate('created_at', $date->toDateString())->count();
            $chartData[] = [
                'label' => $formattedDate,
                'count' => $count,
                'is_today' => $i === 0
            ];
        }

        return Inertia::render('Dashboard', [
            'stats' => [
                'incoming' => $totalIncoming,
                'outgoing' => $totalOutgoing,
                'this_month' => $documentsThisMonth,
                'users' => $totalUsers,
            ],
            'recent_documents' => $recentDocuments,
            'chart_data' => $chartData,
        ]);
    }
}
