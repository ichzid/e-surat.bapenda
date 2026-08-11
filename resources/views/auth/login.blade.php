<x-layouts.guest>
    <x-slot:title>Masuk - E-Surat Bapenda</x-slot:title>

    <div class="mb-8">
        <h2 class="font-display text-2xl font-bold text-navy">Masuk ke Akun</h2>
        <p class="text-sm text-slate-secondary mt-1">Silakan masukkan kredensial Anda.</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <div>
            <label for="username" class="block text-sm font-medium text-navy mb-2">Username</label>
            <input
                id="username"
                name="username"
                type="text"
                value="{{ old('username') }}"
                placeholder="Username"
                autocomplete="username"
                required
                autofocus
                class="block w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-navy placeholder-slate-400 shadow-sm transition-all duration-200 focus:border-sage focus:ring-2 focus:ring-sage/20 focus:outline-none @error('username') border-status-error ring-2 ring-status-error/20 @enderror"
            />
            @error('username')
                <p class="mt-2 text-xs font-medium text-status-error">{{ $message }}</p>
            @enderror
        </div>

        <div x-data="{ showPassword: false }">
            <label for="password" class="block text-sm font-medium text-navy mb-2">Kata Sandi</label>
            <div class="relative">
                <input
                    id="password"
                    name="password"
                    x-bind:type="showPassword ? 'text' : 'password'"
                    placeholder="Kata Sandi"
                    autocomplete="current-password"
                    required
                    class="block w-full rounded-xl border border-slate-200 bg-white px-4 py-3 pr-12 text-sm text-navy placeholder-slate-400 shadow-sm transition-all duration-200 focus:border-sage focus:ring-2 focus:ring-sage/20 focus:outline-none @error('password') border-status-error ring-2 ring-status-error/20 @enderror"
                />
                <button
                    type="button"
                    x-on:click="showPassword = !showPassword"
                    x-bind:aria-label="showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
                    class="absolute inset-y-0 right-0 flex items-center px-4 text-slate-400 transition-colors hover:text-sage focus:outline-none"
                >
                    <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .638C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                    <svg x-show="showPassword" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 2.036 11.68a1.017 1.017 0 0 0 0 .639C3.423 16.49 7.36 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .638a10.48 10.48 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.792 8.212L21 21m-3.33-2.91-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                    </svg>
                </button>
            </div>
            @error('password')
                <p class="mt-2 text-xs font-medium text-status-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center">
            <input
                id="remember_me"
                name="remember"
                type="checkbox"
                class="h-4 w-4 rounded border-slate-300 text-sage focus:ring-sage/20 transition-colors"
            />
            <label for="remember_me" class="ml-2 block text-sm text-slate-secondary select-none cursor-pointer">
                Ingat saya
            </label>
        </div>

        <button
            type="submit"
            class="flex w-full items-center justify-center gap-2 rounded-xl bg-navy px-4 py-3 text-sm font-semibold text-white shadow-md transition-all duration-200 hover:bg-navy-dark focus:outline-none focus:ring-2 focus:ring-sage focus:ring-offset-2"
        >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9"/>
            </svg>
            Masuk ke Aplikasi
        </button>
    </form>
</x-layouts.guest>
