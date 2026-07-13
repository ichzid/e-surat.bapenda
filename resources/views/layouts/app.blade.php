<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $pageTitle = $title ?? match (true) {
            request()->routeIs('dashboard') => 'Dashboard',
            request()->routeIs('incoming.*') => 'Surat Masuk',
            request()->routeIs('outgoing.*') => 'Surat Keluar',
            request()->routeIs('dispositions.*') => 'Disposisi',
            request()->routeIs('reports.*') => 'Laporan',
            request()->routeIs('users.*') => 'Master User',
            request()->routeIs('profile.*') => 'Profil Akun',
            default => 'Dashboard',
        };
    @endphp
    <title>{{ $pageTitle }} | E-Surat Bapenda</title>
    <link rel="icon" type="image/png" href="/logo/logo.png">
    <link rel="shortcut icon" type="image/png" href="/logo/logo.png">
    <link rel="apple-touch-icon" href="/logo/logo.png">

    @vite(['resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-[#F8FAFC] text-navy">
    <div x-data="{ sidebarOpen: false, profileOpen: false }" class="flex h-screen overflow-hidden">
        
        <!-- Mobile Overlay -->
        <div 
            x-show="sidebarOpen" 
            x-on:click="sidebarOpen = false"
            class="fixed inset-0 bg-navy/50 z-40 lg:hidden transition-opacity"
            x-transition.opacity
        ></div>

        <!-- Sidebar -->
        <aside 
            class="fixed inset-y-0 left-0 z-50 w-[260px] bg-white border-r border-slate-200 flex flex-col flex-shrink-0 transition-transform duration-300 ease-in-out lg:static lg:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <!-- Logo Area -->
            <div class="h-16 lg:h-24 flex items-center justify-between px-6 lg:px-8 border-b border-slate-100 lg:border-none">
                <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-3">
                    <img src="/logo/logo.png" alt="E-Surat Logo" class="h-8 lg:h-10 w-auto" />
                    <div class="flex flex-col">
                        <span class="font-display text-lg lg:text-xl font-bold leading-none text-navy">E-Surat</span>
                        <span class="text-[9px] lg:text-[10px] text-slate-secondary font-medium tracking-widest uppercase mt-1">Bapenda Kab. Batu Bara</span>
                    </div>
                </a>
                <button x-on:click="sidebarOpen = false" class="lg:hidden p-2 text-slate-400 hover:text-navy hover:bg-slate-50 rounded-lg transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Navigation -->
            @php
                $user = auth()->user();
                $isAdministrator = $user->role === 'administrator' || $user->role === 'admin';
                $isOperatorSekretariat = $user->isSekretariatOperator();
                $isOperatorBidang = $user->role === 'operator' && !$isOperatorSekretariat;
                $isSekretaris = $user->role === 'sekretaris';
                $isKepalaBadan = $user->role === 'kepala_badan';

                $canSeeIncoming = $isAdministrator || $isOperatorSekretariat;
                $canSeeOutgoing = $isAdministrator || $isOperatorSekretariat;
                $canSeeReports = $isAdministrator || $isOperatorSekretariat || $isKepalaBadan;
                $canSeeUsers = $isAdministrator;

                // Badge counters
                $dispositionBadge = 0;
                if ($isSekretaris || $isAdministrator || $isOperatorSekretariat) {
                    $dispositionBadge = \App\Models\Document::where('type', 'incoming')
                        ->where('status', 'menunggu_disposisi')->count();
                } elseif ($isOperatorBidang) {
                    $dispositionBadge = \App\Models\Disposition::where('department_id', $user->department_id)
                        ->where('target_role', 'department')
                        ->where(function ($q) {
                            $q->whereNull('follow_up_status')
                              ->orWhere('follow_up_status', '!=', 'selesai');
                        })->count();
                }
            @endphp
            <nav class="flex-1 px-4 py-6 overflow-y-auto">
                <!-- Menu Utama -->
                <div class="mb-2 px-4">
                    <h3 class="text-xs font-bold tracking-wider text-slate-400 uppercase">Menu Utama</h3>
                </div>
                <div class="space-y-1">
                    <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-sage/10 text-sage' : 'text-slate-secondary hover:bg-slate-50 hover:text-navy' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6Zm0 9.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6Zm0 9.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z"/></svg>
                        Dashboard
                    </a>
                </div>

                <!-- Manajemen Arsip -->
                <div class="mt-6 mb-2 px-4">
                    <h3 class="text-xs font-bold tracking-wider text-slate-400 uppercase">Manajemen Arsip</h3>
                </div>
                <div class="space-y-1">
                    @if($canSeeIncoming)
                    <a href="{{ route('incoming.index') }}" wire:navigate class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('incoming.*') ? 'bg-sage/10 text-sage' : 'text-slate-secondary hover:bg-slate-50 hover:text-navy' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 3.75H6.912a2.25 2.25 0 0 0-2.15 1.588L2.35 13.177a2.25 2.25 0 0 0-.1.661V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 0 0-2.15-1.588H15M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859M12 3v8.25m0 0-3-3m3 3 3-3"/></svg>
                        Surat Masuk
                    </a>
                    @endif

                    @if($canSeeOutgoing)
                    <a href="{{ route('outgoing.index') }}" wire:navigate class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('outgoing.*') ? 'bg-sage/10 text-sage' : 'text-slate-secondary hover:bg-slate-50 hover:text-navy' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5"/></svg>
                        Surat Keluar
                    </a>
                    @endif

                    <a href="{{ route('dispositions.index') }}" wire:navigate class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('dispositions.*') ? 'bg-sage/10 text-sage' : 'text-slate-secondary hover:bg-slate-50 hover:text-navy' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm0 5.25h.007v.008H3.75V12Zm0 5.25h.007v.008H3.75v-.008Z"/></svg>
                        Disposisi
                        <livewire:sidebar-badge />
                    </a>

                    @if($canSeeReports)
                    <a href="{{ route('reports.index') }}" wire:navigate class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('reports.*') ? 'bg-sage/10 text-sage' : 'text-slate-secondary hover:bg-slate-50 hover:text-navy' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25M9 16.5v.75m3-3v3M15 12v5.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                        Laporan
                    </a>
                    @endif
                </div>

                <!-- Pengaturan -->
                <div class="mt-6 mb-2 px-4">
                    <h3 class="text-xs font-bold tracking-wider text-slate-400 uppercase">Pengaturan</h3>
                </div>
                <div class="space-y-1">
                    @if(auth()->user()->role === 'administrator')
                    <a href="{{ route('users.index') }}" wire:navigate class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('users.*') ? 'bg-sage/10 text-sage' : 'text-slate-secondary hover:bg-slate-50 hover:text-navy' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/></svg>
                        Master Data User
                    </a>
                    @endif
                    <a href="{{ route('profile.edit') }}" wire:navigate class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('profile.*') ? 'bg-sage/10 text-sage' : 'text-slate-secondary hover:bg-slate-50 hover:text-navy' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                        Profil Akun
                    </a>
                </div>
            </nav>

            <!-- Footer -->
            <div class="p-4 border-t border-slate-100">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-slate-secondary hover:bg-red-50 hover:text-red-600 transition-all duration-200 w-full">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9"/></svg>
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Topbar -->
            <header class="h-16 lg:h-24 flex items-center justify-between px-4 lg:px-10 flex-shrink-0 bg-white lg:bg-transparent border-b border-slate-200 lg:border-none z-30">
                <div class="flex items-center gap-2 lg:hidden">
                    <button x-on:click="sidebarOpen = true" class="p-2 text-slate-500 hover:text-navy hover:bg-slate-100 rounded-lg transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
                    </button>
                    <span class="font-display text-lg font-bold text-navy ml-2">E-Surat</span>
                </div>

                <!-- Search (Desktop) -->
                <div class="relative w-full max-w-[400px] search-container z-50 hidden md:block">
                    <livewire:global-search />
                </div>

                <!-- Right Menu -->
                <div class="flex items-center gap-3 lg:gap-6">
                    <div class="relative" x-data="{ open: false }">
                        <button x-on:click="open = !open" class="flex items-center gap-2 lg:gap-3 lg:border-l border-slate-200 lg:pl-6 text-left hover:bg-slate-50 p-1 lg:p-2 rounded-xl transition-colors focus:outline-none">
                            <div class="hidden lg:flex flex-col text-right">
                                <span class="text-sm font-bold text-navy">{{ auth()->user()->name }}</span>
                                <span class="text-xs font-medium text-slate-secondary capitalize">{{ auth()->user()->role }}</span>
                            </div>
                            <div class="h-8 w-8 lg:h-10 lg:w-10 rounded-full bg-navy flex items-center justify-center text-white font-bold overflow-hidden ring-2 ring-transparent transition-all text-sm lg:text-base">
                                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                            </div>
                        </button>

                        <div x-show="open" x-on:click.outside="open = false" x-transition class="absolute right-0 mt-3 w-56 bg-white rounded-xl shadow-lg border border-slate-200 py-2 z-50">
                            <a href="{{ route('profile.edit') }}" wire:navigate class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-secondary hover:bg-slate-50 hover:text-navy transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                                Profil Akun
                            </a>
                            <hr class="my-2 border-slate-100">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex items-center gap-3 px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 transition-colors w-full text-left">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9"/></svg>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto px-4 lg:px-10 pb-10">
                <!-- Toast Notifications -->
                <div
                    x-data="{
                        show: false,
                        type: 'success',
                        title: 'Berhasil',
                        message: '',
                        timeout: null,
                        openToast(event) {
                            this.type = event.detail.type || 'success';
                            this.title = event.detail.title || (this.type === 'success' ? 'Berhasil' : 'Terjadi Kesalahan');
                            this.message = event.detail.message || '';
                            this.show = true;
                            clearTimeout(this.timeout);
                            this.timeout = setTimeout(() => this.show = false, this.type === 'success' ? 4000 : 5000);
                        }
                    }"
                    x-on:toast.window="openToast($event)"
                    x-init="@if(session('message') || session('success')) openToast({ detail: { type: 'success', message: @js(session('message') ?? session('success')) } }); @endif @if($errors->any() || session('error')) openToast({ detail: { type: 'error', message: @js(session('error') ?? $errors->first()) } }); @endif"
                    x-show="show"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-2 sm:translate-y-0 sm:translate-x-4"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0 translate-y-2 sm:translate-y-0 sm:translate-x-4"
                    class="fixed top-5 right-5 z-[80] w-[calc(100%-2.5rem)] sm:w-auto sm:min-w-[360px] sm:max-w-md overflow-hidden rounded-2xl shadow-xl border bg-white"
                    x-bind:class="type === 'success' ? 'border-sage/25 shadow-sage/10' : 'border-red-200 shadow-red-100'"
                    style="display: none;"
                >
                    <div class="absolute inset-0" x-bind:class="type === 'success' ? 'bg-gradient-to-r from-sage/10 via-sage/5 to-white' : 'bg-gradient-to-r from-red-50 via-red-50/60 to-white'"></div>
                    <div class="relative p-4 flex items-start gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl shadow-sm" x-bind:class="type === 'success' ? 'bg-sage text-white' : 'bg-red-500 text-white'">
                            <template x-if="type === 'success'">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                            </template>
                            <template x-if="type !== 'success'">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008v.008H12v-.008Zm9-4.5a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                            </template>
                        </div>
                        <div class="min-w-0 flex-1 pr-2">
                            <p class="text-sm font-bold text-navy" x-text="title"></p>
                            <p class="text-sm text-slate-secondary mt-0.5" x-text="message"></p>
                        </div>
                        <button x-on:click="show = false" class="text-slate-400 hover:text-navy transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    <div class="relative h-1 w-full bg-slate-100">
                        <div x-show="show" x-bind:class="type === 'success' ? 'bg-sage' : 'bg-red-500'" x-bind:style="`animation-duration: ${type === 'success' ? 4000 : 5000}ms`" class="h-full origin-left animate-[toast-progress_linear_forwards]"></div>
                    </div>
                </div>

                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
</body>
</html>
