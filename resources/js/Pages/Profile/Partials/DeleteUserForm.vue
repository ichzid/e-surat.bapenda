<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    nextTick(() => passwordInput.value.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-lg font-bold text-status-error">
                Hapus Akun
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Setelah akun Anda dihapus, semua sumber daya dan data akan dihapus secara permanen. Pastikan Anda telah mengamankan data yang penting sebelum menghapus akun ini.
            </p>
        </header>

        <button 
            @click="confirmUserDeletion"
            class="inline-flex items-center justify-center px-6 py-2.5 bg-status-error/10 text-status-error border border-status-error/20 rounded-xl font-bold text-sm hover:bg-status-error hover:text-white focus:outline-none focus:ring-4 focus:ring-status-error/20 transition-all shadow-sm"
        >
            Hapus Akun Permanen
        </button>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-bold text-navy">
                    Apakah Anda yakin ingin menghapus akun?
                </h2>

                <p class="mt-2 text-sm text-slate-500">
                    Setelah akun Anda dihapus, semua data akan hilang secara permanen. Masukkan password Anda untuk mengonfirmasi bahwa Anda ingin menghapus akun Anda secara permanen.
                </p>

                <div class="mt-6">
                    <label for="password" class="sr-only">Password</label>
                    <input
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-navy placeholder-slate-400 focus:border-status-error focus:ring focus:ring-status-error/20 transition-all shadow-sm"
                        placeholder="Masukkan password Anda"
                        @keyup.enter="deleteUser"
                    />
                    <InputError :message="form.errors.password" class="mt-1.5" />
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button 
                        @click="closeModal"
                        class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-50 focus:ring-4 focus:ring-slate-100 transition-all shadow-sm"
                    >
                        Batal
                    </button>

                    <button
                        class="inline-flex items-center justify-center px-4 py-2 bg-status-error border border-transparent rounded-xl font-bold text-sm text-white hover:bg-status-error/90 focus:outline-none focus:ring-4 focus:ring-status-error/20 transition-all shadow-sm disabled:opacity-50 disabled:cursor-not-allowed"
                        :disabled="form.processing"
                        @click="deleteUser"
                    >
                        Hapus Akun
                    </button>
                </div>
            </div>
        </Modal>
    </section>
</template>
