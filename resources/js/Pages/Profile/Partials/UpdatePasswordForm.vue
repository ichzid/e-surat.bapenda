<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-bold text-navy">
                Ubah Password
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Pastikan akun Anda menggunakan password yang panjang dan acak agar tetap aman.
            </p>
        </header>

        <form @submit.prevent="updatePassword" class="mt-6 space-y-6">
            <div>
                <label for="current_password" class="block text-sm font-semibold text-navy mb-1.5">Password Saat Ini</label>
                <input
                    id="current_password"
                    ref="currentPasswordInput"
                    v-model="form.current_password"
                    type="password"
                    class="block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-navy placeholder-slate-400 focus:border-sage focus:ring focus:ring-sage/20 transition-all shadow-sm"
                    autocomplete="current-password"
                />
                <InputError :message="form.errors.current_password" class="mt-1.5" />
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold text-navy mb-1.5">Password Baru</label>
                <input
                    id="password"
                    ref="passwordInput"
                    v-model="form.password"
                    type="password"
                    class="block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-navy placeholder-slate-400 focus:border-sage focus:ring focus:ring-sage/20 transition-all shadow-sm"
                    autocomplete="new-password"
                />
                <InputError :message="form.errors.password" class="mt-1.5" />
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-navy mb-1.5">Konfirmasi Password</label>
                <input
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-navy placeholder-slate-400 focus:border-sage focus:ring focus:ring-sage/20 transition-all shadow-sm"
                    autocomplete="new-password"
                />
                <InputError :message="form.errors.password_confirmation" class="mt-1.5" />
            </div>

            <div class="flex items-center gap-4 pt-2">
                <button
                    type="submit"
                    class="inline-flex items-center justify-center px-6 py-2.5 bg-sage border border-transparent rounded-xl font-bold text-sm text-white hover:bg-sage/90 focus:outline-none focus:ring-4 focus:ring-sage/20 transition-all shadow-sm disabled:opacity-50 disabled:cursor-not-allowed"
                    :disabled="form.processing"
                >
                    Simpan Password
                </button>

                <Transition
                    enter-active-class="transition ease-in-out duration-300"
                    enter-from-class="opacity-0 -translate-y-2"
                    leave-active-class="transition ease-in-out duration-300"
                    leave-to-class="opacity-0 translate-y-2"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-sm font-medium text-status-success bg-status-success/10 px-3 py-1.5 rounded-lg border border-status-success/20"
                    >
                        Tersimpan.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
