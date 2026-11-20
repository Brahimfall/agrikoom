<x-guest-layout>
    <div class="mb-6">
        <p class="text-xs font-bold uppercase tracking-wide text-[#7D5A50]">Nouveau mot de passe</p>
        <h1 class="mt-2 text-3xl font-extrabold leading-tight text-slate-950">Securisez votre espace</h1>
        <p class="mt-2 text-sm leading-6 text-slate-600">Choisissez un mot de passe fiable pour retrouver la gestion de vos annonces vendeur.</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <x-input-label for="email" value="Email du compte vendeur" />
            <x-text-input id="email" class="mt-2 block min-h-12 w-full px-4 text-base" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="Nouveau mot de passe" />
            <x-text-input id="password" class="mt-2 block min-h-12 w-full px-4 text-base" type="password" name="password" required autocomplete="new-password" placeholder="Minimum 8 caracteres" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" value="Confirmer le mot de passe" />

            <x-text-input id="password_confirmation" class="mt-2 block min-h-12 w-full px-4 text-base"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password" placeholder="Repetez le mot de passe" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <button type="submit" class="flex min-h-12 w-full items-center justify-center rounded-md bg-[#7D5A50] px-4 text-sm font-extrabold text-white shadow-sm hover:bg-[#6b4d44] focus:outline-none focus:ring-2 focus:ring-[#7D5A50] focus:ring-offset-2">
            Enregistrer le nouveau mot de passe
        </button>
    </form>
</x-guest-layout>
