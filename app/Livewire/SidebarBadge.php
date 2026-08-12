<?php

namespace App\Livewire;

use App\Models\Disposition;
use App\Models\Document;
use Livewire\Attributes\On;
use Livewire\Component;

class SidebarBadge extends Component
{
    public $count = 0;

    public function mount(): void
    {
        $this->refresh();
    }

    #[On('refresh-sidebar-badge')]
    public function refresh(): void
    {
        $user = auth()->user();
        $isSekretaris = in_array($user->role, ['sekretaris', 'administrator'], true);
        $isOperatorSekretariat = $user->isSekretariatOperator();
        $isOperatorBidang = $user->role === 'operator' && !$isOperatorSekretariat;
        $isKepalaBadan = $user->role === 'kepala_badan';

        if ($isSekretaris || $isOperatorSekretariat) {
            $this->count = Document::where('type', 'incoming')
                ->where('status', 'menunggu_disposisi')->count();
        } elseif ($isOperatorBidang) {
            $this->count = Disposition::where('department_id', $user->department_id)
                ->where('target_role', 'department')
                ->whereNull('follow_up_status')->count();
        } elseif ($isKepalaBadan) {
            $this->count = Disposition::where('target_role', 'kepala_badan')
                ->whereNull('follow_up_status')->count();
        } else {
            $this->count = 0;
        }
    }

    public function render()
    {
        if ($this->count <= 0) {
            return <<<'blade'
                <span></span>
            blade;
        }

        return <<<'blade'
            <span class="ml-auto inline-flex items-center justify-center min-w-[22px] h-5 px-1.5 text-[10px] font-bold bg-sage text-white rounded-full">{{ $count }}</span>
        blade;
    }
}
