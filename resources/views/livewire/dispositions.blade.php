<div>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between mt-2 mb-8 gap-4">
        <div>
            <h1 class="font-display text-2xl sm:text-3xl font-bold text-navy">Disposisi Surat</h1>
            <p class="font-sans text-sm sm:text-base text-slate-secondary mt-1">Kelola antrian disposisi, riwayat disposisi, dan tindak lanjut surat masuk.</p>
        </div>
    </div>

    @if($isSecretary)
    <div class="mb-8">
        <nav class="inline-flex rounded-2xl border border-slate-200 bg-white p-1 shadow-sm" aria-label="Tabs">
            <button wire:click="setActiveTab('antrian')" class="relative inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold rounded-xl transition-all {{ $activeTab === 'antrian' ? 'bg-sage text-white shadow-sm' : 'text-slate-secondary hover:text-navy' }}">
                Antrian
                @if($pendingDocuments->total() > 0)
                <span class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 text-[10px] font-bold bg-white/25 text-white rounded-full">{{ $pendingDocuments->total() }}</span>
                @endif
            </button>
            <button wire:click="setActiveTab('riwayat')" class="relative inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold rounded-xl transition-all {{ $activeTab === 'riwayat' ? 'bg-sage text-white shadow-sm' : 'text-slate-secondary hover:text-navy' }}">
                Riwayat
            </button>
        </nav>
    </div>

    @elseif($isOperator)
    <div class="mb-8">
        <nav class="inline-flex rounded-2xl border border-slate-200 bg-white p-1 shadow-sm" aria-label="Tabs">
            <button wire:click="setActiveTab('baru')" class="relative inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold rounded-xl transition-all {{ $activeTab === 'baru' ? 'bg-sage text-white shadow-sm' : 'text-slate-secondary hover:text-navy' }}">
                Disposisi Baru
                @if($countBaru > 0)
                <span class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 text-[10px] font-bold bg-white/25 text-white rounded-full">{{ $countBaru }}</span>
                @endif
            </button>
            <button wire:click="setActiveTab('riwayat')" class="relative inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold rounded-xl transition-all {{ $activeTab === 'riwayat' ? 'bg-sage text-white shadow-sm' : 'text-slate-secondary hover:text-navy' }}">
                Riwayat
            </button>
        </nav>
    </div>
    @endif

    @if($isSecretary && $activeTab === 'antrian')
    <!-- Card: Antrian Disposisi -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 mb-8">
        <div class="px-5 lg:px-6 py-4 border-b border-slate-100 bg-slate-50/30">
            <h2 class="text-lg font-bold text-navy">Antrian Surat Masuk</h2>
            <p class="text-sm text-slate-secondary mt-1">Surat masuk berstatus menunggu disposisi.</p>
        </div>

        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-5 lg:p-6 gap-4 border-b border-slate-100">
            <div class="flex items-center gap-2 text-sm text-slate-secondary">
                <span class="whitespace-nowrap">Tampilkan</span>
                <select wire:model.live="pendingPerPage" class="appearance-none border border-slate-200 rounded-lg pl-3 pr-8 py-2 text-sm focus:border-sage focus:ring-sage bg-white">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span class="whitespace-nowrap">data antrian</span>
            </div>
            <div class="relative w-full sm:w-64 lg:w-80">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 text-slate-400"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                </div>
                <input wire:model.live.debounce.300ms="pendingSearch" type="text" class="block w-full pl-10 pr-4 py-2 border border-slate-200 rounded-lg text-sm placeholder-slate-400 focus:border-sage focus:ring-sage" placeholder="Cari antrian surat...">
            </div>
        </div>

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
                    @forelse($pendingDocuments as $doc)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="py-3 px-4 lg:px-6">
                            <div class="font-sans text-sm font-bold text-navy">{{ $doc->document_number }}</div>
                            @if($doc->reference_number && $doc->reference_number !== $doc->document_number)
                            <div class="text-[10px] text-slate-400 font-mono mt-0.5 mb-1">Ref: {{ $doc->reference_number }}</div>
                            @endif
                            <div class="text-xs text-slate-500 line-clamp-2 mt-0.5" title="{{ $doc->subject }}">{{ $doc->subject }}</div>
                        </td>
                        <td class="py-3 px-4 lg:px-6"><span class="text-sm font-medium text-navy">{{ $doc->sender_or_receiver }}</span></td>
                        <td class="py-3 px-4 lg:px-6">
                            <div class="text-xs">
                                <span class="text-slate-500 block">Tgl Surat: <span class="font-medium text-navy">{{ $doc->document_date ? $doc->document_date->format('d M Y') : '-' }}</span></span>
                                <span class="text-slate-500 block mt-0.5">Diterima: <span class="font-medium text-navy">{{ $doc->received_date ? $doc->received_date->format('d M Y') : '-' }}</span></span>
                            </div>
                        </td>
                        <td class="py-3 px-4 lg:px-6 text-center">
                            @if($doc->file_path)
                            <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="inline-flex items-center justify-center p-1.5 bg-status-info/10 text-status-info hover:bg-status-info/20 rounded-lg transition-colors" title="Lihat/Download File">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                            </a>
                            @else
                            <span class="text-xs text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 lg:px-6 text-right">
                            @if(in_array(auth()->user()->role, ['administrator', 'sekretaris']))
                            <button wire:click="openDispositionModal({{ $doc->id }})" class="inline-flex items-center gap-1.5 px-3 py-2 bg-sage text-white text-xs font-bold rounded-lg hover:bg-sage/90 transition-colors">
                                Disposisikan
                            </button>
                            @else
                            <span class="text-xs text-slate-400">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-sm text-slate-500">
                            <div class="flex flex-col items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="w-10 h-10 text-slate-300"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                                <span>Tidak ada surat yang menunggu disposisi.</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div wire:loading wire:target="pendingSearch, pendingPerPage" class="px-6 py-4 text-center border-t border-slate-100">
            <div class="inline-flex items-center gap-2 text-sm text-slate-secondary">
                <svg class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                Memuat data...
            </div>
        </div>

        @if($pendingDocuments->hasPages())
        <div class="p-5 lg:p-6 border-t border-slate-100 bg-slate-50/30">{{ $pendingDocuments->links() }}</div>
        @endif
    </div>
    @endif

    @if($isSecretary && $showDispositionModal)
    <div class="fixed inset-0 z-[60] flex items-center justify-center p-4" x-data>
        <div class="fixed inset-0 bg-navy/50 backdrop-blur-sm" wire:click="closeDispositionModal"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-xl max-h-[90vh] overflow-y-auto z-10" @click.stop="">
            <div class="flex items-center justify-between p-6 border-b border-slate-100">
                <div>
                    <h3 class="text-lg font-bold text-navy">Buat Disposisi</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Tentukan bidang tujuan dan instruksi disposisi.</p>
                </div>
                <button wire:click="closeDispositionModal" class="p-2 text-slate-400 hover:text-navy hover:bg-slate-100 rounded-lg transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-6 space-y-5">
                @php $selectedDoc = $pendingDocuments?->firstWhere('id', $selectedDocumentId); @endphp
                @if($selectedDoc)
                <div class="bg-slate-50 rounded-xl p-4 space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Nomor Surat</p>
                            <p class="text-sm font-semibold text-navy">{{ $selectedDoc->document_number }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Tanggal Surat</p>
                            <p class="text-sm font-semibold text-navy">{{ $selectedDoc->document_date ? $selectedDoc->document_date->format('d M Y') : '-' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Perihal</p>
                            <p class="text-sm font-semibold text-navy">{{ $selectedDoc->subject }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Pengirim</p>
                            <p class="text-sm font-semibold text-navy">{{ $selectedDoc->sender_or_receiver }}</p>
                        </div>
                    </div>
                </div>
                @if($selectedDoc->file_path)
                <a href="{{ Storage::url($selectedDoc->file_path) }}" target="_blank" class="flex items-center justify-between gap-3 p-3 rounded-xl border border-sage/20 bg-sage/[0.04] hover:bg-sage/[0.08] transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sage/10 text-sage">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m5.231 13.481L15 17.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v16.5c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Zm3.75 11.625a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-navy">Berkas Surat</p>
                            <p class="text-xs text-slate-400">Klik untuk melihat dan mengunduh dokumen</p>
                        </div>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-sage"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                </a>
                @endif
                @endif

                <div>
                    <label class="block text-xs font-bold text-slate-secondary uppercase tracking-wider mb-2">Bidang Tujuan <span class="text-red-400">*</span></label>
                    <div class="space-y-1.5">
                        @foreach($departments as $department)
                        <label class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer {{ in_array($department->id, $departmentIds) ? 'border-sage/30 bg-sage/[0.04]' : 'border-slate-200 bg-white hover:border-slate-300' }}">
                            <input type="checkbox" wire:model.live="departmentIds" value="{{ $department->id }}" class="rounded border-slate-300 text-sage focus:ring-sage">
                            <span class="text-sm font-medium text-navy">{{ $department->name }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('departmentIds') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                @if(count($departmentIds) > 0)
                <div class="space-y-3">
                    <div>
                        <h4 class="text-xs font-bold text-slate-secondary uppercase tracking-wider">Instruksi per Bidang <span class="text-red-400">*</span></h4>
                    </div>
                    @foreach($departments->whereIn('id', $departmentIds) as $department)
                    <div class="rounded-xl border border-slate-200 bg-white p-4 space-y-3">
                        <p class="text-sm font-bold text-navy">{{ $department->name }}</p>
                        <select wire:model="instructions.{{ $department->id }}" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:border-sage focus:ring-sage bg-white font-medium">
                            <option value="">Pilih instruksi...</option>
                            <option value="Untuk ditindaklanjuti">Untuk ditindaklanjuti</option>
                            <option value="Untuk dipahami">Untuk dipahami</option>
                            <option value="Untuk dipelajari">Untuk dipelajari</option>
                            <option value="Untuk dijadwalkan rapat">Untuk dijadwalkan rapat</option>
                            <option value="Untuk dibuatkan tanggapan">Untuk dibuatkan tanggapan</option>
                            <option value="Untuk dikoordinasikan">Untuk dikoordinasikan</option>
                            <option value="Untuk diarsipkan">Untuk diarsipkan</option>
                            <option value="Mohon pendapat dan saran">Mohon pendapat dan saran</option>
                            <option value="Harap menjadi perhatian">Harap menjadi perhatian</option>
                        </select>
                        @error('instructions.' . $department->id) <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        <textarea wire:model="notes.{{ $department->id }}" rows="2" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:border-sage focus:ring-sage bg-white placeholder:text-slate-300" placeholder="Catatan tambahan (opsional)..."></textarea>
                        @error('notes.' . $department->id) <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            <div class="flex items-center justify-end gap-3 p-6 border-t border-slate-100 bg-slate-50/50">
                <button wire:click="closeDispositionModal" class="px-4 py-2.5 text-sm font-medium text-slate-secondary hover:text-navy bg-white border border-slate-200 rounded-xl transition-colors">Batal</button>
                <button wire:click="createDisposition" wire:loading.attr="disabled" class="px-5 py-2.5 text-sm font-bold text-white bg-sage hover:bg-sage/90 rounded-xl transition-colors disabled:opacity-50">
                    <span wire:loading.remove wire:target="createDisposition">Simpan Disposisi</span>
                    <span wire:loading wire:target="createDisposition">Menyimpan...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    @if(!$isSecretary || $activeTab === 'riwayat')
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 mb-8">
        <div class="px-5 lg:px-6 py-4 border-b border-slate-100 bg-slate-50/30">
            <h2 class="text-lg font-bold text-navy">
                @if($isOperator && $activeTab === 'baru')
                    Disposisi Baru
                @elseif($isOperator)
                    Riwayat Disposisi
                @elseif($isKepalaBadan)
                    Disposisi Masuk
                @else
                    Riwayat Disposisi
                @endif
            </h2>
            <p class="text-sm text-slate-secondary mt-1">
                @if($isOperator && $activeTab === 'baru')
                    Disposisi yang belum ditindaklanjuti oleh bidang Anda.
                @elseif($isOperator)
                    Disposisi yang sudah ditindaklanjuti oleh bidang Anda.
                @elseif($isKepalaBadan)
                    Semua disposisi surat yang telah dibuat.
                @else
                    Daftar surat yang sudah didisposisikan beserta tindak lanjutnya.
                @endif
            </p>
        </div>

        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-5 lg:p-6 gap-4 border-b border-slate-100">
            <div class="flex items-center gap-2 text-sm text-slate-secondary">
                <span class="whitespace-nowrap">Tampilkan</span>
                <select wire:model.live="dispositionPerPage" class="appearance-none border border-slate-200 rounded-lg pl-3 pr-8 py-2 text-sm focus:border-sage focus:ring-sage bg-white">
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
                <input wire:model.live.debounce.300ms="dispositionSearch" type="text" class="block w-full pl-10 pr-4 py-2 border border-slate-200 rounded-lg text-sm placeholder-slate-400 focus:border-sage focus:ring-sage" placeholder="Cari disposisi...">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50">
                        <th class="py-3 px-4 lg:px-6 text-xs font-bold text-navy uppercase tracking-wider w-1/3">Info Surat</th>
                        @if($isOperator)
                        <th class="py-3 px-4 lg:px-6 text-xs font-bold text-navy uppercase tracking-wider w-1/5">Pengirim</th>
                        <th class="py-3 px-4 lg:px-6 text-xs font-bold text-navy uppercase tracking-wider w-1/5">Tanggal</th>
                        @endif
                        <th class="py-3 px-4 lg:px-6 text-xs font-bold text-navy uppercase tracking-wider w-1/4">
                            @if($isKepalaBadan)
                                Tujuan Disposisi
                            @else
                                {{ $isSecretary ? 'Tujuan' : 'Dari' }}
                            @endif
                        </th>
                        <th class="py-3 px-4 lg:px-6 text-xs font-bold text-navy uppercase tracking-wider">Catatan</th>
                        <th class="py-3 px-4 lg:px-6 text-xs font-bold text-navy uppercase tracking-wider w-[22%]">
                            @if($isKepalaBadan)
                                Status Tindak Lanjut
                            @else
                                Tindak Lanjut
                            @endif
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($dispositions as $disposition)
                    <tr class="hover:bg-slate-50 transition-colors align-top">
                        <td class="py-3 px-4 lg:px-6">
                            <div class="font-sans text-sm font-bold text-navy">{{ $disposition->document->document_number }}</div>
                            @if($disposition->document->reference_number && $disposition->document->reference_number !== $disposition->document->document_number)
                            <div class="text-[10px] text-slate-400 font-mono mt-0.5 mb-1">Ref: {{ $disposition->document->reference_number }}</div>
                            @endif
                            <div class="text-xs text-slate-500 line-clamp-2 mt-0.5" title="{{ $disposition->document->subject }}">{{ $disposition->document->subject }}</div>
                            <div class="mt-2 flex items-center gap-2">
                                @if($disposition->document->file_path)
                                <a href="{{ Storage::url($disposition->document->file_path) }}" target="_blank" class="text-xs text-sage font-medium hover:underline">Lihat file</a>
                                @endif
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $disposition->document->status === 'sudah_disposisi' ? 'bg-green-50 text-green-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ str_replace('_', ' ', $disposition->document->status) }}
                                </span>
                            </div>
                        </td>
                        @if($isOperator)
                        <td class="py-3 px-4 lg:px-6">
                            <span class="text-sm font-medium text-navy">{{ $disposition->document->sender_or_receiver ?: '-' }}</span>
                        </td>
                        <td class="py-3 px-4 lg:px-6">
                            <div class="text-xs">
                                <span class="text-slate-500 block">Tgl Surat: <span class="font-medium text-navy">{{ $disposition->document->document_date ? $disposition->document->document_date->format('d M Y') : '-' }}</span></span>
                                <span class="text-slate-500 block mt-0.5">Diterima: <span class="font-medium text-navy">{{ $disposition->document->received_date ? $disposition->document->received_date->format('d M Y') : '-' }}</span></span>
                            </div>
                        </td>
                        @endif
                        <td class="py-3 px-4 lg:px-6 text-sm text-slate-600">
                            @if($isKepalaBadan)
                                {{ $disposition->department?->name ?? '-' }}
                            @else
                                {{ $isSecretary ? ($disposition->target_role === 'kepala_badan' ? 'Kepala Badan' : $disposition->department?->name) : $disposition->creator?->name }}
                            @endif
                        </td>
                        <td class="py-3 px-4 lg:px-6 text-sm text-slate-600">{{ $disposition->note ?: '-' }}</td>
                        <td class="py-3 px-4 lg:px-6 min-w-64">
                            @if(auth()->user()->role === 'operator' && !auth()->user()->isSekretariatOperator() && $disposition->target_role === 'department')
                            <select wire:model="followUpStatus.{{ $disposition->id }}" class="block w-full mb-2 px-3 py-2 border border-slate-200 rounded-lg text-sm focus:border-sage focus:ring-sage bg-white">
                                <option value="">Pilih status</option>
                                <option value="diterima">Diterima</option>
                                <option value="diproses">Diproses</option>
                                <option value="diteruskan_internal">Diteruskan Internal</option>
                                <option value="butuh_koordinasi">Butuh Koordinasi</option>
                                <option value="selesai">Selesai</option>
                                <option value="arsip">Arsip</option>
                            </select>
                            @error('followUpStatus.' . $disposition->id) <span class="text-xs text-red-500 -mt-1 mb-2 block">{{ $message }}</span> @enderror
                            <textarea wire:model="followUpNote.{{ $disposition->id }}" rows="2" placeholder="Keterangan tindak lanjut" class="block w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:border-sage focus:ring-sage"></textarea>
                            @error('followUpNote.' . $disposition->id) <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            <button wire:click="saveFollowUp({{ $disposition->id }})" class="mt-2 px-3 py-1.5 text-xs font-bold text-white bg-sage hover:bg-sage/90 rounded-lg transition-colors">Simpan Tindak Lanjut</button>
                            @else
                            @if($disposition->follow_up_status)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-status-info/10 text-status-info">{{ str_replace('_', ' ', $disposition->follow_up_status) }}</span>
                            @else
                            <span class="text-xs text-slate-400">Belum ada</span>
                            @endif
                            @if($disposition->follow_up_note)
                            <div class="text-xs text-slate-500 mt-1">{{ $disposition->follow_up_note }}</div>
                            @endif
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $isOperator ? 6 : 4 }}" class="py-12 text-center text-sm text-slate-500">
                            <div class="flex flex-col items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="w-10 h-10 text-slate-300"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm0 5.25h.007v.008H3.75V12Zm0 5.25h.007v.008H3.75v-.008Z"/></svg>
                                <span>{{ $isSecretary ? 'Belum ada riwayat disposisi.' : 'Tidak ada disposisi masuk.' }}</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div wire:loading wire:target="dispositionSearch, dispositionPerPage" class="px-6 py-4 text-center border-t border-slate-100">
            <div class="inline-flex items-center gap-2 text-sm text-slate-secondary">
                <svg class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                Memuat data...
            </div>
        </div>

        @if($dispositions->hasPages())
        <div class="p-5 lg:p-6 border-t border-slate-100 bg-slate-50/30">{{ $dispositions->links() }}</div>
        @endif
    </div>
    @endif
</div>
