<x-guest-layout>
    <div class="mb-6">
        <p class="text-xs font-bold uppercase tracking-wide text-[#7D5A50]">Verification securite</p>
        <h1 class="mt-2 text-3xl font-extrabold leading-tight text-slate-950">Confirmez votre mot de passe</h1>
        <p class="mt-2 text-sm leading-6 text-slate-600">Cette etape protege les actions sensibles de votre espace vendeur.</p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="password" value="Mot de passe" />
            <x-text-input id="password" class="mt-2 block min-h-12 w-full px-4 text-base" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <button type="submit" class="flex min-h-12 w-full items-center justify-center rounded-md bg-[#7D5A50] px-4 text-sm font-extrabold text-white shadow-sm hover:bg-[#6b4d44] focus:outline-none focus:ring-2 focus:ring-[#7D5A50] focus:ring-offset-2">
            Confirmer
        </button>
    </form>
</x-guest-layout>
