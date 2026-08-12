<div>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between mt-2 mb-8 gap-4">
        <div>
            <h1 class="font-display text-2xl sm:text-3xl font-bold text-navy">Dashboard</h1>
            <p class="font-sans text-sm sm:text-base text-slate-secondary mt-1">
                @if($isOperator)
                    Ringkasan disposisi untuk bidang Anda.
                @elseif($isSekretaris)
                    Ringkasan antrian dan riwayat disposisi.
                @elseif($isKepalaBadan)
                    Ringkasan arsip dan disposisi surat.
                @else
                    Ringkasan aktivitas arsip surat.
                @endif
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-8">
        {{-- Admin / Sekretariat Op / Kaban --}}
        @if($isAdmin || $isSekretariatOp || $isKepalaBadan)
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 lg:p-6">
            <p class="text-sm font-medium text-slate-secondary">Total Surat Masuk</p>
            <p class="text-3xl lg:text-4xl font-bold text-navy mt-1">{{ $stats['total_incoming'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 lg:p-6">
            <p class="text-sm font-medium text-slate-secondary">Total Surat Keluar</p>
            <p class="text-3xl lg:text-4xl font-bold text-navy mt-1">{{ $stats['total_outgoing'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 lg:p-6">
            <p class="text-sm font-medium text-slate-secondary">Menunggu Disposisi</p>
            <p class="text-3xl lg:text-4xl font-bold text-amber-600 mt-1">{{ $stats['pending_disposition'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 lg:p-6">
            <p class="text-sm font-medium text-slate-secondary">Sudah Disposisi</p>
            <p class="text-3xl lg:text-4xl font-bold text-green-600 mt-1">{{ $stats['done_disposition'] ?? 0 }}</p>
        </div>
        @endif

        {{-- Sekretaris --}}
        @if($isSekretaris)
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 lg:p-6">
            <p class="text-sm font-medium text-slate-secondary">Menunggu Disposisi</p>
            <p class="text-3xl lg:text-4xl font-bold text-amber-600 mt-1">{{ $stats['pending_disposition'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 lg:p-6">
            <p class="text-sm font-medium text-slate-secondary">Sudah Disposisi</p>
            <p class="text-3xl lg:text-4xl font-bold text-green-600 mt-1">{{ $stats['done_disposition'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 lg:p-6">
            <p class="text-sm font-medium text-slate-secondary">Total Disposisi</p>
            <p class="text-3xl lg:text-4xl font-bold text-navy mt-1">{{ $stats['total_dispositions'] ?? 0 }}</p>
        </div>
        @endif

        {{-- Operator Bidang --}}
        @if($isOperator)
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 lg:p-6">
            <p class="text-sm font-medium text-slate-secondary">Disposisi Baru</p>
            <p class="text-3xl lg:text-4xl font-bold text-amber-600 mt-1">{{ $stats['disposisi_baru'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 lg:p-6">
            <p class="text-sm font-medium text-slate-secondary">Dalam Proses</p>
            <p class="text-3xl lg:text-4xl font-bold text-blue-600 mt-1">{{ $stats['disposisi_proses'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 lg:p-6">
            <p class="text-sm font-medium text-slate-secondary">Selesai</p>
            <p class="text-3xl lg:text-4xl font-bold text-green-600 mt-1">{{ $stats['disposisi_selesai'] ?? 0 }}</p>
        </div>
        @endif
    </div>

    {{-- Recent Activity --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-8">
        <div class="p-5 lg:p-6 border-b border-slate-100">
            <h2 class="text-lg font-bold text-navy">
                @if($isOperator || $isSekretaris || $isKepalaBadan)
                    Disposisi Terbaru
                @else
                    Dokumen Terbaru
                @endif
            </h2>
        </div>

        <div class="overflow-x-auto">
            @if($isAdmin || $isSekretariatOp)
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50">
                        <th class="py-3 px-4 lg:px-6 text-xs font-bold text-navy uppercase tracking-wider">No. Referensi</th>
                        <th class="py-3 px-4 lg:px-6 text-xs font-bold text-navy uppercase tracking-wider">Tipe</th>
                        <th class="py-3 px-4 lg:px-6 text-xs font-bold text-navy uppercase tracking-wider hidden sm:table-cell">Perihal</th>
                        <th class="py-3 px-4 lg:px-6 text-xs font-bold text-navy uppercase tracking-wider hidden md:table-cell">Pengirim/Penerima</th>
                        <th class="py-3 px-4 lg:px-6 text-xs font-bold text-navy uppercase tracking-wider hidden lg:table-cell">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($recentItems as $doc)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-5 lg:px-6 py-3.5 font-mono text-xs text-navy font-medium">{{ $doc->reference_number }}</td>
                        <td class="px-5 lg:px-6 py-3.5">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $doc->type === 'incoming' ? 'bg-blue-50 text-blue-700' : 'bg-amber-50 text-amber-700' }}">
                                {{ $doc->type === 'incoming' ? 'Masuk' : 'Keluar' }}
                            </span>
                        </td>
                        <td class="px-5 lg:px-6 py-3.5 text-navy font-medium max-w-[200px] truncate hidden sm:table-cell">{{ $doc->subject ?? '-' }}</td>
                        <td class="px-5 lg:px-6 py-3.5 text-slate-secondary hidden md:table-cell">{{ $doc->sender_or_receiver ?? '-' }}</td>
                        <td class="px-5 lg:px-6 py-3.5 hidden lg:table-cell">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $doc->status === 'menunggu_disposisi' ? 'bg-amber-50 text-amber-700' : ($doc->status === 'sudah_disposisi' ? 'bg-green-50 text-green-700' : 'bg-slate-100 text-slate-600') }}">
                                {{ str_replace('_', ' ', $doc->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-secondary">Belum ada dokumen.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            @else
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50">
                        <th class="py-3 px-4 lg:px-6 text-xs font-bold text-navy uppercase tracking-wider">No. Surat</th>
                        <th class="py-3 px-4 lg:px-6 text-xs font-bold text-navy uppercase tracking-wider">Perihal</th>
                        <th class="py-3 px-4 lg:px-6 text-xs font-bold text-navy uppercase tracking-wider hidden sm:table-cell">Bidang</th>
                        <th class="py-3 px-4 lg:px-6 text-xs font-bold text-navy uppercase tracking-wider hidden md:table-cell">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($recentItems as $d)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-5 lg:px-6 py-3.5">
                            <span class="font-mono text-xs text-navy font-medium">
                                {{ $d->document->document_number ?? '-' }}
                                @if(isset($d->document) && $d->document->trashed())
                                <span class="ml-1.5 inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-red-50 text-red-700 border border-red-100">
                                    Dihapus
                                </span>
                                @endif
                            </span>
                            @if(isset($d->document) && $d->document->reference_number && $d->document->reference_number !== $d->document->document_number)
                            <div class="text-[10px] text-slate-400 font-mono">Ref: {{ $d->document->reference_number }}</div>
                            @endif
                        </td>
                        <td class="px-5 lg:px-6 py-3.5 text-navy font-medium max-w-[200px] truncate">{{ $d->document->subject ?? '-' }}</td>
                        <td class="px-5 lg:px-6 py-3.5 text-slate-secondary hidden sm:table-cell">{{ $isKepalaBadan ? ($d->creator?->name ?? '-') : ($d->department?->name ?? '-') }}</td>
                        <td class="px-5 lg:px-6 py-3.5 hidden md:table-cell">
                            @if($d->follow_up_status)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-status-info/10 text-status-info">{{ str_replace('_', ' ', $d->follow_up_status) }}</span>
                            @else
                            <span class="text-xs text-amber-600 font-medium">Belum ditindaklanjuti</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-slate-secondary">Belum ada disposisi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            @endif
        </div>
    </div>
</div>
