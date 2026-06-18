<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';
import {
    DocumentChartBarIcon,
    ArrowDownTrayIcon
} from '@heroicons/vue/24/outline';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    documents: Object,
    filters: Object
});

const form = ref({
    year: props.filters.year || 'all',
    month: props.filters.month || 'all',
    type: props.filters.type || 'all'
});

// Generate years for dropdown
const currentYear = new Date().getFullYear();
const years = Array.from({ length: 5 }, (_, i) => currentYear - i);

// Months data
const months = [
    { value: '1', label: 'Januari' },
    { value: '2', label: 'Februari' },
    { value: '3', label: 'Maret' },
    { value: '4', label: 'April' },
    { value: '5', label: 'Mei' },
    { value: '6', label: 'Juni' },
    { value: '7', label: 'Juli' },
    { value: '8', label: 'Agustus' },
    { value: '9', label: 'September' },
    { value: '10', label: 'Oktober' },
    { value: '11', label: 'November' },
    { value: '12', label: 'Desember' }
];

watch(form, debounce(function (value) {
    router.get(route('reports.index'), value, {
        preserveState: true,
        replace: true
    });
}, 300), { deep: true });

const exportExcel = () => {
    window.location.href = route('reports.export', form.value);
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric'
    }).format(date);
};
</script>

<template>
    <Head title="Laporan" />

    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto w-full">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mt-2 mb-8 gap-4">
                <div>
                    <h1 class="font-display text-3xl font-bold text-navy">Laporan Arsip</h1>
                    <p class="font-sans text-slate-secondary mt-1">Filter dan ekspor data arsip surat.</p>
                </div>
            </div>

            <!-- Filters Card -->
            <div class="bg-surface rounded-2xl shadow-DEFAULT p-6 border border-slate-100 mb-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Tahun</label>
                        <select
                            v-model="form.year"
                            class="w-full border border-slate-200 rounded-lg p-2.5 text-sm focus:border-sage focus:ring-sage bg-white"
                        >
                            <option value="all">Semua Tahun</option>
                            <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
                        </select>
                    </div>
                    
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Bulan</label>
                        <select
                            v-model="form.month"
                            class="w-full border border-slate-200 rounded-lg p-2.5 text-sm focus:border-sage focus:ring-sage bg-white"
                        >
                            <option value="all">Semua Bulan</option>
                            <option v-for="m in months" :key="m.value" :value="m.value">{{ m.label }}</option>
                        </select>
                    </div>
                    
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Kategori</label>
                        <select
                            v-model="form.type"
                            class="w-full border border-slate-200 rounded-lg p-2.5 text-sm focus:border-sage focus:ring-sage bg-white"
                        >
                            <option value="all">Semua Kategori</option>
                            <option value="incoming">Surat Masuk</option>
                            <option value="outgoing">Surat Keluar</option>
                        </select>
                    </div>

                    <div class="flex justify-end">
                        <button
                            @click="exportExcel"
                            class="w-full inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-sage text-white font-bold rounded-lg hover:bg-sage/90 focus:ring-4 focus:ring-sage/20 transition-colors text-sm shadow-sm"
                        >
                            <ArrowDownTrayIcon class="w-4 h-4" />
                            Export Excel
                        </button>
                    </div>
                </div>
            </div>

            <!-- Data Table Card -->
            <div class="bg-surface rounded-2xl shadow-DEFAULT border border-slate-100 overflow-hidden mb-8">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/50 border-b border-slate-200/60 text-sm">
                                    <th class="py-4 px-6 font-semibold text-navy w-16 text-center">No</th>
                                    <th class="py-4 px-6 font-semibold text-navy">Nomor / Referensi</th>
                                    <th class="py-4 px-6 font-semibold text-navy">Kategori</th>
                                    <th class="py-4 px-6 font-semibold text-navy">Pengirim/Penerima</th>
                                    <th class="py-4 px-6 font-semibold text-navy">Perihal</th>
                                    <th class="py-4 px-6 font-semibold text-navy">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-slate-100">
                                <tr 
                                    v-for="(doc, index) in documents.data" 
                                    :key="doc.id"
                                    class="hover:bg-slate-50/50 transition-colors"
                                >
                                    <td class="py-4 px-6 text-slate-500 text-center">
                                        {{ (documents.current_page - 1) * documents.per_page + index + 1 }}
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="font-medium text-navy">{{ doc.document_number }}</div>
                                        <div class="text-xs text-slate-500 mt-1">{{ doc.reference_number || '-' }}</div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span 
                                            class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium border"
                                            :class="doc.type === 'incoming' ? 'bg-sage/10 text-sage border-sage/20' : 'bg-amber-50 text-amber-600 border-amber-200/60'"
                                        >
                                            {{ doc.type === 'incoming' ? 'Surat Masuk' : 'Surat Keluar' }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-slate-600">{{ doc.sender_or_receiver }}</td>
                                    <td class="py-4 px-6 text-slate-600">
                                        <div class="line-clamp-2">{{ doc.subject }}</div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="text-slate-600">{{ formatDate(doc.document_date) }}</div>
                                    </td>
                                </tr>
                                <tr v-if="documents.data.length === 0">
                                    <td colspan="6" class="py-12 px-6 text-center">
                                        <div class="flex flex-col items-center justify-center space-y-3">
                                            <div class="p-3 bg-slate-50 rounded-full">
                                                <DocumentChartBarIcon class="w-8 h-8 text-slate-400" />
                                            </div>
                                            <div class="text-slate-500 font-medium">Tidak ada data laporan ditemukan</div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4 p-6 border-t border-slate-100 bg-slate-50/30" v-if="documents.data.length > 0">
                        <div class="text-sm text-slate-500 text-center sm:text-left w-full sm:w-auto">
                            Showing <span class="font-medium text-navy">{{ documents.from }}</span> to <span class="font-medium text-navy">{{ documents.to }}</span> of <span class="font-medium text-navy">{{ documents.total }}</span> results
                        </div>
                        <div class="flex flex-wrap justify-center gap-1">
                            <template v-for="(link, i) in documents.links" :key="i">
                                <Link 
                                    v-if="link.url"
                                    :href="link.url" 
                                    class="px-3 py-1 text-sm border rounded"
                                    :class="link.active ? 'bg-sage border-sage text-white' : 'border-slate-200 text-slate-600 hover:bg-slate-50'"
                                    v-html="link.label"
                                />
                                <span v-else class="px-3 py-1 text-sm border border-slate-200 text-slate-400 rounded cursor-not-allowed" v-html="link.label"></span>
                            </template>
                        </div>
                    </div>
                </div>
        </div>
    </AuthenticatedLayout>
</template>
