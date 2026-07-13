<?php

namespace App\Livewire;

use App\Models\Document;
use App\Models\User;
use Livewire\Component;

class GlobalSearch extends Component
{
    public $query = '';
    public $results = [];

    public function updatedQuery(): void
    {
        $this->results = [];

        if (empty(trim($this->query))) {
            return;
        }

        $q = $this->query;

        // Search Documents by document_number or subject
        $documents = Document::where(function ($query) use ($q) {
            $query->where('document_number', 'like', "%{$q}%")
                  ->orWhere('subject', 'like', "%{$q}%");
        })
            ->limit(5)
            ->get()
            ->map(function ($doc) {
                return [
                    'type' => 'document',
                    'id' => $doc->id,
                    'title' => $doc->subject,
                    'subtitle' => $doc->document_number . ($doc->reference_number ? ' - ' . $doc->reference_number : ''),
                    'doc_type' => $doc->type,
                ];
            });

        $this->results = $documents->toArray();

        // Search Users (admin only)
        if (auth()->check() && auth()->user()->role === 'administrator') {
            $users = User::where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('username', 'like', "%{$q}%");
            })
                ->limit(5)
                ->get()
                ->map(function ($user) {
                    return [
                        'type' => 'user',
                        'id' => $user->id,
                        'title' => $user->name,
                        'subtitle' => '@' . $user->username,
                        'role' => $user->role,
                    ];
                });

            $this->results = array_merge($this->results, $users->toArray());
        }
    }

    public function render()
    {
        return view('livewire.global-search');
    }
}
