<script setup>
import { ref, watch } from 'vue';
import { Head, useForm, router, usePage, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { toast } from 'vue3-toastify';
import debounce from 'lodash/debounce';
import { 
    MagnifyingGlassIcon, 
    PlusIcon, 
    PencilSquareIcon, 
    TrashIcon,
    XMarkIcon,
    DocumentArrowDownIcon,
    EyeIcon
} from '@heroicons/vue/24/outline';

const page = usePage();
const props = defineProps({
    documents: Object,
    filters: Object,
});

// Watcher untuk Global Flash Messages
watch(() => page.props.flash, (flash) => {
    if (flash?.message) {
        toast.success(flash.message);
    }
    if (flash?.error) {
        toast.error(flash.error);
    }
}, { deep: true, immediate: true });

// Modal State
const isModalOpen = ref(false);
const isEditing = ref(false);
const fileInput = ref(null);

const form = useForm({
    id: null,
    document_number: '',
    document_date: '',
    received_date: '',
    sender_or_receiver: '',
    subject: '',
    file_document: null,
});

// Pencarian
const search = ref(props.filters.search || '');

watch(search, debounce(function (value) {
    router.get(route('incoming.index'), { search: value }, { 
        preserveState: true, 
        replace: true 
    });
}, 300));

// Modal Actions
const isDragging = ref(false);

const openAddModal = () => {
    isEditing.value = false;
    form.reset();
    form.clearErrors();
    if (fileInput.value) fileInput.value.value = '';
    isModalOpen.value = true;
};

const openEditModal = (doc) => {
    isEditing.value = true;
    form.clearErrors();
    form.id = doc.id;
    form.document_number = doc.document_number;
    form.document_date = doc.document_date ? doc.document_date.substring(0, 10) : '';
    form.received_date = doc.received_date ? doc.received_date.substring(0, 10) : '';
    form.sender_or_receiver = doc.sender_or_receiver;
    form.subject = doc.subject;
    form.file_document = null;
    if (fileInput.value) fileInput.value.value = '';
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
};

const handleFileUpload = (e) => {
    form.file_document = e.target.files[0];
};

const handleDrop = (e) => {
    isDragging.value = false;
    if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
        form.file_document = e.dataTransfer.files[0];
        if (fileInput.value) {
            fileInput.value.files = e.dataTransfer.files;
        }
    }
};

const submit = () => {
    // Karena form mengirim file, untuk "Update" di Inertia JS menggunakan HTTP POST dengan _method='put'
    if (isEditing.value) {
        form.transform((data) => ({
            ...data,
            _method: 'put',
        })).post(route('incoming.update', form.id), {
            forceFormData: true,
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('incoming.store'), {
            forceFormData: true,
            onSuccess: () => closeModal(),
        });
    }
};

const deleteDocument = (doc) => {
    if (confirm(`Hapus data surat masuk no: ${doc.document_number}?`)) {
        router.delete(route('incoming.destroy', doc.id));
    }
};

// Formatter Tanggal
const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }).format(date);
};
</script>

