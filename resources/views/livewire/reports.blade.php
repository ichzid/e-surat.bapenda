<div>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between mt-2 mb-8 gap-4">
        <div>
            <h1 class="font-display text-2xl sm:text-3xl font-bold text-navy">Laporan Arsip</h1>
            <p class="font-sans text-sm sm:text-base text-slate-secondary mt-1">Filter dan export laporan arsip surat.</p>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 lg:p-6 mb-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
            <div>
                <label for="year" class="block text-sm font-medium text-slate-secondary mb-1.5">Tahun</label>
                <select wire:model.live="year" class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm text-navy bg-white focus:border-sage focus:ring-1 focus:ring-sage transition-colors">
                    <option value="all">Semua Tahun</option>
                    @for($y = 2020; $y <= 2030; $y++)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label for="month" class="block text-sm font-medium text-slate-secondary mb-1.5">Bulan</label>
                <select wire:model.live="month" class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm text-navy bg-white focus:border-sage focus:ring-1 focus:ring-sage transition-colors">
                    <option value="all">Semua Bulan</option>
                    @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $label)
                        <option value="{{ $i + 1 }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="type" class="block text-sm font-medium text-slate-secondary mb-1.5">Kategori</label>
                <select wire:model.live="type" class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm text-navy bg-white focus:border-sage focus:ring-1 focus:ring-sage transition-colors">
                    <option value="all">Semua Kategori</option>
                    <option value="incoming">Surat Masuk</option>
                    <option value="outgoing">Surat Keluar</option>
                </select>
            </div>
            <div>
                <button wire:click="export" class="inline-flex items-center justify-center gap-2 bg-sage hover:bg-sage/90 text-white font-bold py-2.5 px-4 rounded-lg transition-colors shadow-sm text-sm w-full">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                    Export Excel
                </button>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 mb-8">
        <div class="px-5 lg:px-6 py-4 border-b border-slate-100 bg-slate-50/30">
            <h2 class="text-lg font-bold text-navy">Hasil Laporan</h2>
        </div>

        <div class="flex items-center p-5 lg:p-6 gap-4 border-b border-slate-100">
            <div class="flex items-center gap-2 text-sm text-slate-secondary">
                <span class="whitespace-nowrap">Tampilkan</span>
                <select wire:model.live="perPage" class="appearance-none border border-slate-200 rounded-lg pl-3 pr-8 py-2 text-sm focus:border-sage focus:ring-sage bg-white">
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span class="whitespace-nowrap">data</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50">
                        <th class="py-3 px-4 lg:px-6 text-xs font-bold text-navy uppercase tracking-wider w-1/3">Info Surat</th>
                        <th class="py-3 px-4 lg:px-6 text-xs font-bold text-navy uppercase tracking-wider text-center">Jenis</th>
                        <th class="py-3 px-4 lg:px-6 text-xs font-bold text-navy uppercase tracking-wider w-1/4">Penerima/Pengirim</th>
                        <th class="py-3 px-4 lg:px-6 text-xs font-bold text-navy uppercase tracking-wider">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($documents as $index => $doc)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="py-3 px-4 lg:px-6">
                            <div class="font-sans text-sm font-bold text-navy">{{ $doc->document_number }}</div>
                            @if($doc->reference_number && $doc->reference_number !== $doc->document_number)
                            <div class="text-[10px] text-slate-400 font-mono mt-0.5 mb-1">Ref: {{ $doc->reference_number }}</div>
                            @endif
                            <div class="text-xs text-slate-500 line-clamp-2 mt-0.5" title="{{ $doc->subject }}">{{ $doc->subject }}</div>
                        </td>
                        <td class="py-3 px-4 lg:px-6 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $doc->type === 'incoming' ? 'bg-blue-50 text-blue-700' : 'bg-amber-50 text-amber-700' }}">
                                {{ $doc->type === 'incoming' ? 'Masuk' : 'Keluar' }}
                            </span>
                        </td>
                        <td class="py-3 px-4 lg:px-6"><span class="text-sm font-medium text-navy">{{ $doc->sender_or_receiver ?? '-' }}</span></td>
                        <td class="py-3 px-4 lg:px-6">
                            <div class="text-xs">
                                <span class="text-slate-500 block">Tgl Surat: <span class="font-medium text-navy">{{ $doc->document_date ? $doc->document_date->format('d M Y') : '-' }}</span></span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-12 text-center text-sm text-slate-500">
                            Tidak ada data laporan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div wire:loading wire:target="year, month, type, perPage" class="px-6 py-4 text-center border-t border-slate-100">
            <div class="inline-flex items-center gap-2 text-sm text-slate-secondary">
                <svg class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                Memuat data...
            </div>
        </div>

        @if($documents->hasPages())
        <div class="p-5 lg:p-6 border-t border-slate-100 bg-slate-50/30">{{ $documents->links() }}</div>
        @endif
    </div>
</div>
