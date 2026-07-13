<div>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between mt-2 mb-8 gap-4">
        <div>
            <h1 class="font-display text-2xl sm:text-3xl font-bold text-navy">Master Data User</h1>
            <p class="font-sans text-sm sm:text-base text-slate-secondary mt-1">Kelola akun pengguna aplikasi.</p>
        </div>
        <button wire:click="$set('showCreateModal', true)" class="inline-flex items-center gap-2 bg-sage hover:bg-sage/90 text-white font-bold py-2.5 px-4 rounded-lg transition-colors shadow-sm text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Tambah User
        </button>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 mb-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-5 lg:p-6 gap-4 border-b border-slate-100">
            <div class="flex items-center gap-2 text-sm text-slate-secondary">
                <span class="whitespace-nowrap">Tampilkan</span>
                <select wire:model.live="perPage" class="appearance-none border border-slate-200 rounded-lg pl-3 pr-8 py-2 text-sm focus:border-sage focus:ring-sage bg-white">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span class="whitespace-nowrap">data</span>
            </div>
            <div class="relative w-full sm:w-64 lg:w-80">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 text-slate-400"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari..." class="block w-full pl-10 pr-4 py-2 border border-slate-200 rounded-lg text-sm placeholder-slate-400 focus:border-sage focus:ring-sage bg-white">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50">
                        <th class="text-center py-3 px-4 lg:px-6 text-xs font-bold text-navy uppercase tracking-wider w-12">No</th>
                        <th class="text-left py-3 px-4 lg:px-6 text-xs font-bold text-navy uppercase tracking-wider">Nama</th>
                        <th class="text-left py-3 px-4 lg:px-6 text-xs font-bold text-navy uppercase tracking-wider hidden sm:table-cell">Username</th>
                        <th class="text-left py-3 px-4 lg:px-6 text-xs font-bold text-navy uppercase tracking-wider">Role</th>
                        <th class="text-left py-3 px-4 lg:px-6 text-xs font-bold text-navy uppercase tracking-wider hidden lg:table-cell">Bidang</th>
                        <th class="text-left py-3 px-4 lg:px-6 text-xs font-bold text-navy uppercase tracking-wider hidden sm:table-cell">Status</th>
                        <th class="text-right py-3 px-4 lg:px-6 text-xs font-bold text-navy uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($users as $index => $user)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="text-center px-5 lg:px-6 py-3.5 text-slate-secondary text-xs">{{ $users->firstItem() + $index }}</td>
                        <td class="px-5 lg:px-6 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="h-8 w-8 rounded-full bg-navy flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <span class="font-medium text-navy">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="px-5 lg:px-6 py-3.5 text-slate-secondary hidden sm:table-cell">{{ $user->username }}</td>
                        <td class="px-5 lg:px-6 py-3.5">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium capitalize {{ $user->role === 'administrator' ? 'bg-red-50 text-red-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ $user->role === 'administrator' ? 'Admin' : ($user->role === 'operator' ? 'Operator' : ($user->role === 'sekretaris' ? 'Sekretaris' : ($user->role === 'kepala_badan' ? 'Kepala Badan' : $user->role))) }}
                            </span>
                        </td>
                        <td class="px-5 lg:px-6 py-3.5 text-slate-secondary hidden lg:table-cell">
                            {{ $user->department?->name ?? '-' }}
                        </td>
                        <td class="px-5 lg:px-6 py-3.5 hidden sm:table-cell">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold tracking-wider {{ $user->is_active ? 'bg-green-50 text-green-700' : 'bg-slate-100 text-slate-500' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $user->is_active ? 'bg-green-500' : 'bg-slate-400' }}"></span>
                                {{ $user->is_active ? 'AKTIF' : 'NONAKTIF' }}
                            </span>
                        </td>
                        <td class="px-5 lg:px-6 py-3.5 text-right">
                            <div class="flex justify-end gap-2">
                                <button wire:click="edit({{ $user->id }})" class="inline-flex items-center justify-center p-1.5 bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-100 transition-colors" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
                                    </svg>
                                </button>
                                <button wire:click="confirmDelete({{ $user->id }})" class="inline-flex items-center justify-center p-1.5 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors" title="Hapus">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-secondary">
                            <div class="flex flex-col items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="w-10 h-10 text-slate-300">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/>
                                </svg>
                                <span class="text-sm">Tidak ada data pengguna</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
        <div class="p-5 lg:p-6 border-t border-slate-100 bg-slate-50/30">
            {{ $users->links() }}
        </div>
        @endif
    </div>

    <!-- ========== CREATE / EDIT MODAL ========== -->
    @if($showCreateModal || $showEditModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Overlay -->
            <div class="fixed inset-0 bg-navy/60 transition-opacity" aria-hidden="true" wire:click="resetForm"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal Panel -->
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <div class="bg-white px-6 pt-6 pb-4 border-b border-slate-100">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-bold text-navy" id="modal-title">
                            {{ $showEditModal ? 'Edit Pengguna' : 'Tambah Pengguna Baru' }}
                        </h3>
                        <button wire:click="resetForm" class="text-slate-400 hover:text-navy transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="px-6 py-4 space-y-4">
                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
                        <input wire:model="name" type="text" class="block w-full border border-slate-200 rounded-xl p-2.5 text-sm focus:border-sage focus:ring-sage placeholder-slate-400" placeholder="Masukkan nama lengkap">
                        @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <!-- Username -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Username</label>
                        <input wire:model="username" type="text" class="block w-full border border-slate-200 rounded-xl p-2.5 text-sm focus:border-sage focus:ring-sage placeholder-slate-400" placeholder="Masukkan username">
                        @error('username') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <!-- Password (only on create; optional on edit) -->
                    @if($showCreateModal)
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                        <input wire:model="password" type="password" class="block w-full border border-slate-200 rounded-xl p-2.5 text-sm focus:border-sage focus:ring-sage placeholder-slate-400" placeholder="Masukkan password">
                        @error('password') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Konfirmasi Password</label>
                        <input wire:model="password_confirmation" type="password" class="block w-full border border-slate-200 rounded-xl p-2.5 text-sm focus:border-sage focus:ring-sage placeholder-slate-400" placeholder="Ulangi password">
                    </div>
                    @else
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-3">
                        <p class="text-xs text-slate-500">
                            <span class="font-medium text-slate-700">Password</span> —
                            Kosongkan jika tidak ingin mengubah password.
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Password Baru <span class="text-xs font-normal text-slate-400">(Opsional)</span></label>
                        <input wire:model="password" type="password" class="block w-full border border-slate-200 rounded-xl p-2.5 text-sm focus:border-sage focus:ring-sage placeholder-slate-400" placeholder="Kosongkan jika tidak ingin mengubah">
                        @error('password') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Konfirmasi Password Baru</label>
                        <input wire:model="password_confirmation" type="password" class="block w-full border border-slate-200 rounded-xl p-2.5 text-sm focus:border-sage focus:ring-sage placeholder-slate-400" placeholder="Ulangi password baru">
                    </div>
                    @endif

                    <!-- Role -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Role</label>
                        <select wire:model="role" class="block w-full border border-slate-200 rounded-xl p-2.5 text-sm focus:border-sage focus:ring-sage bg-white">
                            <option value="operator">Operator</option>
                            <option value="sekretaris">Sekretaris</option>
                            <option value="kepala_badan">Kepala Badan</option>
                            <option value="administrator">Administrator</option>
                        </select>
                        @error('role') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Bidang</label>
                        <select wire:model="department_id" class="block w-full border border-slate-200 rounded-xl p-2.5 text-sm focus:border-sage focus:ring-sage bg-white">
                            <option value="">Tanpa bidang</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                        @error('department_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="bg-slate-50 px-6 py-4 flex flex-row-reverse gap-3">
                    <button
                        wire:click="{{ $showEditModal ? 'update' : 'create' }}"
                        class="inline-flex justify-center items-center gap-2 rounded-xl border border-transparent px-5 py-2.5 bg-sage text-sm font-medium text-white shadow-sm hover:bg-sage/90 focus:outline-none transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                        {{ $showEditModal ? 'Simpan Perubahan' : 'Tambahkan User' }}
                    </button>
                    <button
                        wire:click="resetForm"
                        class="inline-flex justify-center rounded-xl border border-slate-300 px-5 py-2.5 bg-white text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none transition-colors"
                    >
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- ========== DELETE CONFIRMATION MODAL ========== -->
    @if($showDeleteModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="delete-modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Overlay -->
            <div class="fixed inset-0 bg-navy/60 transition-opacity" aria-hidden="true" wire:click="closeDeleteModal"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal Panel -->
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full">
                <div class="bg-white px-6 pt-6 pb-4">
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0 h-12 w-12 rounded-full bg-red-100 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-red-600">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-navy" id="delete-modal-title">Hapus Pengguna</h3>
                            <p class="text-sm text-slate-secondary mt-1">
                                Apakah Anda yakin ingin menghapus pengguna ini? Tindakan ini tidak dapat dibatalkan.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-50 px-6 py-4 flex flex-row-reverse gap-3">
                    <button
                        wire:click="delete"
                        class="inline-flex justify-center rounded-xl border border-transparent px-5 py-2.5 bg-red-600 text-sm font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none transition-colors"
                    >
                        Hapus
                    </button>
                    <button
                        wire:click="closeDeleteModal"
                        class="inline-flex justify-center rounded-xl border border-slate-300 px-5 py-2.5 bg-white text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none transition-colors"
                    >
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
