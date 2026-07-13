<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'E-Surat' }} | Bapenda Kab. Batu Bara</title>
    <link rel="icon" type="image/png" href="/logo/logo.png">
    <link rel="shortcut icon" type="image/png" href="/logo/logo.png">
    <link rel="apple-touch-icon" href="/logo/logo.png">

    @vite(['resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen flex">
        <!-- Left Brand Panel -->
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-sage">
            <!-- TailAdmin-inspired solid green grid accent -->
            <div class="absolute inset-0 opacity-25 [background-image:linear-gradient(rgba(255,255,255,0.18)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.18)_1px,transparent_1px)] [background-size:72px_72px]"></div>
            <div class="absolute top-14 right-24 h-16 w-16 bg-white/10 border border-white/10 rounded-sm"></div>
            <div class="absolute top-28 right-40 h-16 w-16 bg-white/10 border border-white/10 rounded-sm"></div>
            <div class="absolute bottom-24 left-28 h-16 w-16 bg-white/10 border border-white/10 rounded-sm"></div>
            <div class="absolute bottom-40 left-44 h-16 w-16 bg-white/5 border border-white/10 rounded-sm"></div>

            <div class="relative z-10 flex flex-col justify-center items-center text-center p-16 w-full">
                <div class="flex items-center justify-center gap-4 mb-8">
                    <img src="/logo/logo.png" alt="Logo Kabupaten Batu Bara" class="h-20 w-auto drop-shadow-lg" />
                    <h2 class="font-display text-4xl font-bold text-white">E-Surat</h2>
                </div>
                <p class="text-lg text-white/80 leading-relaxed max-w-lg font-medium">
                    Sistem Informasi Pengelolaan Surat dan Disposisi Digital
                </p>
                <p class="text-sm text-white/65 leading-relaxed max-w-md mt-3">
                    Badan Pendapatan Daerah Kabupaten Batu Bara
                </p>
            </div>

            <p class="absolute bottom-10 left-0 right-0 text-center text-xs text-white/55">
                &copy; {{ date('Y') }} Badan Pendapatan Daerah Kabupaten Batu Bara
            </p>
        </div>

        <!-- Right Login Panel -->
        <div class="flex-1 flex items-center justify-center p-8 bg-[#F8FAFC]">
            <div class="w-full max-w-md">
                <!-- Mobile Logo -->
                <div class="lg:hidden text-center mb-10">
                    <img src="/logo/logo.png" alt="E-Surat Logo" class="h-14 w-auto mx-auto mb-3" />
                    <h2 class="font-display text-2xl font-bold text-navy">E-Surat</h2>
                    <span class="text-xs text-sage font-bold tracking-widest uppercase mt-1 block">Bapenda Batu Bara</span>
                </div>

                {{ $slot }}

                <p class="text-xs text-slate-500 text-center mt-8">
                    Hanya untuk penggunaan internal Bapenda Batu Bara.<br>
                    Hubungi IT Support jika mengalami kendala akses.
                </p>
            </div>
        </div>
    </div>

    @livewireScripts
</body>
</html>
