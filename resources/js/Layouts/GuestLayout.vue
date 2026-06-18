<script setup>
import { Link } from '@inertiajs/vue3';
import { ShieldCheckIcon, DocumentMagnifyingGlassIcon, ServerStackIcon } from '@heroicons/vue/24/outline';
</script>

<template>
    <div class="flex min-h-screen font-sans text-navy bg-white">
        <!-- Left Side (Branding/Illustration) -->
        <div class="hidden lg:flex lg:w-1/2 bg-navy relative overflow-hidden flex-col justify-between p-12 lg:p-16">
            <!-- Solid Corporate Background -->
            <div class="absolute inset-0 bg-navy"></div>
            <!-- Very subtle geometric pattern for texture -->
            <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHBhdGggZD0iTTAgMGg0MHY0MEgweiIgZmlsbD0ibm9uZSIvPjxwYXRoIGQ9Ik0wIDEwaDQwTTEwIDB2NDBNMCAzMGg0ME0zMCAwdjQwIiBzdHJva2U9InJnYmEoMjU1LDI1NSwyNTUsMC4wNCkiIHN0cm9rZS13aWR0aD0iMSIvPjwvc3ZnPg==')] opacity-50"></div>

            <!-- Top Header Logo -->
            <div class="relative z-10 flex items-center gap-4">
                <div>
                    <img src="/logo/logo.png" alt="Logo Bapenda" class="h-16 w-auto" />
                </div>
                <div class="flex flex-col">
                    <span class="font-display text-2xl font-bold text-white tracking-tight">E-Surat</span>
                    <span class="text-xs text-slate-300 font-medium tracking-widest uppercase mt-0.5">Badan Pendapatan Daerah Kabupaten Batu Bara</span>
                </div>
            </div>

            <!-- Middle Content -->
            <div class="relative z-10 max-w-lg my-auto">
                <div class="mb-8">
                    <h1 class="font-display text-4xl font-bold text-white mb-6 leading-tight">
                        Arsip Digital Terpadu Bapenda Batu Bara.
                    </h1>
                    <p class="text-slate-300 text-base leading-relaxed mb-10">
                        Platform resmi pengelolaan dokumen elektronik Badan Pendapatan Daerah Kabupaten Batu Bara. Dirancang untuk meningkatkan efisiensi tata usaha dan keamanan arsip instansi.
                    </p>
                </div>

                <!-- Structured Features List -->
                <div class="space-y-6 border-t border-white/10 pt-8">
                    <div class="flex gap-4 items-start">
                        <div class="p-2 rounded bg-white/5 border border-white/10 text-sage shrink-0 mt-0.5">
                            <ShieldCheckIcon class="h-5 w-5" />
                        </div>
                        <div>
                            <h3 class="text-white font-medium text-sm mb-1">Infrastruktur Aman</h3>
                            <p class="text-slate-400 text-xs leading-relaxed">Sistem dilengkapi autentikasi berjenjang untuk mencegah akses yang tidak sah.</p>
                        </div>
                    </div>
                    <div class="flex gap-4 items-start">
                        <div class="p-2 rounded bg-white/5 border border-white/10 text-sage shrink-0 mt-0.5">
                            <DocumentMagnifyingGlassIcon class="h-5 w-5" />
                        </div>
                        <div>
                            <h3 class="text-white font-medium text-sm mb-1">Penelusuran Terpusat</h3>
                            <p class="text-slate-400 text-xs leading-relaxed">Pencarian dokumen keluar dan masuk yang lebih terstruktur dan rapi.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Footer -->
            <div class="relative z-10 flex items-center justify-between border-t border-white/10 pt-6 mt-12">
                <div class="text-slate-400 text-xs font-medium uppercase tracking-wider">
                    &copy; {{ new Date().getFullYear() }} Badan Pendapatan Daerah Kabupaten Batu Bara
                </div>
                <div class="text-xs text-slate-500 font-mono">
                    v2.0.1
                </div>
            </div>
        </div>

        <!-- Right Side (Login Form) -->
        <div class="w-full lg:w-1/2 flex items-center justify-center bg-[#F8FAFC] p-8 sm:p-12 relative border-l border-slate-200">
            <div class="w-full max-w-[420px] relative z-10">
                <!-- Mobile Branding (Hidden on lg) -->
                <div class="lg:hidden flex flex-col items-center mb-10">
                    <img src="/logo/logo.png" alt="Logo" class="h-20 w-auto mb-4" />
                    <div class="flex flex-col items-center text-center">
                        <span class="font-display text-3xl font-bold text-navy leading-tight">E-Surat</span>
                        <span class="text-xs text-sage font-bold tracking-widest uppercase mt-1">Badan Pendapatan Daerah Kabupaten Batu Bara</span>
                    </div>
                </div>

                <!-- Form Container -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8 sm:p-10 relative">
                    <!-- Solid Top Border -->
                    <div class="absolute top-0 left-0 w-full h-1 bg-navy rounded-t-xl"></div>
                    
                    <slot />
                </div>
                
                <!-- Help Text below form -->
                <div class="mt-8 text-center">
                    <p class="text-xs text-slate-500">
                        Hanya untuk penggunaan internal Bapenda Batu Bara.<br>
                        Hubungi IT Support jika mengalami kendala akses.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
