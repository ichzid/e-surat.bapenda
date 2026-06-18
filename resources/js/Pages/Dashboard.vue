<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage, Link } from '@inertiajs/vue3';
import { 
    InboxArrowDownIcon, 
    PaperAirplaneIcon, 
    DocumentTextIcon, 
    UsersIcon,
    PlusIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    stats: {
        type: Object,
        required: true
    },
    recent_documents: {
        type: Array,
        required: true
    },
    chart_data: {
        type: Array,
        required: true
    }
});

const page = usePage();
const user = page.props.auth.user;
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        
        <div class="max-w-7xl mx-auto w-full">
            <!-- Header Title Area -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mt-2 mb-8 gap-4">
                <div>
                    <h1 class="font-display text-2xl sm:text-3xl font-bold text-navy">Dashboard</h1>
                    <p class="font-sans text-sm sm:text-base text-slate-secondary mt-1">Ringkasan aktivitas arsip surat Anda hari ini.</p>
                </div>
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <Link v-if="user.role === 'admin'" :href="route('users.index')" class="flex-1 sm:flex-none justify-center flex items-center gap-2 bg-slate-bg hover:bg-slate-200 text-navy font-bold py-2.5 px-4 rounded-lg transition-colors border border-slate-200 shadow-sm text-xs sm:text-sm">
                        <UsersIcon class="h-4 w-4" />
                        Kelola Pengguna
                    </Link>
                    <Link :href="route('incoming.index')" class="flex-1 sm:flex-none justify-center flex items-center gap-2 bg-sage hover:bg-sage/90 text-white font-bold py-2.5 px-4 rounded-lg transition-colors shadow-sm text-xs sm:text-sm">
                        <PlusIcon class="h-4 w-4" />
                        Tambah Surat
                    </Link>
                </div>
            </div>

        <!-- KPI Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Surat Masuk -->
            <div class="bg-surface rounded-2xl shadow-DEFAULT p-6 border border-slate-100 flex flex-col justify-between h-[160px]">
                <div class="flex justify-between items-start">
                    <div class="p-2 rounded-lg bg-status-info/10">
                        <InboxArrowDownIcon class="h-5 w-5 text-status-info" />
                    </div>
                </div>
                <div>
                    <h3 class="font-sans text-xs font-medium text-slate-secondary mb-1 line-clamp-1">Total Surat Masuk</h3>
                    <p class="font-display text-2xl font-bold text-navy tracking-tight">{{ stats.incoming.toLocaleString() }}</p>
                </div>
            </div>

            <!-- Total Surat Keluar -->
            <div class="bg-surface rounded-2xl shadow-DEFAULT p-6 border border-slate-100 flex flex-col justify-between h-[160px]">
                <div class="flex justify-between items-start">
                    <div class="p-2 rounded-lg bg-status-success/10">
                        <PaperAirplaneIcon class="h-5 w-5 text-status-success" />
                    </div>
                </div>
                <div>
                    <h3 class="font-sans text-xs font-medium text-slate-secondary mb-1 line-clamp-1">Total Surat Keluar</h3>
                    <p class="font-display text-2xl font-bold text-navy tracking-tight">{{ stats.outgoing.toLocaleString() }}</p>
                </div>
            </div>

            <!-- Arsip Bulan Ini -->
            <div class="bg-surface rounded-2xl shadow-DEFAULT p-6 border border-slate-100 flex flex-col justify-between h-[160px]">
                <div class="flex justify-between items-start">
                    <div class="p-2 rounded-lg bg-sage/10">
                        <DocumentTextIcon class="h-5 w-5 text-sage" />
                    </div>
                </div>
                <div>
                    <h3 class="font-sans text-xs font-medium text-slate-secondary mb-1 line-clamp-1">Arsip Bulan Ini</h3>
                    <p class="font-display text-2xl font-bold text-navy tracking-tight">{{ stats.this_month.toLocaleString() }}</p>
                </div>
            </div>

            <!-- Total Pengguna (Admin Only) / Placeholder -->
            <div v-if="user.role === 'admin'" class="bg-surface rounded-2xl shadow-DEFAULT p-6 border border-slate-100 flex flex-col justify-between h-[160px]">
                <div class="flex justify-between items-start">
                    <div class="p-2 rounded-lg bg-status-warning/10">
                        <UsersIcon class="h-5 w-5 text-status-warning" />
                    </div>
                </div>
                <div>
                    <h3 class="font-sans text-xs font-medium text-slate-secondary mb-1 line-clamp-1">Total Pengguna Aktif</h3>
                    <p class="font-display text-2xl font-bold text-navy tracking-tight">{{ stats.users.toLocaleString() }}</p>
                </div>
            </div>
            <div v-else class="bg-surface rounded-2xl shadow-DEFAULT p-6 border border-slate-100 flex flex-col justify-center h-[160px]">
                <p class="text-sm text-center text-slate-secondary">Sistem E-Surat Bapenda</p>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Main Chart (Aktivitas Arsip) -->
            <div class="lg:col-span-2 bg-surface rounded-2xl shadow-DEFAULT p-6 border border-slate-100">
                <div class="flex justify-between items-start mb-8">
                    <div>
                        <h3 class="font-display text-lg font-bold text-navy">Aktivitas Arsip</h3>
                        <p class="font-sans text-xs text-slate-secondary mt-1">Total volume dokumen yang masuk sistem 10 hari terakhir.</p>
                    </div>
                </div>
                
                <!-- Chart Dinamik berdasarkan data database -->
                <div class="h-48 flex items-end justify-between gap-3 px-2">
                    <div v-for="bar in chart_data" :key="bar.label" class="w-full flex flex-col items-center justify-end h-full group">
                        <div class="text-[9px] font-bold text-navy opacity-0 group-hover:opacity-100 transition-opacity mb-1">
                            {{ bar.count }}
                        </div>
                        <div 
                            class="w-full rounded-t-sm transition-all duration-300" 
                            :class="[bar.is_today ? 'bg-sage shadow-[0_0_10px_rgba(5,150,105,0.3)]' : 'bg-slate-200 group-hover:bg-slate-300']"
                            :style="{ height: bar.count > 0 ? Math.max((bar.count / Math.max(...chart_data.map(d => d.count), 1)) * 80, 8) + '%' : '4%' }"
                        ></div>
                        <span class="text-[9px] text-slate-400 font-medium mt-2">{{ bar.label }}</span>
                    </div>
                </div>
            </div>

            <!-- Side Panel (Informasi Ringkas) -->
            <div class="lg:col-span-1 bg-surface rounded-2xl shadow-DEFAULT p-6 border border-slate-100 flex flex-col justify-between">
                <div>
                    <h3 class="font-display text-lg font-bold text-navy">Ringkasan Sistem</h3>
                    <p class="font-sans text-xs text-slate-secondary mt-1 mb-6">Status terkini e-surat</p>

                    <div class="space-y-5">
                        <div class="bg-slate-50 border border-slate-100 rounded-xl p-4">
                            <h4 class="font-sans text-xs font-bold text-navy mb-1 uppercase tracking-wider">Aktivitas Pegawai</h4>
                            <p class="font-sans text-xs text-slate-secondary">Ada {{ stats.users }} pegawai yang memiliki akses ke dalam sistem E-Surat Bapenda pada saat ini.</p>
                        </div>
                    </div>
                </div>

                <!-- AI Insight Banner -->
                <div class="mt-8 bg-sage/5 border border-sage/20 rounded-xl p-4 flex items-start gap-3">
                    <SparklesIcon class="h-5 w-5 text-sage shrink-0 mt-0.5" />
                    <p class="text-xs font-medium text-sage leading-relaxed">Sistem Berjalan Optimal: Backup otomatis terakhir kali dilakukan dengan aman pada pukul 02:00 dini hari.</p>
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="bg-surface rounded-2xl shadow-DEFAULT p-6 border border-slate-100 mb-8">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-display text-lg font-bold text-navy">Arsip Surat Terbaru</h3>
                <Link :href="route('incoming.index')" class="font-sans text-xs font-bold text-sage hover:text-navy transition-colors">Lihat Semua Arsip</Link>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200">
                            <th class="py-3 px-2 text-xs font-bold text-navy uppercase tracking-wider w-[30%]">No. Referensi</th>
                            <th class="py-3 px-2 text-xs font-bold text-navy uppercase tracking-wider w-[15%]">Tipe</th>
                            <th class="py-3 px-2 text-xs font-bold text-navy uppercase tracking-wider w-[35%]">Pengirim / Penerima</th>
                            <th class="py-3 px-2 text-xs font-bold text-navy uppercase tracking-wider w-[20%]">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-if="recent_documents.length === 0">
                            <td colspan="4" class="py-6 text-center text-sm text-slate-500">Belum ada data arsip.</td>
                        </tr>
                        <tr v-for="doc in recent_documents" :key="doc.id" class="hover:bg-slate-50 transition-colors">
                            <td class="py-4 px-2">
                                <span class="font-sans text-sm font-bold text-navy">{{ doc.reference_number }}</span>
                            </td>
                            <td class="py-4 px-2">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold tracking-wider" 
                                      :class="doc.type === 'incoming' ? 'bg-status-info/10 text-status-info' : 'bg-status-success/10 text-status-success'">
                                    <span class="w-1.5 h-1.5 rounded-full" :class="doc.type === 'incoming' ? 'bg-status-info' : 'bg-status-success'"></span>
                                    {{ doc.type === 'incoming' ? 'MASUK' : 'KELUAR' }}
                                </span>
                            </td>
                            <td class="py-4 px-2">
                                <div class="text-sm text-navy truncate font-medium">{{ doc.sender_or_receiver }}</div>
                                <div class="text-xs text-slate-secondary truncate">{{ doc.subject }}</div>
                            </td>
                            <td class="py-4 px-2 text-sm text-slate-secondary">{{ doc.date }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer Note -->
        <div class="text-center pb-4">
            <p class="text-[10px] text-slate-400 font-medium tracking-wide">© 2026 E-Surat Bapenda. All rights reserved.</p>
        </div>
        </div>

    </AuthenticatedLayout>
</template>
