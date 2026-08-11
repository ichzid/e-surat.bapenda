@if($showCreateModal || $showEditModal)
<div class="fixed inset-0 z-[60] flex items-center justify-center p-4" x-data>
    <div class="fixed inset-0 bg-navy/50 backdrop-blur-sm" wire:click="resetForm"></div>
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto z-10" @click.stop="">
        <div class="flex items-center justify-between p-6 border-b border-slate-100">
            <h3 class="text-lg font-bold text-navy">{{ $showEditModal ? 'Edit' : 'Tambah' }} Surat Keluar</h3>
            <button wire:click="resetForm" class="p-2 text-slate-400 hover:text-navy hover:bg-slate-50 rounded-lg transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
            </button>
        </div>
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
                    <label class="block text-xs font-bold text-slate-secondary uppercase tracking-wider mb-1">Tanggal Keluar</label>
                    <input type="date" wire:model="received_date" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:border-sage focus:ring-sage">
                    @error('received_date') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-secondary uppercase tracking-wider mb-1">Penerima</label>
                <input type="text" wire:model="sender_or_receiver" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:border-sage focus:ring-sage" placeholder="Nama instansi/perorangan penerima surat">
                @error('sender_or_receiver') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-secondary uppercase tracking-wider mb-1">Perihal</label>
                <textarea wire:model="subject" rows="2" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:border-sage focus:ring-sage" placeholder="Perihal/ringkasan isi surat"></textarea>
                @error('subject') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
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
                @if($showEditModal && !$file_document)<p class="text-xs text-slate-400 mt-1">Biarkan kosong jika tidak ingin mengganti file lama.</p>@endif
                @error('file_document') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                <div wire:loading wire:target="file_document" class="text-xs text-sage mt-1">Mengunggah file...</div>
            </div>
        </div>
        <div class="flex items-center justify-end gap-3 p-6 border-t border-slate-100 bg-slate-50/50">
            <button wire:click="resetForm" class="px-4 py-2.5 text-sm font-medium text-slate-secondary hover:text-navy bg-white border border-slate-200 rounded-xl transition-colors">Batal</button>
            <button wire:click="{{ $showEditModal ? 'update' : 'create' }}" wire:loading.attr="disabled" class="px-5 py-2.5 text-sm font-bold text-white bg-sage hover:bg-sage/90 rounded-xl transition-colors disabled:opacity-50">
                <span wire:loading.remove wire:target="{{ $showEditModal ? 'update' : 'create' }}">{{ $showEditModal ? 'Simpan Perubahan' : 'Simpan Surat' }}</span>
                <span wire:loading wire:target="{{ $showEditModal ? 'update' : 'create' }}">Menyimpan...</span>
            </button>
        </div>
    </div>
</div>
@endif

@if($showDeleteModal)
<div class="fixed inset-0 z-[60] flex items-center justify-center p-4" x-data>
    <div class="fixed inset-0 bg-navy/50 backdrop-blur-sm" wire:click="$set('showDeleteModal', false)"></div>
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm z-10 p-6 text-center">
        <div class="mx-auto w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-red-600"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
        </div>
        <h3 class="text-lg font-bold text-navy mb-2">Konfirmasi Hapus</h3>
        <p class="text-sm text-slate-secondary mb-6">Apakah Anda yakin ingin menghapus surat keluar ini? Data yang sudah dihapus tidak dapat dikembalikan.</p>
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
