<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';
import { UserCircleIcon } from '@heroicons/vue/24/outline';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Profil Akun" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <div class="p-2 bg-navy/5 rounded-lg border border-navy/10">
                    <UserCircleIcon class="w-6 h-6 text-navy" />
                </div>
                <div>
                    <h2 class="text-xl font-bold text-navy leading-tight">Profil Akun</h2>
                    <p class="text-sm text-slate-500 mt-0.5">Kelola informasi profil dan keamanan akun Anda</p>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto w-full space-y-8">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6 sm:p-8">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-xl"
                    />
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6 sm:p-8">
                    <UpdatePasswordForm class="max-w-xl" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
