<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <a href="{{ route('admin.mercuriale.index') }}" class="inline-flex items-center text-xs font-bold text-[#7D5A50] hover:text-[#5a3f38] mb-1">
                    <svg class="mr-1 h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                    Retour aux prix du marché
                </a>
                <h1 class="text-2xl font-black text-slate-950">
                    {{ $marketPrice->exists ? 'Modifier le prix : '.$marketPrice->product_name : 'Ajouter un nouveau prix du marché' }}
                </h1>
                <p class="mt-1 text-xs text-slate-500">Ces informations sont directement diffusées sur la page d'accueil d'AgriKoom.</p>
            </div>
            @if ($marketPrice->exists)
                <form method="POST" action="{{ route('admin.mercuriale.destroy', $marketPrice) }}" onsubmit="return confirm('Supprimer ce cours ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="rounded-lg border border-rose-200 bg-rose-50 px-3.5 py-2 text-xs font-bold text-rose-700 hover:bg-rose-100 transition">
                        Supprimer ce cours
                    </button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="py-8 bg-slate-50 min-h-screen">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <form method="POST"
                  action="{{ $marketPrice->exists ? route('admin.mercuriale.update', $marketPrice) : route('admin.mercuriale.store') }}"
                  class="space-y-6">
                @csrf
                @if ($marketPrice->exists)
                    @method('PATCH')
                @endif

                <!-- Card 1: Identité du produit et prix -->
                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-2xs space-y-4">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 border-b border-slate-100 pb-2">
                        1. Produit &amp; Fourchette de prix
                    </h2>

                    <div class="grid gap-4 sm:grid-cols-3">
                        <div class="sm:col-span-2">
                            <label for="product_name" class="block text-xs font-bold text-slate-700 mb-1">Nom du produit / service <span class="text-rose-500">*</span></label>
                            <input id="product_name" name="product_name" type="text" required
                                   value="{{ old('product_name', $marketPrice->product_name) }}"
                                   placeholder="Ex: Riz paddy / blanc, Oignon local, Bélier Ladoum..."
                                   class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                            @error('product_name') <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="ticker_code" class="block text-xs font-bold text-slate-700 mb-1">Code Ticker Bourse</label>
                            <input id="ticker_code" name="ticker_code" type="text"
                                   value="{{ old('ticker_code', $marketPrice->ticker_code) }}"
                                   placeholder="Ex: SEN-RIZ, SEN-OIGN"
                                   class="w-full uppercase font-mono rounded-lg border-slate-300 text-sm shadow-xs focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                            <p class="mt-1 text-[11px] text-slate-400">Optionnel (généré auto si vide).</p>
                            @error('ticker_code') <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="category" class="block text-xs font-bold text-slate-700 mb-1">Catégorie agricole <span class="text-rose-500">*</span></label>
                            <input id="category" name="category" type="text" list="categories-list" required
                                   value="{{ old('category', $marketPrice->category) }}"
                                   placeholder="Céréales, Maraîchage, Élevage..."
                                   class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                            <datalist id="categories-list">
                                <option value="Céréales">
                                <option value="Céréales & Oléagineux">
                                <option value="Légumes & Maraîchage">
                                <option value="Élevage & Bétail">
                                <option value="Matériel & Services">
                                <option value="Intrants & Semences">
                                <option value="Fruits">
                                <option value="Tubercules">
                                <option value="Légumineuses">
                            </datalist>
                            @error('category') <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="unit" class="block text-xs font-bold text-slate-700 mb-1">Conditionnement / Unité de mesure</label>
                            <input id="unit" name="unit" type="text"
                                   value="{{ old('unit', $marketPrice->unit) }}"
                                   placeholder="Ex: Sac de 50 kg, Tarif par hectare, Par tête..."
                                   class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                            @error('unit') <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Cotations Boursières & Variations -->
                    <div class="rounded-lg bg-slate-50 border border-slate-200/80 p-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-black uppercase tracking-wider text-slate-800 flex items-center gap-1.5">
                                <svg class="h-4 w-4 text-[#7D5A50]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                Cotations Bourse &amp; Variations des prix
                            </span>
                            <span class="text-[11px] font-semibold text-slate-500">Bourse Agricole AgriKoom</span>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-3">
                            <div>
                                <label for="price_range" class="block text-xs font-bold text-slate-700 mb-1">Cours actuel constaté <span class="text-rose-500">*</span></label>
                                <input id="price_range" name="price_range" type="text" required
                                       value="{{ old('price_range', $marketPrice->price_range) }}"
                                       placeholder="Ex: 17 500 – 19 000 FCFA"
                                       class="w-full rounded-lg border-slate-300 text-sm font-bold text-[#7D5A50] shadow-xs focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                                @error('price_range') <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="previous_price" class="block text-xs font-bold text-slate-700 mb-1">Cours précédent de référence</label>
                                <input id="previous_price" name="previous_price" type="text"
                                       value="{{ old('previous_price', $marketPrice->previous_price) }}"
                                       placeholder="Ex: 18 200 – 19 800 FCFA"
                                       class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                                @error('previous_price') <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="change_percent" class="block text-xs font-bold text-slate-700 mb-1">Variation en %</label>
                                <input id="change_percent" name="change_percent" type="number" step="0.1"
                                       value="{{ old('change_percent', $marketPrice->change_percent ?? 0.0) }}"
                                       placeholder="Ex: -3.5 ou +4.2"
                                       class="w-full rounded-lg border-slate-300 text-sm font-bold shadow-xs focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                                @error('change_percent') <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Aide couleur règle demandée par l'utilisateur -->
                        <div class="rounded-md border border-slate-200 bg-white p-2.5 text-[11px] leading-relaxed text-slate-600 flex items-start gap-2">
                            <span class="text-base">💡</span>
                            <div>
                                <strong class="text-slate-800">Règle d'affichage Bourse :</strong>
                                <span class="inline-flex items-center gap-1 font-bold text-emerald-700 ml-1">
                                    <span class="inline-block h-2 w-2 rounded-full bg-emerald-500"></span> Négatif (ex: -3.5%) = MOINS CHER (affiché en VERT)
                                </span>
                                &bull;
                                <span class="inline-flex items-center gap-1 font-bold text-rose-700 ml-1">
                                    <span class="inline-block h-2 w-2 rounded-full bg-rose-500"></span> Positif (ex: +4.2%) = PLUS CHER (affiché en ROUGE)
                                </span>
                                &bull;
                                <span class="text-slate-500 ml-1">0% = Stable (Gris)</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Localisation, Tendance & Observations -->
                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-2xs space-y-4">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 border-b border-slate-100 pb-2">
                        2. Zone géographique &amp; Dynamique du marché
                    </h2>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="location" class="block text-xs font-bold text-slate-700 mb-1">Bassin / Zone de référence</label>
                            <input id="location" name="location" type="text"
                                   value="{{ old('location', $marketPrice->location) }}"
                                   placeholder="Ex: Vallée du fleuve, Zone des Niayes, Kaolack..."
                                   class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                            @error('location') <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="source" class="block text-xs font-bold text-slate-700 mb-1">Source de l'information</label>
                            <input id="source" name="source" type="text"
                                   value="{{ old('source', $marketPrice->source ?? 'Marchés régionaux & coopératives') }}"
                                   placeholder="Ex: Marchés régionaux, Louma de Dahra..."
                                   class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                            @error('source') <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="trend" class="block text-xs font-bold text-slate-700 mb-1">Dynamique / Tendance <span class="text-rose-500">*</span></label>
                            <select id="trend" name="trend" required
                                    class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                                <option value="stable" @selected(old('trend', $marketPrice->trend) === 'stable')>Stable (Vert émeraude)</option>
                                <option value="active" @selected(old('trend', $marketPrice->trend) === 'active')>Actif / Fort échange (Ambre)</option>
                                <option value="up" @selected(old('trend', $marketPrice->trend) === 'up')>En hausse (Rouge / Flèche haute)</option>
                                <option value="down" @selected(old('trend', $marketPrice->trend) === 'down')>En baisse (Bleu / Flèche basse)</option>
                                <option value="available" @selected(old('trend', $marketPrice->trend) === 'available')>Disponible (Vert)</option>
                                <option value="variable" @selected(old('trend', $marketPrice->trend) === 'variable')>Variable (Gris neutre)</option>
                            </select>
                            @error('trend') <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="trend_label" class="block text-xs font-bold text-slate-700 mb-1">Libellé du badge <span class="text-rose-500">*</span></label>
                            <input id="trend_label" name="trend_label" type="text" required
                                   value="{{ old('trend_label', $marketPrice->trend_label) }}"
                                   placeholder="Ex: Stable, Actif, En hausse, Variable..."
                                   class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                            @error('trend_label') <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="notes" class="block text-xs font-bold text-slate-700 mb-1">Détail ou précision complémentaire</label>
                        <input id="notes" name="notes" type="text"
                               value="{{ old('notes', $marketPrice->notes) }}"
                               placeholder="Ex: Tracteur + chauffeur + carburant inclus, Selon calibre et poids..."
                               class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                        @error('notes') <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Card 3: Paramètres d'affichage -->
                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-2xs space-y-4">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 border-b border-slate-100 pb-2">
                        3. Paramètres de publication
                    </h2>

                    <div class="grid gap-4 sm:grid-cols-2 items-center">
                        <div>
                            <label for="sort_order" class="block text-xs font-bold text-slate-700 mb-1">Position / Ordre d'affichage</label>
                            <input id="sort_order" name="sort_order" type="number" min="0"
                                   value="{{ old('sort_order', $marketPrice->sort_order) }}"
                                   class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                            <p class="mt-1 text-[11px] text-slate-400">Les petits numéros s'affichent en premier (1, 2, 3...).</p>
                            @error('sort_order') <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p> @enderror
                        </div>

                        <div class="pt-2 sm:pt-4">
                            <label class="relative flex items-start gap-3 cursor-pointer">
                                <input type="checkbox" name="is_active" value="1"
                                       @checked(old('is_active', $marketPrice->is_active))
                                       class="h-4 w-4 mt-0.5 rounded border-slate-300 text-[#7D5A50] focus:ring-[#7D5A50]">
                                <div>
                                    <span class="block text-sm font-bold text-slate-800">Afficher sur la page d'accueil</span>
                                    <span class="block text-xs text-slate-500">Si décoché, ce cours restera enregistré mais sera masqué aux visiteurs.</span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('admin.mercuriale.index') }}"
                       class="rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50 transition">
                        Annuler
                    </a>
                    <button type="submit"
                            class="rounded-lg bg-[#7D5A50] px-6 py-2.5 text-sm font-extrabold text-white shadow-xs hover:bg-[#6b4d44] transition">
                        {{ $marketPrice->exists ? 'Enregistrer les modifications' : 'Publier ce cours' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

