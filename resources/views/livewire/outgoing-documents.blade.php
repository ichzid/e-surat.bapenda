<div>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between mt-2 mb-8 gap-4">
        <div>
            <h1 class="font-display text-2xl sm:text-3xl font-bold text-navy">Surat Keluar</h1>
            <p class="font-sans text-sm sm:text-base text-slate-secondary mt-1">Daftar arsip dokumen dan surat yang dikirim oleh instansi.</p>
        </div>
        <div class="flex items-center gap-4">
            <button wire:click="openCreateModal" class="flex items-center gap-2 bg-sage hover:bg-sage/90 text-white font-bold py-2.5 px-4 rounded-lg transition-colors shadow-sm text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Tambah Surat Keluar
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
                        <th class="py-3 px-4 lg:px-6 text-xs font-bold text-navy uppercase tracking-wider w-1/4">Penerima</th>
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
                        <!-- Penerima -->
                        <td class="py-3 px-4 lg:px-6">
                            <span class="text-sm font-medium text-navy">{{ $doc->sender_or_receiver }}</span>
                        </td>
                        <!-- Tanggal -->
                        <td class="py-3 px-4 lg:px-6">
                            <div class="text-xs">
                                <span class="text-slate-500 block">Tgl Surat: <span class="font-medium text-navy">{{ $doc->document_date ? $doc->document_date->format('d M Y') : '-' }}</span></span>
                                <span class="text-slate-500 block mt-0.5">Tgl Keluar: <span class="font-medium text-navy">{{ $doc->received_date ? $doc->received_date->format('d M Y') : '-' }}</span></span>
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
                                <span>Tidak ada data surat keluar ditemukan.</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div wire:loading wire:target="search, perPage" class="px-6 py-4 text-center border-t border-slate-100">
            <div class="inline-flex items-center gap-2 text-sm text-slate-secondary">
                <svg class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                Memuat data...
            </div>
        </div>
        @if($documents->hasPages())
        <div class="p-5 lg:p-6 border-t border-slate-100 bg-slate-50/30">
            {{ $documents->links() }}
        </div>
        @endif
    </div>

    @include('livewire.partials.outgoing-modals')
</div>