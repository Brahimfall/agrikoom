<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if (session('warning'))
        <div class="mb-4 rounded-xl bg-amber-50 p-3.5 text-xs text-amber-900 border border-amber-200">
            {{ session('warning') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 rounded-xl bg-rose-50 p-3.5 text-xs text-rose-900 border border-rose-200">
            {{ session('error') }}
        </div>
    @endif

    <div class="mb-6">
        <p class="text-xs font-bold uppercase tracking-wider text-[#7D5A50]">Espace Vendeurs &amp; Producteurs</p>
        <h1 class="mt-1 text-2xl sm:text-3xl font-extrabold leading-tight text-slate-950">Connexion à votre espace</h1>
        <p class="mt-2 text-sm leading-6 text-slate-600">Connectez-vous pour gérer vos annonces, vos cotations et vos contacts clients.</p>
    </div>

    <div class="space-y-4" x-data="{ mode: 'otp' }">
        {{-- Option 1 : Connexion directe avec Google --}}
        <div>
            <a href="{{ route('auth.google') }}"
               class="flex min-h-12 w-full items-center justify-center gap-3 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 shadow-2xs hover:bg-slate-50 hover:border-slate-400 transition active:scale-98">
                <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.665-5.17 3.665-9.17z"/>
                    <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.27 21.39 7.35 24 12 24z"/>
                    <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 9.99 0 12s.45 3.82 1.25 5.42l4.03-3.15z"/>
                    <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.35 0 3.27 2.61 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
                </svg>
                <span>Continuer avec Google</span>
            </a>
        </div>

        {{-- Séparateur --}}
        <div class="relative flex items-center justify-center py-2">
            <div class="border-t border-slate-200 w-full"></div>
            <span class="bg-white px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider shrink-0">ou par e-mail</span>
            <div class="border-t border-slate-200 w-full"></div>
        </div>

        {{-- Mode OTP : Connexion instantanée par code E-mail (Recommandé) --}}
        <div x-show="mode === 'otp'">
            <form method="POST" action="{{ route('otp.send') }}" class="space-y-4">
                @csrf

                <div>
                    <x-input-label for="login-email" value="Votre adresse e-mail" class="font-bold text-slate-800 text-xs uppercase" />
                    <div class="relative mt-1.5">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </span>
                        <x-text-input id="login-email"
                                      class="block w-full rounded-xl pl-9 pr-3 text-sm"
                                      type="email"
                                      name="email"
                                      :value="old('email')"
                                      required
                                      autofocus
                                      autocomplete="email"
                                      placeholder="exemple@domaine.com" />
                    </div>
                    <p class="mt-1 text-[11px] text-slate-500">Un code de vérification à 6 chiffres vous sera envoyé.</p>
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                <button type="submit"
                        class="flex min-h-12 w-full items-center justify-center gap-2 rounded-xl bg-[#7D5A50] px-4 text-sm font-extrabold text-white shadow-md hover:bg-[#6b4d44] transition active:scale-98">
                    <span>Recevoir mon code de connexion</span>
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </form>

            <div class="mt-4 text-center">
                <button type="button" @click="mode = 'password'" class="text-xs text-slate-500 hover:text-[#7D5A50] underline">
                    Se connecter avec numéro de téléphone &amp; mot de passe
                </button>
            </div>
        </div>

        {{-- Mode classique : Téléphone + Mot de passe (Alternative) --}}
        <div x-show="mode === 'password'" x-cloak>
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <x-input-label for="phone" value="Numéro de téléphone" class="font-bold text-slate-800 text-xs uppercase" />
                    <x-text-input id="phone" class="mt-1.5 block w-full rounded-xl px-3 text-sm" type="tel" name="phone" :value="old('phone')" autocomplete="username" placeholder="+221770000000" />
                    <x-input-error :messages="$errors->get('phone')" class="mt-1" />
                </div>

                <div>
                    <div class="flex items-center justify-between">
                        <x-input-label for="password" value="Mot de passe" class="font-bold text-slate-800 text-xs uppercase" />
                        @if (Route::has('password.request'))
                            <a class="text-xs text-[#7D5A50] hover:underline" href="{{ route('password.request') }}">
                                Oublié ?
                            </a>
                        @endif
                    </div>
                    <x-text-input id="password" class="mt-1.5 block w-full rounded-xl px-3 text-sm" type="password" name="password" autocomplete="current-password" placeholder="Votre mot de passe" />
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>

                <div class="flex items-center justify-between">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-[#7D5A50] shadow-sm focus:ring-[#7D5A50]" name="remember">
                        <span class="ms-2 text-xs font-medium text-slate-600">Rester connecté</span>
                    </label>
                </div>

                <button type="submit" class="flex min-h-12 w-full items-center justify-center rounded-xl bg-[#7D5A50] px-4 text-sm font-extrabold text-white shadow-md hover:bg-[#6b4d44] transition">
                    Entrer dans mon espace
                </button>
            </form>

            <div class="mt-4 text-center">
                <button type="button" @click="mode = 'otp'" class="text-xs text-[#7D5A50] hover:underline font-semibold">
                    &larr; Revenir à la connexion rapide par code e-mail
                </button>
            </div>
        </div>

        {{-- Inscription --}}
        <div class="flex items-center justify-center gap-2 border-t border-slate-100 pt-4 text-sm">
            <span class="text-slate-500">Nouveau sur AgriKoom ?</span>
            <a class="font-bold text-[#7D5A50] hover:text-[#5a3f38]" href="{{ route('register') }}">Créer un compte</a>
        </div>
    </div>
</x-guest-layout>
