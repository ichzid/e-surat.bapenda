<x-layouts.guest>
    <x-slot:title>Login</x-slot:title>

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

        <div>
            <label for="password" class="block text-sm font-medium text-navy mb-2">Kata Sandi</label>
            <input
                id="password"
                name="password"
                type="password"
                placeholder="Kata Sandi"
                autocomplete="current-password"
                required
                class="block w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-navy placeholder-slate-400 shadow-sm transition-all duration-200 focus:border-sage focus:ring-2 focus:ring-sage/20 focus:outline-none @error('password') border-status-error ring-2 ring-status-error/20 @enderror"
            />
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
