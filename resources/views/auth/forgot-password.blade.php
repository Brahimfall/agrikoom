<x-guest-layout>
    <div class="mb-6">
        <p class="text-xs font-bold uppercase tracking-wide text-[#7D5A50]">Assistance vendeur</p>
        <h1 class="mt-2 text-3xl font-extrabold leading-tight text-slate-950">Recuperer l'acces</h1>
        <p class="mt-2 text-sm leading-6 text-slate-600">Si un email est rattache a votre compte vendeur, nous vous envoyons un lien pour choisir un nouveau mot de passe.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" value="Email du compte vendeur" />
            <x-text-input id="email" class="mt-2 block min-h-12 w-full px-4 text-base" type="email" name="email" :value="old('email')" required autofocus autocomplete="email" placeholder="vendeur@exemple.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="rounded-md border border-[#7D5A50]/20 bg-[#7D5A50]/5 px-4 py-3 text-sm text-[#5a3f38]">
            Les nouveaux vendeurs se connectent surtout par telephone. Si aucun email n'est associe a votre compte, contactez l'equipe AgriKoom.
        </div>

        <button type="submit" class="flex min-h-12 w-full items-center justify-center rounded-md bg-[#7D5A50] px-4 text-sm font-extrabold text-white shadow-sm hover:bg-[#6b4d44] focus:outline-none focus:ring-2 focus:ring-[#7D5A50] focus:ring-offset-2">
            Envoyer le lien de recuperation
        </button>

        <div class="flex items-center justify-center gap-2 border-t border-slate-100 pt-4 text-sm">
            <span class="text-slate-500">Vous vous souvenez du mot de passe ?</span>
            <a class="font-bold text-[#7D5A50] hover:text-[#5a3f38]" href="{{ route('login') }}">Se connecter</a>
        </div>
    </form>
</x-guest-layout>
