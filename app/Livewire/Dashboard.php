<?php

namespace App\Livewire;

use App\Models\Disposition;
use App\Models\Document;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $user = auth()->user();
        $role = $user->role;

        $isAdmin = $role === 'administrator';
        $isSekretariatOp = $user->isSekretariatOperator();
        $isOperator = $role === 'operator' && !$isSekretariatOp;
        $isSekretaris = $role === 'sekretaris';
        $isKepalaBadan = $role === 'kepala_badan';

        $stats = [];

        if ($isAdmin || $isSekretariatOp || $isKepalaBadan) {
            $stats['total_incoming'] = Document::where('type', 'incoming')->count();
            $stats['total_outgoing'] = Document::where('type', 'outgoing')->count();
            $stats['pending_disposition'] = Document::where('type', 'incoming')
                ->where('status', 'menunggu_disposisi')->count();
            $stats['done_disposition'] = Document::where('type', 'incoming')
                ->where('status', 'sudah_disposisi')->count();
        }

        if ($isSekretaris) {
            $stats['pending_disposition'] = Document::where('type', 'incoming')
                ->where('status', 'menunggu_disposisi')->count();
            $stats['done_disposition'] = Document::where('type', 'incoming')
                ->where('status', 'sudah_disposisi')->count();
            $stats['total_dispositions'] = Disposition::count();
        }

        if ($isOperator) {
            $stats['disposisi_baru'] = Disposition::where('department_id', $user->department_id)
                ->where('target_role', 'department')
                ->whereNull('follow_up_status')->count();
            $stats['disposisi_proses'] = Disposition::where('department_id', $user->department_id)
                ->where('target_role', 'department')
                ->whereNotNull('follow_up_status')
                ->where('follow_up_status', '!=', 'selesai')->count();
            $stats['disposisi_selesai'] = Disposition::where('department_id', $user->department_id)
                ->where('target_role', 'department')
                ->where('follow_up_status', 'selesai')->count();
        }

        // Recent items
        $recentItems = collect();
        if ($isAdmin || $isSekretariatOp) {
            $recentItems = Document::query()
                ->when($isSekretariatOp, fn ($q) => $q->where('created_by', $user->id))
                ->orderBy('id', 'desc')
                ->take(10)
                ->get();
        } elseif ($isSekretaris) {
            $recentItems = Disposition::with(['document', 'department', 'creator'])
                ->orderBy('id', 'desc')
                ->take(10)
                ->get();
        } elseif ($isKepalaBadan) {
            $recentItems = Disposition::with(['document', 'department', 'creator'])
                ->where('target_role', 'kepala_badan')
                ->orderBy('id', 'desc')
                ->take(10)
                ->get();
        } elseif ($isOperator) {
            $recentItems = Disposition::with(['document', 'department', 'creator'])
                ->where('department_id', $user->department_id)
                ->where('target_role', 'department')
                ->orderBy('id', 'desc')
                ->take(10)
                ->get();
        }

        return view('livewire.dashboard', compact(
            'stats', 'recentItems',
            'isAdmin', 'isSekretariatOp', 'isOperator', 'isSekretaris', 'isKepalaBadan'
        ));
    }
}
