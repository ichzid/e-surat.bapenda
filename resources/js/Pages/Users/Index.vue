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
    XMarkIcon
} from '@heroicons/vue/24/outline';

const page = usePage();
const props = defineProps({
    users: Object,
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

const form = useForm({
    id: null,
    name: '',
    username: '',
    password: '',
    role: 'user',
    is_active: true,
});

// Pencarian
const search = ref(props.filters.search || '');

// Pencarian realtime dengan debounce (menunggu 300ms setelah user berhenti mengetik)
watch(search, debounce(function (value) {
    router.get(route('users.index'), { search: value }, { 
        preserveState: true, 
        replace: true 
    });
}, 300));

// Fungsi Buka Modal Tambah
const openAddModal = () => {
    isEditing.value = false;
    form.reset();
    form.clearErrors();
    isModalOpen.value = true;
};

// Fungsi Buka Modal Edit
const openEditModal = (user) => {
    isEditing.value = true;
    form.clearErrors();
    form.id = user.id;
    form.name = user.name;
    form.username = user.username;
    form.password = ''; // Kosongkan password saat edit
    form.role = user.role;
    form.is_active = Boolean(user.is_active);
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
};

// Submit Data
const submit = () => {
    if (isEditing.value) {
        form.put(route('users.update', form.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('users.store'), {
            onSuccess: () => closeModal(),
        });
    }
};

// Hapus Data
const deleteUser = (user) => {
    if (confirm(`Apakah Anda yakin ingin menghapus akun ${user.name}?`)) {
        router.delete(route('users.destroy', user.id));
    }
};
</script>

<template>
    <Head title="Master Data User" />

    <AuthenticatedLayout>
        <!-- Kontainer pembungkus utama agar tidak mentok ke kiri seperti sidebar -->
        <div class="max-w-7xl mx-auto w-full">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mt-2 mb-8 gap-4">
                <div>
                    <h1 class="font-display text-3xl font-bold text-navy">Master Data User</h1>
                    <p class="font-sans text-slate-secondary mt-1">Kelola data pegawai, role, dan hak akses sistem.</p>
                </div>
                <div class="flex items-center gap-4">
                    <button @click="openAddModal" class="flex items-center gap-2 bg-sage hover:bg-sage/90 text-white font-bold py-2.5 px-4 rounded-lg transition-colors shadow-sm text-sm">
                        <PlusIcon class="h-4 w-4" />
                        Tambah Pengguna
                    </button>
                </div>
            </div>

            <div class="bg-surface rounded-2xl shadow-DEFAULT p-6 border border-slate-100 mb-8">
                <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                    <div class="relative w-full sm:w-80">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <MagnifyingGlassIcon class="h-5 w-5 text-slate-400" />
                        </div>
                        <input 
                            v-model="search"
                            type="text" 
                            class="block w-full pl-10 pr-4 py-2 border border-slate-200 rounded-lg text-sm placeholder-slate-400 focus:border-sage focus:ring-sage" 
                            placeholder="Cari nama atau username..."
                        >
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50">
                            <th class="py-3 px-4 text-xs font-bold text-navy uppercase tracking-wider">Pengguna</th>
                            <th class="py-3 px-4 text-xs font-bold text-navy uppercase tracking-wider">Username</th>
                            <th class="py-3 px-4 text-xs font-bold text-navy uppercase tracking-wider">Role</th>
                            <th class="py-3 px-4 text-xs font-bold text-navy uppercase tracking-wider">Status</th>
                            <th class="py-3 px-4 text-xs font-bold text-navy uppercase tracking-wider text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-if="users.data.length === 0">
                            <td colspan="5" class="py-8 text-center text-sm text-slate-500">Tidak ada data pengguna yang ditemukan.</td>
                        </tr>
                        <tr v-for="user in users.data" :key="user.id" class="hover:bg-slate-50 transition-colors">
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-3">
                                    <img :src="'https://ui-avatars.com/api/?name='+user.name+'&background=0F172A&color=fff'" class="w-8 h-8 rounded-full" />
                                    <span class="font-sans text-sm font-bold text-navy">{{ user.name }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-sm text-slate-secondary">{{ user.username }}</td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium uppercase" 
                                      :class="user.role === 'admin' ? 'bg-status-error/10 text-status-error' : 'bg-slate-100 text-slate-600'">
                                    {{ user.role }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold tracking-wider" 
                                      :class="user.is_active ? 'bg-status-success/10 text-status-success' : 'bg-slate-100 text-slate-500'">
                                    <span class="w-1.5 h-1.5 rounded-full" :class="user.is_active ? 'bg-status-success' : 'bg-slate-400'"></span>
                                    {{ user.is_active ? 'AKTIF' : 'NONAKTIF' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <button @click="openEditModal(user)" class="p-1 text-slate-400 hover:text-navy transition-colors">
                                        <PencilSquareIcon class="h-5 w-5" />
                                    </button>
                                    <button @click="deleteUser(user)" class="p-1 text-slate-400 hover:text-status-error transition-colors">
                                        <TrashIcon class="h-5 w-5" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4" v-if="users.links.length > 3">
                <div class="text-sm text-slate-500 text-center sm:text-left">
                    Showing <span class="font-medium text-navy">{{ users.from }}</span> to <span class="font-medium text-navy">{{ users.to }}</span> of <span class="font-medium text-navy">{{ users.total }}</span> results
                </div>
                <div class="flex flex-wrap justify-center gap-1">
                    <template v-for="(link, i) in users.links" :key="i">
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

    <!-- Modal Dialog -->
    <div v-if="isModalOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-navy/60 transition-opacity" aria-hidden="true" @click="closeModal"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-slate-100">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-navy" id="modal-title">
                            {{ isEditing ? 'Edit Pengguna' : 'Tambah Pengguna Baru' }}
                        </h3>
                        <button @click="closeModal" class="text-slate-400 hover:text-navy">
                            <XMarkIcon class="h-6 w-6" />
                        </button>
                    </div>

                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
                            <input v-model="form.name" type="text" class="w-full border border-slate-200 rounded-lg p-2.5 text-sm focus:border-sage focus:ring-sage" required>
                            <p v-if="form.errors.name" class="mt-1 text-xs text-status-error">{{ form.errors.name }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Username</label>
                            <input v-model="form.username" type="text" class="w-full border border-slate-200 rounded-lg p-2.5 text-sm focus:border-sage focus:ring-sage" required>
                            <p v-if="form.errors.username" class="mt-1 text-xs text-status-error">{{ form.errors.username }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Password <span v-if="isEditing" class="text-xs font-normal text-slate-400">(Kosongkan jika tidak ingin mengubah)</span></label>
                            <input v-model="form.password" type="password" class="w-full border border-slate-200 rounded-lg p-2.5 text-sm focus:border-sage focus:ring-sage" :required="!isEditing">
                            <p v-if="form.errors.password" class="mt-1 text-xs text-status-error">{{ form.errors.password }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-1">Role</label>
                                <select v-model="form.role" class="w-full border border-slate-200 rounded-lg p-2.5 text-sm focus:border-sage focus:ring-sage">
                                    <option value="user">Pegawai / User</option>
                                    <option value="admin">Administrator</option>
                                </select>
                                <p v-if="form.errors.role" class="mt-1 text-xs text-status-error">{{ form.errors.role }}</p>
                            </div>
                        </div>

                        <div class="flex items-center mt-4">
                            <input v-model="form.is_active" id="is_active" type="checkbox" class="h-4 w-4 text-sage focus:ring-sage border-gray-300 rounded">
                            <label for="is_active" class="ml-2 block text-sm text-slate-700">Akun Aktif (Dapat Login)</label>
                        </div>
                    </form>
                </div>
                <div class="bg-slate-50 px-4 py-3 sm:px-6 flex flex-row-reverse gap-3">
                    <button @click="submit" :disabled="form.processing" type="button" class="w-full inline-flex justify-center rounded-lg border border-transparent px-4 py-2 bg-sage text-base font-medium text-white shadow-sm hover:bg-sage/90 focus:outline-none sm:w-auto sm:text-sm">
                        {{ isEditing ? 'Simpan Perubahan' : 'Tambahkan Akun' }}
                    </button>
                    <button @click="closeModal" type="button" class="w-full inline-flex justify-center rounded-lg border border-slate-300 px-4 py-2 bg-white text-base font-medium text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>