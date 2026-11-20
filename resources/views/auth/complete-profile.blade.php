<x-guest-layout>
    <div class="mb-6">
        <div class="inline-flex items-center gap-1.5 rounded-full bg-[#7D5A50]/10 px-3 py-1 text-xs font-bold text-[#7D5A50] mb-2">
            <span>🌾 Étape 2 sur 2</span>
            <span>&bull;</span>
            <span>Finalisation du profil vendeur</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 tracking-tight">Vos coordonnées de vendeur</h1>
        <p class="mt-2 text-sm leading-6 text-slate-600">
            Pour que les acheteurs puissent vous contacter directement par <strong class="text-slate-800">appel ou WhatsApp</strong> et voir la <strong class="text-slate-800">provenance de vos produits</strong>, complétez ces deux informations indispensables.
        </p>
    </div>

    @if (session('info'))
        <div class="mb-5 rounded-xl bg-blue-50 p-3.5 text-xs text-blue-900 border border-blue-200 flex items-start gap-2.5">
            <svg class="h-4 w-4 shrink-0 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('info') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('profile.complete.store') }}" class="space-y-4">
        @csrf

        {{-- 1. Nom complet / Raison sociale --}}
        <div>
            <x-input-label for="name" value="Nom complet ou Nom de l'exploitation" class="font-bold text-slate-800 text-xs uppercase" />
            <div class="relative mt-1.5">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </span>
                <x-text-input id="name"
                              class="block w-full rounded-xl pl-9 pr-3 text-sm"
                              type="text"
                              name="name"
                              :value="old('name', $user->name)"
                              required
                              placeholder="Ex: Babacar Ndiaye ou Ferme Bio de Niayes" />
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-1" />
        </div>

        {{-- 2. Numéro de téléphone WhatsApp & Appels --}}
        <div>
            <div class="flex items-center justify-between">
                <x-input-label for="phone" value="Numéro WhatsApp & Appels" class="font-bold text-slate-800 text-xs uppercase" />
                <span class="text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">Recommandé WhatsApp</span>
            </div>
            <div class="relative mt-1.5 flex rounded-xl shadow-xs">
                <span class="inline-flex items-center gap-1 rounded-l-xl border border-r-0 border-slate-300 bg-slate-100 px-3 text-xs font-bold text-slate-700 select-none">
                    <span>🇸🇳</span>
                    <span>+221</span>
                </span>
                <input id="phone"
                       name="phone"
                       type="tel"
                       required
                       autocomplete="tel"
                       value="{{ old('phone', $user->phone) }}"
                       placeholder="77 123 45 67"
                       class="block w-full rounded-r-xl border border-slate-300 bg-white py-2.5 px-3 text-sm text-slate-900 shadow-2xs focus:border-[#7D5A50] focus:ring-2 focus:ring-[#7D5A50]/20 focus:outline-none">
            </div>
            <p class="mt-1 text-[11px] text-slate-500">Ce numéro permettra aux acheteurs de vous appeler directement ou de discuter avec vous sur WhatsApp pour conclure la vente.</p>
            <x-input-error :messages="$errors->get('phone')" class="mt-1" />
        </div>

        {{-- 3. Localisation : Région du Sénégal + Précision --}}
        <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-3.5 space-y-3">
            <div>
                <x-input-label for="region" value="Région principale du produit" class="font-bold text-slate-800 text-xs uppercase" />
                <select id="region"
                        name="region"
                        required
                        class="mt-1.5 block w-full rounded-xl border border-slate-300 bg-white py-2.5 px-3 text-sm text-slate-900 shadow-2xs focus:border-[#7D5A50] focus:ring-2 focus:ring-[#7D5A50]/20 focus:outline-none">
                    <option value="">Sélectionnez votre région...</option>
                    @foreach ($regions as $r)
                        <option value="{{ $r }}" @selected(old('region') === $r || str_contains($user->location ?? '', $r))>
                            Région de {{ $r }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('region')" class="mt-1" />
            </div>

            <div>
                <x-input-label for="city_commune" value="Ville, Commune ou Louma / Marché (Facultatif)" class="font-semibold text-slate-700 text-xs" />
                <input id="city_commune"
                       name="city_commune"
                       type="text"
                       value="{{ old('city_commune') }}"
                       placeholder="Ex: Richard-Toll, Touba, Sandaga, Pout..."
                       class="mt-1.5 block w-full rounded-xl border border-slate-300 bg-white py-2 px-3 text-sm text-slate-900 shadow-2xs focus:border-[#7D5A50] focus:ring-2 focus:ring-[#7D5A50]/20 focus:outline-none">
                <x-input-error :messages="$errors->get('city_commune')" class="mt-1" />
            </div>
        </div>

        {{-- 4. Type d'activité --}}
        <div>
            <x-input-label for="actor_type" value="Votre activité principale" class="font-bold text-slate-800 text-xs uppercase" />
            <select id="actor_type"
                    name="actor_type"
                    required
                    class="mt-1.5 block w-full rounded-xl border border-slate-300 bg-white py-2.5 px-3 text-sm text-slate-900 shadow-2xs focus:border-[#7D5A50] focus:ring-2 focus:ring-[#7D5A50]/20 focus:outline-none">
                <option value="producer" @selected(old('actor_type', $user->actor_type) === 'producer')>Producteur / Agriculteur / Maraîcher</option>
                <option value="both" @selected(old('actor_type', $user->actor_type) === 'both')>Éleveur &amp; Producteur</option>
                <option value="buyer" @selected(old('actor_type', $user->actor_type) === 'buyer')>Commerçant / Grossiste / Acheteur</option>
            </select>
        </div>

        {{-- Bouton de soumission --}}
        <div class="pt-2">
            <button type="submit"
                    class="flex min-h-12 w-full items-center justify-center gap-2 rounded-xl bg-[#7D5A50] px-4 text-sm font-extrabold text-white shadow-md hover:bg-[#6b4d44] transition active:scale-98">
                <span>Enregistrer et continuer vers mes produits</span>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </button>
        </div>
    </form>
</x-guest-layout>

