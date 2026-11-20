<x-guest-layout>
    {{-- Status Alerts --}}
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if (session('warning'))
        <div class="mb-4 rounded-lg bg-amber-50 p-3 text-xs font-semibold text-amber-800 border border-amber-200">
            {{ session('warning') }}
        </div>
    @endif

    @if (session('demo_code'))
        <div class="mb-4 rounded-xl bg-emerald-50 p-3.5 text-xs text-emerald-900 border border-emerald-200 shadow-2xs">
            <div class="flex items-center gap-2 font-bold text-emerald-800">
                <svg class="h-4 w-4 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Code de vérification (Mode Démo / Local) :</span>
            </div>
            <p class="mt-1 font-mono text-base font-extrabold tracking-widest text-emerald-700 bg-white py-1 px-3 rounded inline-block border border-emerald-300">
                {{ session('demo_code') }}
            </p>
        </div>
    @endif

    <div class="mb-6 text-center sm:text-left">
        <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-[#7D5A50]/10 text-[#7D5A50] mb-3">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        </div>
        <p class="text-xs font-bold uppercase tracking-wider text-[#7D5A50]">Étape 1 sur 2 &bull; Vérification</p>
        <h1 class="mt-1 text-2xl sm:text-3xl font-extrabold text-slate-950">Vérifiez votre e-mail</h1>
        <p class="mt-2 text-sm text-slate-600">
            Un code à 6 chiffres vient d'être envoyé à :<br>
            <strong class="text-slate-900 font-semibold">{{ $email }}</strong>
            <a href="{{ route('register') }}" class="ml-1 text-xs font-bold text-[#7D5A50] hover:underline">(Changer)</a>
        </p>
    </div>

    <form method="POST" action="{{ route('otp.verify.submit') }}" class="space-y-5" x-data="{
        code: '',
        fillDigit(e) {
            this.code = e.target.value.replace(/[^0-9]/g, '').slice(0, 6);
        }
    }">
        @csrf

        <div>
            <label for="code" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2 text-center sm:text-left">
                Saisissez votre code à 6 chiffres
            </label>
            <div class="relative">
                <input id="code"
                       name="code"
                       type="text"
                       inputmode="numeric"
                       pattern="[0-9]*"
                       maxlength="6"
                       required
                       autofocus
                       autocomplete="one-time-code"
                       placeholder="• • • • • •"
                       class="block w-full text-center font-mono text-2xl sm:text-3xl tracking-[0.35em] font-black rounded-xl border border-slate-300 bg-slate-50 py-3 shadow-xs focus:bg-white focus:border-[#7D5A50] focus:ring-2 focus:ring-[#7D5A50]/20 transition">
            </div>
            <x-input-error :messages="$errors->get('code')" class="mt-2 text-center" />
        </div>

        <button type="submit"
                class="flex min-h-12 w-full items-center justify-center gap-2 rounded-xl bg-[#7D5A50] px-4 text-sm font-extrabold text-white shadow-md hover:bg-[#6b4d44] transition active:scale-98">
            <span>Valider et continuer</span>
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
        </button>
    </form>

    {{-- Renvoyer le code --}}
    <div class="mt-6 border-t border-slate-100 pt-4 text-center text-xs text-slate-600"
         x-data="{ countdown: 60, canResend: false }"
         x-init="let timer = setInterval(() => { if (countdown > 0) { countdown--; } else { canResend = true; clearInterval(timer); } }, 1000)">
        
        <p class="mb-2">Vous n'avez pas reçu le code ? Vérifiez vos courriers indésirables (Spams).</p>
        
        <form method="POST" action="{{ route('otp.resend') }}">
            @csrf
            <template x-if="!canResend">
                <span class="inline-block text-slate-400 font-medium">
                    Renvoyer un nouveau code dans (<span x-text="countdown"></span>s)
                </span>
            </template>
            <template x-if="canResend">
                <button type="submit" class="font-bold text-[#7D5A50] hover:underline focus:outline-none">
                    Renvoyer un nouveau code maintenant
                </button>
            </template>
        </form>
    </div>
</x-guest-layout>

