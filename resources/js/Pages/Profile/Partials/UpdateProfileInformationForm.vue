<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-bold text-navy">
                Informasi Profil
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Perbarui informasi nama pengguna dan alamat email akun Anda.
            </p>
        </header>

        <form
            @submit.prevent="form.patch(route('profile.update'))"
            class="mt-6 space-y-6"
        >
            <div>
                <label for="name" class="block text-sm font-semibold text-navy mb-1.5">Nama Lengkap</label>
                <input
                    id="name"
                    type="text"
                    class="block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-navy placeholder-slate-400 focus:border-sage focus:ring focus:ring-sage/20 transition-all shadow-sm"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />
                <InputError class="mt-1.5" :message="form.errors.name" />
            </div>

            <div>
                <label for="email" class="block text-sm font-semibold text-navy mb-1.5">Alamat Email</label>
                <input
                    id="email"
                    type="email"
                    class="block w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-500 placeholder-slate-400 focus:border-sage focus:ring focus:ring-sage/20 transition-all shadow-sm cursor-not-allowed"
                    v-model="form.email"
                    required
                    autocomplete="username"
                    disabled
                />
                <p class="mt-1 text-xs text-slate-400">Email tidak dapat diubah. Hubungi admin untuk perubahan.</p>
                <InputError class="mt-1.5" :message="form.errors.email" />
            </div>

            <div class="flex items-center gap-4 pt-2">
                <button
                    type="submit"
                    class="inline-flex items-center justify-center px-6 py-2.5 bg-sage border border-transparent rounded-xl font-bold text-sm text-white hover:bg-sage/90 focus:outline-none focus:ring-4 focus:ring-sage/20 transition-all shadow-sm disabled:opacity-50 disabled:cursor-not-allowed"
                    :disabled="form.processing"
                >
                    Simpan Perubahan
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
