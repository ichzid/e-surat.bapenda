<x-layouts.app>
    <x-slot:title>Profil Akun</x-slot:title>

    <div>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between mt-2 mb-8 gap-4">
            <div>
                <h1 class="font-display text-2xl sm:text-3xl font-bold text-navy">Profil Akun</h1>
                <p class="font-sans text-sm sm:text-base text-slate-secondary mt-1">Kelola informasi profil dan kata sandi akun Anda.</p>
            </div>
        </div>

        <div class="grid gap-8 lg:grid-cols-2">
            <!-- Update Profile Information -->
            <section class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 lg:p-8">
                <h2 class="font-display text-lg font-bold text-navy mb-6">Informasi Profil</h2>
                <p class="text-sm text-slate-secondary -mt-4 mb-6">Perbarui informasi nama dan username akun Anda.</p>

                <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label for="name" class="block text-sm font-medium text-navy mb-2">Nama Lengkap</label>
                        <input
                            id="name"
                            name="name"
                            type="text"
                            value="{{ old('name', auth()->user()->name) }}"
                            required
                            class="block w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-navy placeholder-slate-400 shadow-sm transition-all duration-200 focus:border-sage focus:ring-2 focus:ring-sage/20 focus:outline-none @error('name') border-status-error ring-2 ring-status-error/20 @enderror"
                        />
                        @error('name')
                            <p class="mt-2 text-xs font-medium text-status-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="username" class="block text-sm font-medium text-navy mb-2">Username</label>
                        <input
                            id="username"
                            name="username"
                            type="text"
                            value="{{ old('username', auth()->user()->username) }}"
                            required
                            class="block w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-navy placeholder-slate-400 shadow-sm transition-all duration-200 focus:border-sage focus:ring-2 focus:ring-sage/20 focus:outline-none @error('username') border-status-error ring-2 ring-status-error/20 @enderror"
                        />
                        @error('username')
                            <p class="mt-2 text-xs font-medium text-status-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-2">
                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 bg-navy hover:bg-navy-dark text-white font-bold py-2.5 px-4 rounded-lg transition-colors shadow-sm text-sm focus:outline-none focus:ring-2 focus:ring-sage focus:ring-offset-2"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
                            </svg>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </section>

            <!-- Update Password -->
            <section class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 lg:p-8">
                <h2 class="font-display text-lg font-bold text-navy mb-6">Kata Sandi</h2>
                <p class="text-sm text-slate-secondary -mt-4 mb-6">Pastikan kata sandi Anda kuat dan aman.</p>

                <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label for="current_password" class="block text-sm font-medium text-navy mb-2">Kata Sandi Saat Ini</label>
                        <input
                            id="current_password"
                            name="current_password"
                            type="password"
                            required
                            autocomplete="current-password"
                            class="block w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-navy placeholder-slate-400 shadow-sm transition-all duration-200 focus:border-sage focus:ring-2 focus:ring-sage/20 focus:outline-none @error('current_password') border-status-error ring-2 ring-status-error/20 @enderror"
                        />
                        @error('current_password')
                            <p class="mt-2 text-xs font-medium text-status-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-navy mb-2">Kata Sandi Baru</label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            required
                            autocomplete="new-password"
                            class="block w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-navy placeholder-slate-400 shadow-sm transition-all duration-200 focus:border-sage focus:ring-2 focus:ring-sage/20 focus:outline-none @error('password') border-status-error ring-2 ring-status-error/20 @enderror"
                        />
                        @error('password')
                            <p class="mt-2 text-xs font-medium text-status-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-navy mb-2">Konfirmasi Kata Sandi Baru</label>
                        <input
                            id="password_confirmation"
                            name="password_confirmation"
                            type="password"
                            required
                            autocomplete="new-password"
                            class="block w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-navy placeholder-slate-400 shadow-sm transition-all duration-200 focus:border-sage focus:ring-2 focus:ring-sage/20 focus:outline-none @error('password_confirmation') border-status-error ring-2 ring-status-error/20 @enderror"
                        />
                        @error('password_confirmation')
                            <p class="mt-2 text-xs font-medium text-status-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-2">
                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 bg-sage hover:bg-sage/90 text-white font-bold py-2.5 px-4 rounded-lg transition-colors shadow-sm text-sm focus:outline-none focus:ring-2 focus:ring-sage focus:ring-offset-2"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/>
                            </svg>
                            Perbarui Kata Sandi
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</x-layouts.app>