<template>
    <Head title="Arsip Surat Masuk" />

    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto w-full">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mt-2 mb-8 gap-4">
                <div>
                    <h1 class="font-display text-3xl font-bold text-navy">Surat Masuk</h1>
                    <p class="font-sans text-slate-secondary mt-1">Daftar arsip dokumen dan surat yang diterima instansi.</p>
                </div>
                <div class="flex items-center gap-4">
                    <button @click="openAddModal" class="flex items-center gap-2 bg-sage hover:bg-sage/90 text-white font-bold py-2.5 px-4 rounded-lg transition-colors shadow-sm text-sm">
                        <PlusIcon class="h-4 w-4" />
                        Tambah Surat Masuk
                    </button>
                </div>
            </div>

            <div class="bg-surface rounded-2xl shadow-DEFAULT p-6 border border-slate-100 mb-8">
                <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                    <div class="relative w-full sm:w-96">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <MagnifyingGlassIcon class="h-5 w-5 text-slate-400" />
                        </div>
                        <input 
                            v-model="search"
                            type="text" 
                            class="block w-full pl-10 pr-4 py-2 border border-slate-200 rounded-lg text-sm placeholder-slate-400 focus:border-sage focus:ring-sage" 
                            placeholder="Cari no surat, pengirim, atau perihal..."
                        >
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50">
                                <th class="py-3 px-4 text-xs font-bold text-navy uppercase tracking-wider w-1/4">Info Surat</th>
                                <th class="py-3 px-4 text-xs font-bold text-navy uppercase tracking-wider w-1/4">Pengirim</th>
                                <th class="py-3 px-4 text-xs font-bold text-navy uppercase tracking-wider w-[20%]">Tanggal</th>
                                <th class="py-3 px-4 text-xs font-bold text-navy uppercase tracking-wider text-center w-[15%]">File</th>
                                <th class="py-3 px-4 text-xs font-bold text-navy uppercase tracking-wider text-right w-[15%]">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-if="documents.data.length === 0">
                                <td colspan="5" class="py-8 text-center text-sm text-slate-500">Tidak ada data surat masuk ditemukan.</td>
                            </tr>
                            <tr v-for="doc in documents.data" :key="doc.id" class="hover:bg-slate-50 transition-colors">
                                <td class="py-3 px-4">
                                    <div class="font-sans text-sm font-bold text-navy">{{ doc.document_number }}</div>
                                    <div v-if="doc.reference_number && doc.reference_number !== doc.document_number" class="text-[10px] text-slate-400 font-mono mt-0.5 mb-1">
                                        Ref: {{ doc.reference_number }}
                                    </div>
                                    <div class="text-xs text-slate-500 line-clamp-2 mt-0.5" :title="doc.subject">{{ doc.subject }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="text-sm font-medium text-navy">{{ doc.sender_or_receiver }}</span>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="text-xs">
                                        <span class="text-slate-500 block">Tgl Surat: <span class="font-medium text-navy">{{ formatDate(doc.document_date) }}</span></span>
                                        <span class="text-slate-500 block mt-0.5">Diterima: <span class="font-medium text-navy">{{ formatDate(doc.received_date) }}</span></span>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <a v-if="doc.file_path" :href="`/storage/${doc.file_path}`" target="_blank" class="inline-flex items-center justify-center p-1.5 bg-status-info/10 text-status-info hover:bg-status-info/20 rounded-lg transition-colors" title="Lihat/Download File">
                                        <DocumentArrowDownIcon class="h-5 w-5" />
                                    </a>
                                    <span v-else class="text-xs text-slate-400 italic">No File</span>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button @click="openEditModal(doc)" class="p-1 text-slate-400 hover:text-navy transition-colors" title="Edit">
                                            <PencilSquareIcon class="h-5 w-5" />
                                        </button>
                                        <button @click="deleteDocument(doc)" class="p-1 text-slate-400 hover:text-status-error transition-colors" title="Hapus">
                                            <TrashIcon class="h-5 w-5" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4" v-if="documents.links.length > 3">
                    <div class="text-sm text-slate-500 text-center sm:text-left">
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

    <!-- Modal Form (Create / Edit) -->
    <div v-if="isModalOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-navy/60 transition-opacity" aria-hidden="true" @click="closeModal"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-slate-100">
                    <div class="flex justify-between items-center mb-5">
                        <h3 class="text-lg font-bold text-navy" id="modal-title">
                            {{ isEditing ? 'Edit Surat Masuk' : 'Tambah Surat Masuk' }}
                        </h3>
                        <button @click="closeModal" class="text-slate-400 hover:text-navy">
                            <XMarkIcon class="h-6 w-6" />
                        </button>
                    </div>

                    <form @submit.prevent="submit" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Nomor Surat</label>
                                <input v-model="form.document_number" type="text" class="w-full border border-slate-200 rounded-lg p-2.5 text-sm focus:border-sage focus:ring-sage" placeholder="Cth: 001/BAPENDA/2026" required>
                                <p v-if="form.errors.document_number" class="mt-1 text-xs text-status-error">{{ form.errors.document_number }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Asal/Pengirim Surat</label>
                                <input v-model="form.sender_or_receiver" type="text" class="w-full border border-slate-200 rounded-lg p-2.5 text-sm focus:border-sage focus:ring-sage" placeholder="Cth: Dinas Kesehatan" required>
                                <p v-if="form.errors.sender_or_receiver" class="mt-1 text-xs text-status-error">{{ form.errors.sender_or_receiver }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Surat</label>
                                <input v-model="form.document_date" type="date" class="w-full border border-slate-200 rounded-lg p-2.5 text-sm focus:border-sage focus:ring-sage" required>
                                <p v-if="form.errors.document_date" class="mt-1 text-xs text-status-error">{{ form.errors.document_date }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Diterima</label>
                                <input v-model="form.received_date" type="date" class="w-full border border-slate-200 rounded-lg p-2.5 text-sm focus:border-sage focus:ring-sage" required>
                                <p v-if="form.errors.received_date" class="mt-1 text-xs text-status-error">{{ form.errors.received_date }}</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Perihal / Subject</label>
                            <textarea v-model="form.subject" rows="3" class="w-full border border-slate-200 rounded-lg p-2.5 text-sm focus:border-sage focus:ring-sage" placeholder="Tuliskan perihal surat di sini..." required></textarea>
                            <p v-if="form.errors.subject" class="mt-1 text-xs text-status-error">{{ form.errors.subject }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Upload File Fisik <span v-if="isEditing" class="font-normal text-slate-400 text-xs">(Abaikan jika tidak ingin mengganti file)</span></label>
                            <label 
                                for="file-upload" 
                                class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-dashed rounded-lg transition-colors cursor-pointer group relative"
                                :class="[isDragging ? 'border-sage bg-sage/5' : 'border-slate-300 bg-slate-50 hover:bg-slate-100']"
                                @dragover.prevent="isDragging = true"
                                @dragleave.prevent="isDragging = false"
                                @drop.prevent="handleDrop"
                            >
                                <div class="space-y-1 text-center pointer-events-none">
                                    <svg class="mx-auto h-12 w-12 text-slate-400 group-hover:text-sage transition-colors" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-slate-600 justify-center">
                                        <span class="relative font-medium text-sage group-hover:text-sage/80 focus-within:outline-none">
                                            <span>Klik atau drag & drop file ke sini</span>
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-500">PDF, DOC, DOCX up to 10MB</p>
                                    <p v-if="form.file_document" class="text-xs font-bold text-sage mt-2">File terpilih: {{ form.file_document.name }}</p>
                                </div>
                                <input id="file-upload" ref="fileInput" name="file-upload" type="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" @change="handleFileUpload" accept=".pdf,.doc,.docx">
                            </label>
                            <p v-if="form.errors.file_document" class="mt-1 text-xs text-status-error">{{ form.errors.file_document }}</p>
                        </div>
                    </form>
                </div>
                <div class="bg-slate-50 px-4 py-3 sm:px-6 flex flex-row-reverse gap-3">
                    <button @click="submit" :disabled="form.processing" type="button" class="w-full inline-flex justify-center rounded-lg border border-transparent px-4 py-2 bg-sage text-base font-medium text-white shadow-sm hover:bg-sage/90 focus:outline-none sm:w-auto sm:text-sm">
                        <span v-if="form.processing">Menyimpan...</span>
                        <span v-else>{{ isEditing ? 'Simpan Perubahan' : 'Upload Surat Masuk' }}</span>
                    </button>
                    <button @click="closeModal" type="button" class="w-full inline-flex justify-center rounded-lg border border-slate-300 px-4 py-2 bg-white text-base font-medium text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>