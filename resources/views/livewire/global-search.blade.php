<div class="relative w-full max-w-md">
    <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
            </svg>
        </div>
        <input
            wire:model.live.debounce.300ms="query"
            type="text"
            placeholder="Cari surat atau pengguna..."
            class="block w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl text-sm placeholder-slate-400 focus:border-sage focus:ring-sage bg-white shadow-sm transition-colors"
        >
    </div>

    @if (!empty($results))
        <div class="absolute z-50 mt-2 w-full bg-white rounded-xl shadow-lg border border-slate-100 overflow-hidden">
            <div class="py-2 max-h-80 overflow-y-auto">
                @foreach ($results as $result)
                    @if ($result['type'] === 'document')
                        <a href="{{ $result['doc_type'] === 'incoming' ? route('incoming.show', $result['id']) : route('outgoing.show', $result['id']) }}"
                           class="flex items-start gap-3 px-4 py-3 hover:bg-slate-50 transition-colors">
                            <div class="p-1.5 rounded-lg {{ $result['doc_type'] === 'incoming' ? 'bg-blue-50 text-blue-600' : 'bg-emerald-50 text-emerald-600' }} shrink-0 mt-0.5">
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-navy truncate">{{ $result['title'] }}</p>
                                <p class="text-xs text-slate-500 truncate">{{ $result['subtitle'] }}</p>
                            </div>
                            <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $result['doc_type'] === 'incoming' ? 'bg-blue-50 text-blue-700' : 'bg-emerald-50 text-emerald-700' }} shrink-0">
                                {{ $result['doc_type'] === 'incoming' ? 'Masuk' : 'Keluar' }}
                            </span>
                        </a>
                    @elseif ($result['type'] === 'user' && auth()->check() && auth()->user()->role === 'administrator')
                        <a href="{{ route('users.edit', $result['id']) }}"
                           class="flex items-start gap-3 px-4 py-3 hover:bg-slate-50 transition-colors">
                            <div class="p-1.5 rounded-lg bg-purple-50 text-purple-600 shrink-0 mt-0.5">
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-navy truncate">{{ $result['title'] }}</p>
                                <p class="text-xs text-slate-500 truncate">{{ $result['subtitle'] }}</p>
                            </div>
                            <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-purple-50 text-purple-700 shrink-0 capitalize">
                                {{ $result['role'] }}
                            </span>
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
    @endif
</div>
