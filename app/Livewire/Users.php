<?php

namespace App\Livewire;

use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class Users extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    public $showCreateModal = false;
    public $showEditModal = false;
    public $editId;
    public $showDeleteModal = false;
    public $deleteId;

    public $name = '';
    public $username = '';
    public $password = '';
    public $password_confirmation = '';
    public $role = 'operator';
    public $department_id = '';

    public function render()
    {
        $users = User::query()
            ->with('department')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('username', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        return view('livewire.users', [
            'users' => $users,
            'departments' => Department::orderBy('name')->get(),
        ]);
    }

    public function create(): void
    {
        $this->validate($this->rules());

        User::create([
            'name'     => $this->name,
            'username' => $this->username,
            'password' => Hash::make($this->password),
            'role'     => $this->role,
            'department_id' => $this->department_id ?: null,
        ]);

        $this->resetForm();
        $this->showCreateModal = false;

        $this->dispatch('toast', type: 'success', message: 'Pengguna berhasil ditambahkan.');
    }

    public function edit($id): void
    {
        $user = User::findOrFail($id);

        $this->editId   = $id;
        $this->name     = $user->name;
        $this->username = $user->username;
        $this->role     = $user->role;
        $this->department_id = $user->department_id;
        $this->password = '';
        $this->password_confirmation = '';

        $this->showEditModal = true;
    }

    public function update(): void
    {
        $this->validate($this->rules(false));

        $user = User::findOrFail($this->editId);

        $data = [
            'name'     => $this->name,
            'username' => $this->username,
            'role'     => $this->role,
            'department_id' => $this->department_id ?: null,
        ];

        if (! empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        $user->update($data);

        $this->resetForm();
        $this->showEditModal = false;

        $this->dispatch('toast', type: 'success', message: 'Pengguna berhasil diperbarui.');
    }

    public function confirmDelete($id): void
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    public function delete(): void
    {
        $user = User::findOrFail($this->deleteId);

        if ($user->id === auth()->id()) {
            $this->closeDeleteModal();
            $this->dispatch('toast', type: 'error', message: 'Anda tidak dapat menghapus akun sendiri.');
            return;
        }

        $user->delete();
        $this->showDeleteModal = false;
        $this->deleteId = null;

        $this->dispatch('toast', type: 'success', message: 'Pengguna berhasil dihapus.');
    }

    public function resetForm(): void
    {
        $this->reset([
            'name',
            'username',
            'password',
            'password_confirmation',
            'department_id',
            'editId',
            'showCreateModal',
            'showEditModal',
        ]);
        $this->role = 'operator';
        $this->resetValidation();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    private function rules(bool $create = true): array
    {
        return [
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username' . ($create ? '' : ',' . $this->editId),
            'password' => ($create ? 'required' : 'nullable') . '|min:6|confirmed',
            'role'     => 'required|in:administrator,operator,sekretaris,kepala_badan',
            'department_id' => 'nullable|exists:departments,id',
        ];
    }
}
