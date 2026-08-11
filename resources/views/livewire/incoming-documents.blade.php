<div>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between mt-2 mb-8 gap-4">
        <div>
            <h1 class="font-display text-2xl sm:text-3xl font-bold text-navy">Surat Masuk</h1>
            <p class="font-sans text-sm sm:text-base text-slate-secondary mt-1">Daftar arsip dokumen dan surat yang diterima instansi.</p>
        </div>
        <div class="flex items-center gap-4">
            <button wire:click="create" class="flex items-center gap-2 bg-sage hover:bg-sage/90 text-white font-bold py-2.5 px-4 rounded-lg transition-colors shadow-sm text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Tambah Surat Masuk
            </button>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 mb-8">
        <!-- Toolbar: Per Page + Search (DataTables style: length left, search right) -->
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
                <input 
                    wire:model.live.debounce.300ms="search"
                    type="text" 
                    class="block w-full pl-10 pr-4 py-2 border border-slate-200 rounded-lg text-sm placeholder-slate-400 focus:border-sage focus:ring-sage" 
                    placeholder="Cari..."
                >
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50">
                        <th class="py-3 px-4 lg:px-6 text-xs font-bold text-navy uppercase tracking-wider w-1/4">Info Surat</th>
                        <th class="py-3 px-4 lg:px-6 text-xs font-bold text-navy uppercase tracking-wider w-1/4">Pengirim</th>
                        <th class="py-3 px-4 lg:px-6 text-xs font-bold text-navy uppercase tracking-wider w-[20%]">Tanggal</th>
                        <th class="py-3 px-4 lg:px-6 text-xs font-bold text-navy uppercase tracking-wider text-center w-[15%]">File</th>
                        <th class="py-3 px-4 lg:px-6 text-xs font-bold text-navy uppercase tracking-wider text-right w-[15%]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($documents as $doc)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <!-- Info Surat -->
                        <td class="py-3 px-4 lg:px-6">
                            <div class="font-sans text-sm font-bold text-navy">{{ $doc->document_number }}</div>
                            @if($doc->reference_number && $doc->reference_number !== $doc->document_number)
                            <div class="text-[10px] text-slate-400 font-mono mt-0.5 mb-1">Ref: {{ $doc->reference_number }}</div>
                            @endif
                            <div class="text-xs text-slate-500 line-clamp-2 mt-0.5" title="{{ $doc->subject }}">{{ $doc->subject }}</div>
                        </td>
                        <!-- Pengirim -->
                        <td class="py-3 px-4 lg:px-6">
                            <span class="text-sm font-medium text-navy">{{ $doc->sender_or_receiver }}</span>
                        </td>
                        <!-- Tanggal -->
                        <td class="py-3 px-4 lg:px-6">
                            <div class="text-xs">
                                <span class="text-slate-500 block">Tgl Surat: <span class="font-medium text-navy">{{ $doc->document_date ? $doc->document_date->format('d M Y') : '-' }}</span></span>
                                <span class="text-slate-500 block mt-0.5">Diterima: <span class="font-medium text-navy">{{ $doc->received_date ? $doc->received_date->format('d M Y') : '-' }}</span></span>
                            </div>
                        </td>
                        <!-- File -->
                        <td class="py-3 px-4 lg:px-6 text-center">
                            @if($doc->file_path)
                            <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="inline-flex items-center justify-center p-1.5 bg-status-info/10 text-status-info hover:bg-status-info/20 rounded-lg transition-colors" title="Lihat/Download File">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                            </a>
                            @else
                            <span class="text-xs text-slate-400">-</span>
                            @endif
                        </td>
                        <!-- Aksi -->
                        <td class="py-3 px-4 lg:px-6">
                            <div class="flex items-center justify-end gap-1.5">
                                <button wire:click="edit({{ $doc->id }})" class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-amber-50 text-amber-600 text-xs font-medium rounded-lg hover:bg-amber-100 transition-colors" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/></svg>
                                </button>
                                <button wire:click="confirmDelete({{ $doc->id }})" class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-red-50 text-red-600 text-xs font-medium rounded-lg hover:bg-red-100 transition-colors" title="Hapus">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-sm text-slate-500">
                            <div class="flex flex-col items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="w-10 h-10 text-slate-300"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                                <span>Tidak ada data surat masuk ditemukan.</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Loading -->
        <div wire:loading wire:target="search, perPage" class="px-6 py-4 text-center border-t border-slate-100">
            <div class="inline-flex items-center gap-2 text-sm text-slate-secondary">
                <svg class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                Memuat data...
            </div>
        </div>

        <!-- Pagination -->
        @if($documents->hasPages())
        <div class="p-5 lg:p-6 border-t border-slate-100 bg-slate-50/30">
            {{ $documents->links() }}
        </div>
        @endif
    </div>

    <!-- Create/Edit Modal -->
    @if($showCreateModal || $showEditModal)
    <div class="fixed inset-0 z-[60] flex items-center justify-center p-4" x-data>
        <div class="fixed inset-0 bg-navy/50 backdrop-blur-sm" wire:click="resetForm"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto z-10" @click.stop="">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-6 border-b border-slate-100">
                <h3 class="text-lg font-bold text-navy">{{ $showEditModal ? 'Edit' : 'Tambah' }} Surat Masuk</h3>
                <button wire:click="resetForm" class="p-2 text-slate-400 hover:text-navy hover:bg-slate-50 rounded-lg transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-4">
                @if($showEditModal)
                <div>
                    <label class="block text-xs font-bold text-slate-secondary uppercase tracking-wider mb-1">No Referensi</label>
                    <input type="text" value="{{ $reference_number }}" readonly class="block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-500">
                </div>
                @endif

                <div>
                    <label class="block text-xs font-bold text-slate-secondary uppercase tracking-wider mb-1">No Surat</label>
                    <input type="text" wire:model="document_number" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:border-sage focus:ring-sage">
                    @error('document_number') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-secondary uppercase tracking-wider mb-1">Tanggal Surat</label>
                        <input type="date" wire:model="document_date" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:border-sage focus:ring-sage">
                        @error('document_date') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-secondary uppercase tracking-wider mb-1">Tanggal Diterima</label>
                        <input type="date" wire:model="received_date" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:border-sage focus:ring-sage">
                        @error('received_date') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-secondary uppercase tracking-wider mb-1">Pengirim</label>
                    <input type="text" wire:model="sender_or_receiver" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:border-sage focus:ring-sage" placeholder="Nama instansi/perorangan pengirim surat">
                    @error('sender_or_receiver') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-secondary uppercase tracking-wider mb-1">Perihal</label>
                    <textarea wire:model="subject" rows="2" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:border-sage focus:ring-sage" placeholder="Perihal/ringkasan isi surat"></textarea>
                    @error('subject') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-secondary uppercase tracking-wider mb-1">Apakah Surat Perlu Disposisi?</label>
                    <select wire:model="requires_disposition" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:border-sage focus:ring-sage bg-white">
                        <option value="1">Ya, perlu disposisi</option>
                        <option value="0">Tidak, langsung selesai</option>
                    </select>
                    <p class="text-xs text-slate-400 mt-1">Jika perlu disposisi, surat masuk ke antrian Sekretaris. Jika tidak, status langsung selesai.</p>
                    @error('requires_disposition') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    @if($showEditModal && $current_file_path)
                    <div class="mb-3 rounded-xl border border-slate-200 bg-slate-50 p-3 flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-bold text-slate-secondary uppercase tracking-wider">File saat ini</p>
                            <p class="text-sm text-navy font-medium mt-0.5">Dokumen surat sudah tersedia</p>
                        </div>
                        <a href="{{ Storage::url($current_file_path) }}" target="_blank" class="shrink-0 px-3 py-2 rounded-lg bg-sage/10 text-sage text-xs font-bold hover:bg-sage/20 transition-colors">Lihat Dokumen</a>
                    </div>
                    @endif

                    <label class="block text-xs font-bold text-slate-secondary uppercase tracking-wider mb-1">
                        {{ $showEditModal ? 'Upload File Pengganti (Opsional)' : 'File Surat (PDF, max 3MB)' }}
                    </label>
                    <input type="file" wire:model="file_document" accept=".pdf" class="block w-full text-sm text-slate-secondary file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-sage/10 file:text-sage hover:file:bg-sage/20">
                    @if($showEditModal && !$file_document)
                    <p class="text-xs text-slate-400 mt-1">Biarkan kosong jika tidak ingin mengganti file lama.</p>
                    @endif
                    @error('file_document') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    <div wire:loading wire:target="file_document" class="text-xs text-sage mt-1">Mengunggah file...</div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end gap-3 p-6 border-t border-slate-100 bg-slate-50/50">
                <button wire:click="resetForm" class="px-4 py-2.5 text-sm font-medium text-slate-secondary hover:text-navy bg-white border border-slate-200 rounded-xl transition-colors">Batal</button>
                <button wire:click="{{ $showEditModal ? 'update' : 'store' }}" wire:loading.attr="disabled" class="px-5 py-2.5 text-sm font-bold text-white bg-sage hover:bg-sage/90 rounded-xl transition-colors disabled:opacity-50">
                    <span wire:loading.remove wire:target="{{ $showEditModal ? 'update' : 'store' }}">{{ $showEditModal ? 'Simpan Perubahan' : 'Simpan Surat' }}</span>
                    <span wire:loading wire:target="{{ $showEditModal ? 'update' : 'store' }}">Menyimpan...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
    <div class="fixed inset-0 z-[60] flex items-center justify-center p-4" x-data>
        <div class="fixed inset-0 bg-navy/50 backdrop-blur-sm" wire:click="$set('showDeleteModal', false)"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm z-10 p-6 text-center">
            <div class="mx-auto w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-red-600"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
            </div>
            <h3 class="text-lg font-bold text-navy mb-2">Konfirmasi Hapus</h3>
            <p class="text-sm text-slate-secondary mb-6">Apakah Anda yakin ingin menghapus surat masuk ini? Data yang sudah dihapus tidak dapat dikembalikan.</p>
            <div class="flex items-center justify-center gap-3">
                <button wire:click="$set('showDeleteModal', false)" class="px-4 py-2.5 text-sm font-medium text-slate-secondary hover:text-navy bg-white border border-slate-200 rounded-xl transition-colors">Batal</button>
                <button wire:click="delete" wire:loading.attr="disabled" class="px-4 py-2.5 text-sm font-bold text-white bg-red-500 hover:bg-red-600 rounded-xl transition-colors disabled:opacity-50">
                    <span wire:loading.remove wire:target="delete">Hapus</span>
                    <span wire:loading wire:target="delete">Menghapus...</span>
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
