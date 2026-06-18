<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    username: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Masuk" />

        <div class="mb-10 text-center md:text-left">
            <h2 class="text-3xl font-display font-bold text-navy tracking-tight">Form Login</h2>
        </div>

        <div v-if="status" class="mb-6 p-4 rounded bg-status-success/10 border border-status-success/20 text-sm font-medium text-status-success">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <label for="username" class="block text-sm font-semibold text-navy mb-2">Username</label>
                <input
                    id="username"
                    type="text"
                    class="block w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-navy placeholder-slate-400 focus:border-sage focus:ring focus:ring-sage/20 transition-all shadow-sm"
                    v-model="form.username"
                    placeholder="Masukan Username"
                    required
                    autofocus
                    autocomplete="username"
                />
                <InputError class="mt-1.5" :message="form.errors.username" />
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold text-navy mb-2">Password</label>
                <input
                    id="password"
                    type="password"
                    class="block w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-navy placeholder-slate-400 focus:border-sage focus:ring focus:ring-sage/20 transition-all shadow-sm"
                    v-model="form.password"
                    placeholder="Masukan Password"
                    required
                    autocomplete="current-password"
                />
                <InputError class="mt-1.5" :message="form.errors.password" />
            </div>

            <div class="flex items-center justify-end mt-2">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-sm text-sage font-semibold hover:text-navy hover:underline transition-colors"
                >
                    Lupa Password ?
                </Link>
            </div>

            <div class="pt-2">
                <button
                    type="submit"
                    class="w-full inline-flex items-center justify-center px-6 py-3.5 bg-sage border border-transparent rounded-xl font-bold text-sm text-white hover:bg-navy focus:outline-none focus:ring-4 focus:ring-navy/20 transition-colors shadow-sm disabled:opacity-50 disabled:cursor-not-allowed"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing">Memproses...</span>
                    <span v-else>Sign In</span>
                </button>
            </div>
        </form>
    </GuestLayout>
</template>
