<x-guest-layout>
    <div class="pins-card p-8">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/PINS_logo_.png') }}" alt="PINS" class="h-14 w-auto"/>
            <div>
                <p class="text-xs uppercase tracking-widest text-text-secondary">Portal Login</p>
                <h2 class="text-2xl font-semibold text-text-main">Masuk ke Sistem PKL</h2>
            </div>
        </div>

        <div class="mt-6">
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-4 form-surface">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-text-main">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-text-main">Password</label>
                    <input id="password" name="password" type="password" required autocomplete="current-password" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="flex items-center justify-between">
                    <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-text-secondary">
                        <input id="remember_me" type="checkbox" class="rounded border-black/10 text-primary focus:ring-primary" name="remember">
                        <span>Ingat saya</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="text-sm font-medium text-primary hover:underline" href="{{ route('password.request') }}">Lupa password?</a>
                    @endif
                </div>

                <button type="submit" class="pins-btn-primary w-full py-3">Masuk</button>
            </form>

            <div class="notice-box mt-6 text-sm text-text-secondary">
                Akun peserta didapatkan oleh dan hanya dari pembimbing PKL PT Pins Indonesia
            </div>
        </div>
    </div>
</x-guest-layout>
