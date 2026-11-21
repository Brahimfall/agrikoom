<x-app-layout>
    {{-- HERO SECTION with background image --}}
    <section class="relative w-full overflow-hidden" style="min-height: 320px;">
        <div class="absolute inset-0">
            <img src="{{ asset('assets/home/agri_tracteur.jpg') }}"
                 alt="Agriculture au Sénégal"
                 class="h-full w-full object-cover object-center"
                 style="filter: brightness(0.42);">
        </div>
        <div class="absolute inset-0 bg-gradient-to-b from-[#4a342e]/70 via-[#4a342e]/30 to-[#4a342e]/80"></div>

        <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="pt-9 pb-4 sm:pt-12 sm:pb-5">
                <h1 class="text-2xl font-extrabold tracking-tight text-white sm:text-3xl lg:text-4xl" style="text-shadow: 0 2px 8px rgba(0,0,0,0.5);">
                    Le marché agricole du Sénégal
                </h1>
                <p class="mt-2 text-sm sm:text-base text-white/85 max-w-2xl">
                    Achetez et vendez directement : matériel, bétail, céréales, légumes et intrants.
                    <span class="font-semibold text-white">{{ number_format($stats['active'] ?? 0, 0, ',', ' ') }} annonces actives</span>
                    &bull;
                    <span class="font-semibold text-white/90">14 régions couvertes</span>
                </p>
            </div>

            <div class="pb-8">
                <form method="GET" action="{{ route('listings.index') }}#categories"
                      class="rounded-xl bg-white/97 shadow-2xl border border-white/30 p-4 sm:p-5"
                      style="backdrop-filter: blur(8px);">
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-[2fr_1.2fr_1.1fr_1fr_auto]">
                        <div>
                            <label for="search-input" class="block text-xs font-semibold text-slate-700 mb-1">Mot-clé / Produit</label>
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </span>
                                <input id="search-input" name="search" type="search" value="{{ request('search') }}"
                                    placeholder="Tracteur, motopompe, riz, mouton, oignons..."
                                    class="w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-9 pr-3 text-sm text-slate-900 placeholder-slate-400 shadow-xs focus:border-[#7D5A50] focus:ring-2 focus:ring-[#7D5A50]/20 focus:outline-none">
                            </div>
                        </div>

                        <div>
                            <label for="search-category" class="block text-xs font-semibold text-slate-700 mb-1">Catégorie</label>
                            <select id="search-category" name="category"
                                class="w-full rounded-lg border border-slate-300 bg-white py-2.5 px-3 text-sm text-slate-900 shadow-xs focus:border-[#7D5A50] focus:ring-2 focus:ring-[#7D5A50]/20 focus:outline-none">
                                <option value="">Toutes les catégories</option>
                                @foreach ($categoryTiles as $cat)
                                    <option value="{{ $cat->id }}" @selected((string) request('category') === (string) $cat->id)>
                                        {{ $cat->name }} ({{ $cat->active_listings_count }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="search-location" class="block text-xs font-semibold text-slate-700 mb-1">Région / Ville</label>
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </span>
                                <input id="search-location" name="location" type="text" value="{{ request('location') }}"
                                    placeholder="Toute la région"
                                    class="w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-9 pr-3 text-sm text-slate-900 placeholder-slate-400 shadow-xs focus:border-[#7D5A50] focus:ring-2 focus:ring-[#7D5A50]/20 focus:outline-none">
                            </div>
                        </div>

                        <div>
                            <label for="search-type" class="block text-xs font-semibold text-slate-700 mb-1">Type</label>
                            <select id="search-type" name="type"
                                class="w-full rounded-lg border border-slate-300 bg-white py-2.5 px-3 text-sm text-slate-900 shadow-xs focus:border-[#7D5A50] focus:ring-2 focus:ring-[#7D5A50]/20 focus:outline-none">
                                <option value="">Offres &amp; Demandes</option>
                                <option value="offer" @selected(request('type') === 'offer')>Offres de vente</option>
                                <option value="request" @selected(request('type') === 'request')>Demandes d'achat</option>
                            </select>
                        </div>

                        <div class="flex items-end">
                            <button type="submit"
                                class="flex w-full items-center justify-center gap-2 rounded-lg bg-[#7D5A50] px-6 py-2.5 text-sm font-bold text-white shadow-md transition hover:bg-[#6b4d44] active:scale-[0.98]">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                Rechercher
                            </button>
                        </div>
                    </div>

                    <div class="mt-3 flex flex-wrap items-center gap-1.5 pt-2.5 border-t border-slate-200 text-xs">
                        <span class="font-semibold text-slate-600">Accès rapide :</span>
                        <a href="{{ route('listings.index', ['search' => 'Tracteur']) }}#categories" class="rounded-full border border-slate-200 bg-slate-50 px-2.5 py-0.5 text-slate-600 hover:bg-[#7D5A50] hover:text-white hover:border-[#7D5A50] transition">Tracteurs</a>
                        <a href="{{ route('listings.index', ['search' => 'Motopompe']) }}#categories" class="rounded-full border border-slate-200 bg-slate-50 px-2.5 py-0.5 text-slate-600 hover:bg-[#7D5A50] hover:text-white hover:border-[#7D5A50] transition">Motopompes</a>
                        <a href="{{ route('listings.index', ['search' => 'Riz']) }}#categories" class="rounded-full border border-slate-200 bg-slate-50 px-2.5 py-0.5 text-slate-600 hover:bg-[#7D5A50] hover:text-white hover:border-[#7D5A50] transition">Riz vallée</a>
                        <a href="{{ route('listings.index', ['search' => 'Mouton']) }}#categories" class="rounded-full border border-slate-200 bg-slate-50 px-2.5 py-0.5 text-slate-600 hover:bg-[#7D5A50] hover:text-white hover:border-[#7D5A50] transition">Élevage</a>
                        <a href="{{ route('listings.index', ['search' => 'Oignons']) }}#categories" class="rounded-full border border-slate-200 bg-slate-50 px-2.5 py-0.5 text-slate-600 hover:bg-[#7D5A50] hover:text-white hover:border-[#7D5A50] transition">Oignons</a>
                        <a href="{{ route('listings.index', ['search' => 'Engrais']) }}#categories" class="rounded-full border border-slate-200 bg-slate-50 px-2.5 py-0.5 text-slate-600 hover:bg-[#7D5A50] hover:text-white hover:border-[#7D5A50] transition">Intrants</a>
                    </div>
                </form>
            </div>
        </div>
    </section>

    {{-- Reassurance Bar --}}
    <div class="border-b border-slate-200 bg-white py-3 shadow-xs">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 gap-3 text-xs text-slate-700 sm:grid-cols-4">
                <div class="flex items-center gap-2">
                    <svg class="h-4 w-4 text-[#7D5A50] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span>Spécialisé agriculture &amp; rural</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="h-4 w-4 text-[#7D5A50] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>Direct vendeurs sans commission</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="h-4 w-4 text-[#7D5A50] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    <span>Contact WhatsApp &amp; téléphone</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="h-4 w-4 text-[#7D5A50] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    <span>Numéros vérifiés par SMS</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Category Grid with real photos (Pointe directement vers la page de produits) --}}
    <section class="border-b border-slate-200 bg-white py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-bold text-slate-900 sm:text-lg">Parcourir les produits par catégorie</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Accédez directement aux offres de producteurs et distributeurs</p>
                </div>
                <a href="{{ route('products.index') }}"
                   class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3.5 py-1.5 text-xs font-semibold text-slate-700 shadow-2xs hover:bg-slate-50 hover:text-[#7D5A50] hover:border-[#7D5A50] transition">
                    <span>Tous les produits</span>
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

            @php
                $categoryImages = [
                    'Cereales'            => 'prod_cereales.jpg',
                    'Legumes'             => 'prod_legumes.jpg',
                    'Elevage'             => 'agri_elevage.jpg',
                    'Materiel agricole'   => 'agri_tracteur.jpg',
                    'Intrants'            => 'prod_cereales.jpg',
                    'Fruits'              => 'prod_fruits.jpg',
                    'Legumineuses'        => 'prod_legumes.jpg',
                    'Produits horticoles' => 'prod_legumes.jpg',
                    'Tubercules'          => 'prod_manioc.jpg',
                ];
                $categorySubtitles = [
                    'Materiel agricole'   => 'Tracteurs, outils du sol, récolte',
                    'Elevage'             => 'Bovins, ovins, caprins, provende',
                    'Cereales'            => 'Riz de la vallée, maïs, mil, sorgho',
                    'Legumes'             => 'Oignons des Niayes, tomates, piments',
                    'Intrants'            => 'Semences certifiées, engrais, compost',
                    'Fruits'              => 'Mangues, agrumes, papayes',
                    'Tubercules'          => 'Manioc, patates douces, igname',
                    'Legumineuses'        => 'Arachides, niébé, sésame',
                    'Produits horticoles' => 'Production maraîchère et pépinières',
                ];
            @endphp

            <div class="grid grid-cols-2 gap-2 sm:gap-3 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                @foreach ($categoryTiles as $categoryTile)
                    @php
                        $categoryImage    = $categoryImages[$categoryTile->name] ?? 'prod_legumes.jpg';
                        $subtitle         = $categorySubtitles[$categoryTile->name] ?? 'Productions et équipements';
                    @endphp
                    <a href="{{ route('products.index', ['category' => $categoryTile->id]) }}"
                       class="group relative block overflow-hidden rounded-lg border border-slate-200 shadow-xs transition hover:shadow-md hover:border-[#7D5A50]"
                       style="aspect-ratio: 4/3;">
                        <img src="{{ asset('assets/home/'.$categoryImage) }}"
                             alt="{{ $categoryTile->name }}"
                             onerror="this.onerror=null;this.src='{{ asset('assets/home/prod_legumes.jpg') }}';"
                             class="absolute inset-0 h-full w-full object-cover transition duration-300 group-hover:scale-105">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent"></div>
                        <span class="absolute top-1.5 right-1.5 rounded bg-black/70 px-1.5 py-0.2 text-[9px] font-bold text-white">
                            {{ $categoryTile->active_listings_count }} ann.
                        </span>
                        <div class="absolute bottom-0 left-0 right-0 p-2 sm:p-3">
                            <h3 class="text-xs sm:text-sm font-extrabold text-white leading-tight" style="text-shadow:0 1px 4px rgba(0,0,0,0.6);">{{ $categoryTile->name }}</h3>
                            <p class="hidden sm:block mt-0.5 text-[10px] text-white/80 line-clamp-1">{{ $subtitle }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Mercuriale & cours indicatifs des marchés locaux (Bourse Agricole Experte) --}}
    @php
        $pricesList = ($marketPrices ?? collect())->isNotEmpty() ? $marketPrices : collect([
            (object) [
                'id' => 1,
                'product_name' => 'Riz paddy / blanc',
                'ticker_display' => 'SEN-RIZ',
                'category' => 'Céréales',
                'price_range' => '17 500 – 19 000 FCFA',
                'previous_price' => '18 200 – 19 800 FCFA',
                'change_percent' => -3.50,
                'formatted_change' => '-3,5%',
                'bourse_percent_display' => '-3.50 %',
                'bourse_percent_text_color' => 'text-emerald-600 font-extrabold',
                'bourse_stripe_color' => 'bg-emerald-500',
                'bourse_square_color' => '#1e3a8a',
                'bourse_badge_classes' => 'bg-emerald-50 text-emerald-700 border-emerald-300 ring-1 ring-emerald-500/20',
                'bourse_dark_classes' => 'text-emerald-400 bg-emerald-950/70 border border-emerald-500/30',
                'bourse_arrow' => '▼',
                'is_cheaper' => true,
                'is_more_expensive' => false,
                'icon_emoji' => '🌾',
                'unit' => 'Sac de 50 kg',
                'location' => 'Vallée du fleuve (Saint-Louis)',
                'notes' => 'Riz blanc brisé et paddy bord-champ',
                'trend_label' => 'Moins cher',
            ],
            (object) [
                'id' => 2,
                'product_name' => 'Oignon local',
                'ticker_display' => 'SEN-OIGN',
                'category' => 'Légumes & Maraîchage',
                'price_range' => '12 000 – 13 500 FCFA',
                'previous_price' => '11 000 – 12 200 FCFA',
                'change_percent' => 8.20,
                'formatted_change' => '+8,2%',
                'bourse_percent_display' => '+8.20 %',
                'bourse_percent_text_color' => 'text-rose-600 font-extrabold',
                'bourse_stripe_color' => 'bg-rose-500',
                'bourse_square_color' => '#d97706',
                'bourse_badge_classes' => 'bg-rose-50 text-rose-700 border-rose-300 ring-1 ring-rose-500/20',
                'bourse_dark_classes' => 'text-rose-400 bg-rose-950/70 border border-rose-500/30',
                'bourse_arrow' => '▲',
                'is_cheaper' => false,
                'is_more_expensive' => true,
                'icon_emoji' => '🧅',
                'unit' => 'Sac de 25 kg',
                'location' => 'Zone des Niayes (Thiès, Potou)',
                'notes' => 'Oignon violet et jaune séché',
                'trend_label' => 'Plus cher',
            ],
            (object) [
                'id' => 3,
                'product_name' => 'Prestation de labour',
                'ticker_display' => 'SEN-LABR',
                'category' => 'Matériel & Services',
                'price_range' => '25 000 – 30 000 FCFA',
                'previous_price' => '25 000 – 30 000 FCFA',
                'change_percent' => 0.00,
                'formatted_change' => '0,0%',
                'bourse_percent_display' => '0.00 %',
                'bourse_percent_text_color' => 'text-slate-500 font-semibold',
                'bourse_stripe_color' => 'bg-slate-300',
                'bourse_square_color' => '#0d9488',
                'bourse_badge_classes' => 'bg-slate-100 text-slate-700 border-slate-300',
                'bourse_dark_classes' => 'text-slate-300 bg-slate-800/80 border border-slate-700',
                'bourse_arrow' => '=',
                'is_cheaper' => false,
                'is_more_expensive' => false,
                'icon_emoji' => '🚜',
                'unit' => 'Tarif par hectare',
                'location' => 'Toutes zones agricoles',
                'notes' => 'Tracteur + chauffeur + carburant',
                'trend_label' => 'Stable',
            ],
            (object) [
                'id' => 4,
                'product_name' => 'Bélier de race',
                'ticker_display' => 'SEN-BLIR',
                'category' => 'Élevage & Bétail',
                'price_range' => '160 000 – 780 000 FCFA',
                'previous_price' => '150 000 – 740 000 FCFA',
                'change_percent' => 5.40,
                'formatted_change' => '+5,4%',
                'bourse_percent_display' => '+5.40 %',
                'bourse_percent_text_color' => 'text-rose-600 font-extrabold',
                'bourse_stripe_color' => 'bg-rose-500',
                'bourse_square_color' => '#dc2626',
                'bourse_badge_classes' => 'bg-rose-50 text-rose-700 border-rose-300 ring-1 ring-rose-500/20',
                'bourse_dark_classes' => 'text-rose-400 bg-rose-950/70 border border-rose-500/30',
                'bourse_arrow' => '▲',
                'is_cheaper' => false,
                'is_more_expensive' => true,
                'icon_emoji' => '🐑',
                'unit' => 'Par tête',
                'location' => 'Dakar, Thiès, Louga, Kaolack',
                'notes' => 'Ladoum / Touabire selon gabarit',
                'trend_label' => 'Plus cher',
            ],
            (object) [
                'id' => 5,
                'product_name' => 'Maïs local grain',
                'ticker_display' => 'SEN-MAIS',
                'category' => 'Céréales',
                'price_range' => '13 500 – 15 500 FCFA',
                'previous_price' => '13 500 – 15 500 FCFA',
                'change_percent' => 0.00,
                'formatted_change' => '0,0%',
                'bourse_percent_display' => '0.00 %',
                'bourse_percent_text_color' => 'text-slate-500 font-semibold',
                'bourse_stripe_color' => 'bg-slate-300',
                'bourse_square_color' => '#6366f1',
                'bourse_badge_classes' => 'bg-slate-100 text-slate-700 border-slate-300',
                'bourse_dark_classes' => 'text-slate-300 bg-slate-800/80 border border-slate-700',
                'bourse_arrow' => '=',
                'is_cheaper' => false,
                'is_more_expensive' => false,
                'icon_emoji' => '🌽',
                'unit' => 'Sac de 50 kg',
                'location' => 'Kaolack & Nioro',
                'notes' => 'Maïs jaune de qualité supérieure',
                'trend_label' => 'Stable',
            ],
            (object) [
                'id' => 6,
                'product_name' => 'Pomme de terre locale',
                'ticker_display' => 'SEN-PDTR',
                'category' => 'Légumes & Maraîchage',
                'price_range' => '11 500 – 13 000 FCFA',
                'previous_price' => '12 500 – 14 000 FCFA',
                'change_percent' => -6.50,
                'formatted_change' => '-6,5%',
                'bourse_percent_display' => '-6.50 %',
                'bourse_percent_text_color' => 'text-emerald-600 font-extrabold',
                'bourse_stripe_color' => 'bg-emerald-500',
                'bourse_square_color' => '#0284c7',
                'bourse_badge_classes' => 'bg-emerald-50 text-emerald-700 border-emerald-300 ring-1 ring-emerald-500/20',
                'bourse_dark_classes' => 'text-emerald-400 bg-emerald-950/70 border border-emerald-500/30',
                'bourse_arrow' => '▼',
                'is_cheaper' => true,
                'is_more_expensive' => false,
                'icon_emoji' => '🥔',
                'unit' => 'Sac de 25 kg',
                'location' => 'Zone des Niayes (Mboro, Kayar)',
                'notes' => 'Calibre moyen et gros',
                'trend_label' => 'Moins cher',
            ],
            (object) [
                'id' => 7,
                'product_name' => 'Arachide décortiquée',
                'ticker_display' => 'SEN-ARAC',
                'category' => 'Céréales & Oléagineux',
                'price_range' => '27 500 – 30 000 FCFA',
                'previous_price' => '28 800 – 31 500 FCFA',
                'change_percent' => -4.20,
                'formatted_change' => '-4,2%',
                'bourse_percent_display' => '-4.20 %',
                'bourse_percent_text_color' => 'text-emerald-600 font-extrabold',
                'bourse_stripe_color' => 'bg-emerald-500',
                'bourse_square_color' => '#64748b',
                'bourse_badge_classes' => 'bg-emerald-50 text-emerald-700 border-emerald-300 ring-1 ring-emerald-500/20',
                'bourse_dark_classes' => 'text-emerald-400 bg-emerald-950/70 border border-emerald-500/30',
                'bourse_arrow' => '▼',
                'is_cheaper' => true,
                'is_more_expensive' => false,
                'icon_emoji' => '🥜',
                'unit' => 'Sac de 50 kg',
                'location' => 'Bassin arachidier (Kaolack)',
                'notes' => 'Graines saines et triées',
                'trend_label' => 'Moins cher',
            ],
            (object) [
                'id' => 8,
                'product_name' => 'Tomate fraîche de table',
                'ticker_display' => 'SEN-TOMA',
                'category' => 'Légumes & Maraîchage',
                'price_range' => '8 500 – 10 500 FCFA',
                'previous_price' => '7 500 – 9 200 FCFA',
                'change_percent' => 12.50,
                'formatted_change' => '+12,5%',
                'bourse_percent_display' => '+12.50 %',
                'bourse_percent_text_color' => 'text-rose-600 font-extrabold',
                'bourse_stripe_color' => 'bg-rose-500',
                'bourse_square_color' => '#334155',
                'bourse_badge_classes' => 'bg-rose-50 text-rose-700 border-rose-300 ring-1 ring-rose-500/20',
                'bourse_dark_classes' => 'text-rose-400 bg-rose-950/70 border border-rose-500/30',
                'bourse_arrow' => '▲',
                'is_cheaper' => false,
                'is_more_expensive' => true,
                'icon_emoji' => '🍅',
                'unit' => 'Caisse de 20 kg',
                'location' => 'Niayes & Vallée du fleuve',
                'notes' => 'Forte demande marché urbain',
                'trend_label' => 'Plus cher',
            ],
        ]);
        $distinctCategories = $pricesList->pluck('category')->filter()->unique()->values();
        $cheaperCount = $pricesList->where('is_cheaper', true)->count();
        $expensiveCount = $pricesList->where('is_more_expensive', true)->count();
        $stableCount = max(0, $pricesList->count() - $cheaperCount - $expensiveCount);
    @endphp

    <section id="mercuriale" class="border-b border-slate-200 bg-slate-50/60 py-8" x-data="{ activeCategory: 'all', viewMode: 'cards' }">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <!-- Définitions SVG réutilisables pour les graphiques de tendance (Sparklines) -->
            <svg class="hidden" width="0" height="0">
                <defs>
                    <linearGradient id="grad-spark-green" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#10b981" stop-opacity="0.3"/>
                        <stop offset="100%" stop-color="#10b981" stop-opacity="0.0"/>
                    </linearGradient>
                    <linearGradient id="grad-spark-red" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#f43f5e" stop-opacity="0.3"/>
                        <stop offset="100%" stop-color="#f43f5e" stop-opacity="0.0"/>
                    </linearGradient>
                    <linearGradient id="grad-spark-gray" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#94a3b8" stop-opacity="0.2"/>
                        <stop offset="100%" stop-color="#94a3b8" stop-opacity="0.0"/>
                    </linearGradient>
                </defs>
            </svg>

            <!-- Ticker Tape Boursier Continu (Ruban défilant premium) -->
            <div class="mb-5 overflow-hidden rounded-2xl bg-slate-950 border border-slate-800 text-white shadow-lg">
                <div class="flex items-center">
                    <!-- Pastille LIVE fixe à gauche -->
                    <div class="z-10 flex shrink-0 items-center gap-2 bg-[#7D5A50] px-4 py-3 text-xs font-black uppercase tracking-wider text-white shadow-sm">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
                        </span>
                        <span class="whitespace-nowrap tracking-wide">Bourse Live</span>
                    </div>

                    <!-- Ruban défilant continu -->
                    <div class="relative overflow-hidden w-full py-2.5 bg-slate-900/90 text-xs">
                        <div class="animate-ticker flex items-center space-x-8 text-slate-200">
                            {{-- Boucle 1 --}}
                            @foreach ($pricesList as $priceTicker)
                                <div class="inline-flex items-center gap-2.5 whitespace-nowrap">
                                    <span class="text-sm">{{ $priceTicker->icon_emoji ?? '🌱' }}</span>
                                    <span class="font-mono text-[10px] font-black uppercase tracking-wider text-amber-300 bg-black/60 px-1.5 py-0.5 rounded border border-amber-400/20">
                                        {{ $priceTicker->ticker_display }}
                                    </span>
                                    <span class="font-extrabold text-white text-xs uppercase">{{ $priceTicker->product_name }}</span>
                                    <span class="font-black text-[#e0a96d]">{{ $priceTicker->price_range }}</span>
                                    <span class="font-mono text-[11px] font-black {{ $priceTicker->bourse_percent_text_color }}">
                                        {{ $priceTicker->bourse_percent_display }}
                                    </span>
                                    <span class="text-slate-600 font-bold">&bull;</span>
                                </div>
                            @endforeach

                            {{-- Boucle 2 (Duplicata pour raccord infini parfait) --}}
                            @foreach ($pricesList as $priceTicker)
                                <div class="inline-flex items-center gap-2.5 whitespace-nowrap">
                                    <span class="text-sm">{{ $priceTicker->icon_emoji ?? '🌱' }}</span>
                                    <span class="font-mono text-[10px] font-black uppercase tracking-wider text-amber-300 bg-black/60 px-1.5 py-0.5 rounded border border-amber-400/20">
                                        {{ $priceTicker->ticker_display }}
                                    </span>
                                    <span class="font-extrabold text-white text-xs uppercase">{{ $priceTicker->product_name }}</span>
                                    <span class="font-black text-[#e0a96d]">{{ $priceTicker->price_range }}</span>
                                    <span class="font-mono text-[11px] font-black {{ $priceTicker->bourse_percent_text_color }}">
                                        {{ $priceTicker->bourse_percent_display }}
                                    </span>
                                    <span class="text-slate-600 font-bold">&bull;</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bloc Principal Bourse & Prix du Marché -->
            <div class="rounded-3xl border border-slate-200/90 bg-white p-5 sm:p-7 shadow-xs">
                
                <!-- Header : Titre + Sentiment du Marché + Basculeurs de Vue -->
                <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between border-b border-slate-100 pb-6">
                    <div>
                        <div class="flex flex-wrap items-center gap-2.5">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-[#7D5A50]/10 border border-[#7D5A50]/20 px-3 py-1 text-xs font-bold text-[#7D5A50]">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                Bourse Agricole du Sénégal
                            </span>
                            @if (!empty($marketPricesLastUpdated))
                                <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-[11px] font-semibold text-slate-500">
                                    Actualisé {{ \Carbon\Carbon::parse($marketPricesLastUpdated)->diffForHumans() }}
                                </span>
                            @endif
                        </div>

                        <h2 class="mt-2.5 text-2xl sm:text-3xl font-black text-slate-950 tracking-tight">
                            Les prix du marché en direct
                        </h2>
                        <p class="mt-1 text-xs sm:text-sm text-slate-500 max-w-2xl">
                            Cotations relevées bord-champ, auprès des coopératives et sur les loumas hebdomadaires du Sénégal.
                        </p>

                        <!-- Baromètre du Marché (Sentiment Boursier) -->
                        <div class="mt-4 flex flex-wrap items-center gap-2">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mr-1">Tendance globale :</span>
                            <span class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-50 border border-emerald-200 px-2.5 py-1 text-xs font-black text-emerald-700 shadow-2xs">
                                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                <span>{{ $cheaperCount }} denrées en baisse</span>
                                <span class="text-[10px] font-semibold text-emerald-600/80">(Opportunité acheteurs)</span>
                            </span>
                            <span class="inline-flex items-center gap-1.5 rounded-lg bg-rose-50 border border-rose-200 px-2.5 py-1 text-xs font-black text-rose-700 shadow-2xs">
                                <span class="h-2 w-2 rounded-full bg-rose-500"></span>
                                <span>{{ $expensiveCount }} en hausse</span>
                            </span>
                            @if ($stableCount > 0)
                                <span class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 border border-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-600 shadow-2xs">
                                    <span class="h-2 w-2 rounded-full bg-slate-400"></span>
                                    <span>{{ $stableCount }} stables</span>
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Contrôles : Sélecteur de vue (Cartes vs Liste) & Bouton Admin -->
                    <div class="flex flex-wrap items-center gap-3 self-start lg:self-center shrink-0">
                        <!-- Sélecteur de Vue Cartes / Tableau -->
                        <div class="inline-flex items-center rounded-xl border border-slate-200 bg-slate-100 p-1 text-xs font-bold text-slate-600 shadow-2xs">
                            <button type="button"
                                    @click="viewMode = 'cards'"
                                    :class="viewMode === 'cards' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-900'"
                                    class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 transition">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                <span>Cartes</span>
                            </button>
                            <button type="button"
                                    @click="viewMode = 'table'"
                                    :class="viewMode === 'table' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-900'"
                                    class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 transition">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                                <span>Bourse compacte</span>
                            </button>
                        </div>

                        @auth
                            @if (Auth::user()->is_admin)
                                <a href="{{ route('admin.mercuriale.index') }}"
                                   class="inline-flex items-center gap-1.5 rounded-xl border border-[#7D5A50] bg-[#7D5A50] px-4 py-2 text-xs font-bold text-white shadow-xs hover:bg-[#6b4d44] transition active:scale-98">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    <span>⚙️ Gérer les prix</span>
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>

                <!-- Onglets interactifs par Catégorie (Filtre dynamique) -->
                @if ($distinctCategories->count() > 1)
                    <div class="mt-4 flex flex-wrap items-center gap-1.5 pt-1 overflow-x-auto scrollbar-none">
                        <button type="button"
                                @click="activeCategory = 'all'"
                                :class="activeCategory === 'all' ? 'bg-[#7D5A50] text-white shadow-xs font-bold' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-100 font-semibold'"
                                class="rounded-lg px-3 py-1.5 text-xs transition whitespace-nowrap">
                            Toutes les cotations ({{ $pricesList->count() }})
                        </button>
                        @foreach ($distinctCategories as $catName)
                            <button type="button"
                                    @click="activeCategory = '{{ addslashes($catName) }}'"
                                    :class="activeCategory === '{{ addslashes($catName) }}' ? 'bg-[#7D5A50] text-white shadow-xs font-bold' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-100 font-semibold'"
                                    class="rounded-lg px-3 py-1.5 text-xs transition whitespace-nowrap">
                                {{ $catName }}
                            </button>
                        @endforeach
                    </div>
                @endif

                <!-- VUE 1 : CARTES DE COTATIONS BOURSIÈRES (Vue par défaut avec sparklines, métriques & défilement fluide mobile) -->
                <div x-show="viewMode === 'cards'"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="mt-5">
                    
                    <!-- Grille responsive : Défilement horizontal 'snap-x' sur mobile, Grille 4 colonnes sur desktop -->
                    <div class="flex overflow-x-auto snap-x snap-mandatory gap-3.5 pb-3 -mx-4 px-4 sm:mx-0 sm:px-0 sm:grid sm:grid-cols-2 lg:grid-cols-4 sm:gap-4 scrollbar-none">
                        @foreach ($pricesList as $priceItem)
                            <div x-show="activeCategory === 'all' || activeCategory === '{{ addslashes($priceItem->category) }}'"
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 scale-98"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 class="snap-start shrink-0 w-[285px] sm:w-auto relative flex flex-col justify-between rounded-2xl border border-slate-200/90 bg-white p-4 shadow-xs hover:shadow-md hover:border-slate-300 transition-all group overflow-hidden {{ $priceItem->is_cheaper ? 'border-t-3 border-t-emerald-500' : ($priceItem->is_more_expensive ? 'border-t-3 border-t-rose-500' : 'border-t-3 border-t-slate-300') }}">
                                
                                <div>
                                    <!-- En-tête de la carte : Emoji + Ticker + Badge Bourse -->
                                    <div class="flex items-center justify-between gap-2">
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-50 border border-slate-100 text-lg shadow-2xs group-hover:scale-105 transition-transform">
                                                {{ $priceItem->icon_emoji }}
                                            </span>
                                            <div>
                                                <span class="font-mono text-[10px] font-bold text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200">
                                                    {{ $priceItem->ticker_display }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Badge Bourse (Vert si moins cher, Rouge si plus cher) -->
                                        <span class="inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-xs font-black font-mono border shadow-2xs {{ $priceItem->bourse_badge_classes }}">
                                            <span class="text-[10px] leading-none">{{ $priceItem->bourse_arrow }}</span>
                                            <span>{{ $priceItem->bourse_percent_display }}</span>
                                        </span>
                                    </div>

                                    <!-- Nom du Produit & Catégorie -->
                                    <div class="mt-3">
                                        <a href="{{ route('listings.index', ['search' => explode(' ', $priceItem->product_name)[0]]) }}#categories"
                                           class="font-black text-slate-900 text-sm tracking-tight uppercase hover:text-[#7D5A50] transition line-clamp-1"
                                           title="{{ $priceItem->product_name }}">
                                            {{ $priceItem->product_name }}
                                        </a>
                                        <div class="text-[11px] text-slate-500 font-medium truncate flex items-center gap-1.5 mt-0.5">
                                            <span class="text-slate-700 font-semibold">{{ $priceItem->category }}</span>
                                            @if ($priceItem->location)
                                                <span>&bull;</span>
                                                <span class="truncate text-slate-500">{{ $priceItem->location }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Mini Sparkline Graphique SVG de Tendance (Animé & Dégradé) -->
                                    <div class="my-3 py-1 bg-slate-50/60 rounded-lg px-2 border border-slate-100/80">
                                        <div class="flex items-center justify-between text-[9px] font-mono text-slate-400 mb-1">
                                            <span>Tendance 7j</span>
                                            <span class="{{ $priceItem->bourse_percent_text_color }} font-bold">
                                                {{ $priceItem->is_cheaper ? 'Baisse des prix' : ($priceItem->is_more_expensive ? 'Hausse des prix' : 'Cours stable') }}
                                            </span>
                                        </div>
                                        <svg class="w-full h-7 overflow-visible" viewBox="0 0 120 28" preserveAspectRatio="none">
                                            @if ($priceItem->is_cheaper)
                                                <!-- Courbe baissière (prix en baisse = bon pour l'acheteur, tracé vert) -->
                                                <path d="M0,8 Q30,4 60,14 T120,24" fill="none" stroke="{{ $priceItem->sparkline_color }}" stroke-width="2" stroke-linecap="round"/>
                                                <path d="M0,8 Q30,4 60,14 T120,24 L120,28 L0,28 Z" fill="url(#grad-spark-green)"/>
                                                <circle cx="120" cy="24" r="2.5" fill="{{ $priceItem->sparkline_color }}"/>
                                            @elseif ($priceItem->is_more_expensive)
                                                <!-- Courbe haussière (prix en hausse = tracé rouge) -->
                                                <path d="M0,22 Q30,24 60,12 T120,4" fill="none" stroke="{{ $priceItem->sparkline_color }}" stroke-width="2" stroke-linecap="round"/>
                                                <path d="M0,22 Q30,24 60,12 T120,4 L120,28 L0,28 Z" fill="url(#grad-spark-red)"/>
                                                <circle cx="120" cy="4" r="2.5" fill="{{ $priceItem->sparkline_color }}"/>
                                            @else
                                                <!-- Courbe stable (tracé gris) -->
                                                <path d="M0,14 Q30,12 60,16 T120,14" fill="none" stroke="{{ $priceItem->sparkline_color }}" stroke-width="1.8" stroke-dasharray="3,3" stroke-linecap="round"/>
                                                <path d="M0,14 Q30,12 60,16 T120,14 L120,28 L0,28 Z" fill="url(#grad-spark-gray)"/>
                                                <circle cx="120" cy="14" r="2" fill="{{ $priceItem->sparkline_color }}"/>
                                            @endif
                                        </svg>
                                    </div>
                                </div>

                                <!-- Prix Actuel & Actions -->
                                <div class="pt-2 border-t border-slate-100">
                                    <div class="flex items-baseline justify-between gap-1">
                                        <div>
                                            <div class="font-black text-slate-900 text-sm sm:text-base font-mono tracking-tight">
                                                {{ $priceItem->price_range }}
                                            </div>
                                            @if ($priceItem->previous_price)
                                                <div class="text-[10px] text-slate-400">
                                                    Réf préc. : <span class="line-through">{{ $priceItem->previous_price }}</span>
                                                </div>
                                            @elseif ($priceItem->unit)
                                                <div class="text-[10px] text-slate-400">
                                                    Base : <span class="font-medium text-slate-600">{{ $priceItem->unit }}</span>
                                                </div>
                                            @endif
                                        </div>

                                        @auth
                                            @if (Auth::user()->is_admin && isset($priceItem->id))
                                                <a href="{{ route('admin.mercuriale.edit', $priceItem->id) }}"
                                                   title="Modifier ce cours"
                                                   class="rounded-md border border-slate-200 bg-white p-1 text-slate-400 hover:text-[#7D5A50] hover:border-[#7D5A50] transition shadow-2xs">
                                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                                </a>
                                            @endif
                                        @endauth
                                    </div>

                                    <!-- Bouton d'action direct vers les offres correspondantes -->
                                    <a href="{{ route('listings.index', ['search' => explode(' ', $priceItem->product_name)[0]]) }}#categories"
                                       class="mt-3 inline-flex items-center justify-center gap-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-bold text-slate-700 hover:bg-[#7D5A50] hover:text-white hover:border-[#7D5A50] transition-colors shadow-2xs group-hover:border-[#7D5A50]/40">
                                        <span>Consulter les offres</span>
                                        <svg class="h-3.5 w-3.5 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Indication swipe pour mobile -->
                    <div class="sm:hidden flex items-center justify-center gap-1.5 text-[11px] font-medium text-slate-500 mt-2">
                        <svg class="h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        <span>Faites glisser horizontalement pour voir toutes les cotations</span>
                    </div>
                </div>

                <!-- VUE 2 : TABLEAU DOUBLE COLONNE COMPACT STYLE BOURSE (Fidèle à la capture d'écran, sans étirement) -->
                <div x-show="viewMode === 'table'"
                     x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="mt-5">
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        @php
                            $halfCount = ceil($pricesList->count() / 2);
                            $columns = [$pricesList->slice(0, $halfCount), $pricesList->slice($halfCount)];
                        @endphp

                        @foreach ($columns as $colIndex => $colItems)
                            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xs">
                                <!-- En-tête boursier compact -->
                                <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50/90 px-4 py-2 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                                    <span>Indice / Produit</span>
                                    <div class="flex items-center gap-8 text-right">
                                        <span>Cours</span>
                                        <span class="w-16">Variation</span>
                                    </div>
                                </div>

                                <!-- Lignes boursières (style capture d'écran) -->
                                <div class="divide-y divide-slate-100">
                                    @foreach ($colItems as $priceItem)
                                        <div x-show="activeCategory === 'all' || activeCategory === '{{ addslashes($priceItem->category) }}'"
                                             x-transition:enter="transition ease-out duration-150"
                                             x-transition:enter-start="opacity-0"
                                             x-transition:enter-end="opacity-100"
                                             class="relative flex items-center justify-between py-3 px-4 hover:bg-slate-50/80 transition-colors group">
                                            
                                            <!-- Barre latérale indicatrice verticale (Gauche : Vert = moins cher, Rouge = plus cher) -->
                                            <div class="absolute left-0 top-0 bottom-0 w-1.5 {{ $priceItem->bourse_stripe_color }}"></div>

                                            <!-- Carré coloré + Nom Produit en Majuscules (Style CAC 40 / DAX / NASDAQ) -->
                                            <div class="flex items-center gap-2.5 min-w-0 pr-2">
                                                <span class="h-3 w-3 rounded-[2px] shrink-0 shadow-2xs"
                                                      style="background-color: {{ $priceItem->bourse_square_color ?? '#1e3a8a' }};"></span>
                                                <div class="min-w-0">
                                                    <div class="flex items-center gap-1.5">
                                                        <a href="{{ route('listings.index', ['search' => explode(' ', $priceItem->product_name)[0]]) }}#categories"
                                                           class="font-extrabold text-slate-900 text-xs sm:text-sm tracking-tight uppercase hover:text-[#7D5A50] transition truncate">
                                                            {{ $priceItem->product_name }}
                                                        </a>
                                                        <span class="hidden sm:inline-block font-mono text-[9px] font-bold text-slate-500 bg-slate-100 px-1 py-0.2 rounded border border-slate-200">
                                                            {{ $priceItem->ticker_display }}
                                                        </span>
                                                    </div>
                                                    <div class="text-[10px] text-slate-400 font-medium truncate flex items-center gap-1 mt-0.5">
                                                        <span class="text-slate-600 font-semibold">{{ $priceItem->category }}</span>
                                                        @if ($priceItem->location)
                                                            <span>&bull;</span>
                                                            <span class="truncate">{{ $priceItem->location }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Droite : Prix indicatif + Pourcentage Bourse (+0.09 %, -0.40 %) -->
                                            <div class="flex items-center gap-4 sm:gap-6 shrink-0 text-right">
                                                <div class="text-right">
                                                    <span class="font-extrabold text-slate-900 text-xs sm:text-sm tracking-tight font-mono">
                                                        {{ $priceItem->price_range }}
                                                    </span>
                                                    @if ($priceItem->previous_price)
                                                        <div class="text-[9px] text-slate-400">
                                                            Réf : <span class="line-through">{{ $priceItem->previous_price }}</span>
                                                        </div>
                                                    @elseif ($priceItem->unit)
                                                        <div class="text-[9px] text-slate-400 uppercase">
                                                            {{ $priceItem->unit }}
                                                        </div>
                                                    @endif
                                                </div>

                                                <!-- Variation en Pourcentage (Style exact capture Bourse) -->
                                                <div class="w-16 sm:w-20 text-right">
                                                    <span class="text-xs sm:text-sm font-black font-mono tracking-tight {{ $priceItem->bourse_percent_text_color }}">
                                                        {{ $priceItem->bourse_percent_display }}
                                                    </span>
                                                </div>

                                                <!-- Actions rapides -->
                                                <div class="hidden sm:flex items-center gap-1 pl-1">
                                                    <a href="{{ route('listings.index', ['search' => explode(' ', $priceItem->product_name)[0]]) }}#categories"
                                                       title="Voir les offres"
                                                       class="rounded border border-slate-200 bg-white p-1 text-slate-500 hover:text-[#7D5A50] hover:border-[#7D5A50] transition shadow-2xs">
                                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                                    </a>
                                                    @auth
                                                        @if (Auth::user()->is_admin && isset($priceItem->id))
                                                            <a href="{{ route('admin.mercuriale.edit', $priceItem->id) }}"
                                                               title="Modifier"
                                                               class="rounded border border-slate-200 bg-white p-1 text-slate-400 hover:text-[#7D5A50] hover:border-[#7D5A50] transition shadow-2xs">
                                                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                                            </a>
                                                        @endif
                                                    @endauth
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Bandeau d'information bas de section -->
                <div class="mt-5 rounded-xl border border-slate-200 bg-white px-4 py-3 text-xs text-slate-600 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shadow-2xs">
                    <div class="flex items-center gap-2">
                        <svg class="h-4 w-4 text-[#7D5A50] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Ces cours indicatifs sont relevés auprès des loumas ruraux et bourses régionales. Les transactions réelles se concluent de gré à gré directement entre producteurs et acheteurs.</span>
                    </div>
                    <a href="{{ route('listings.create') }}" class="inline-flex items-center gap-1 font-bold text-[#7D5A50] hover:underline whitespace-nowrap shrink-0">
                        <span>Publier une offre à ce cours</span>
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Offres d'emploi agricole --}}
    @if (($jobOffers ?? collect())->isNotEmpty())
        <section class="border-b border-slate-200 bg-white py-6">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-700">Offres d'emploi et recrutements agricoles</h2>
                        <p class="text-[11px] text-slate-500">{{ number_format($jobOfferCount ?? 0, 0, ',', ' ') }} postes actuellement ouverts</p>
                    </div>
                    <a href="{{ route('jobs.index') }}" class="text-xs font-semibold text-[#7D5A50] hover:underline">Voir toutes les offres &rarr;</a>
                </div>
                <div class="mt-3 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($jobOffers as $job)
                        <a href="{{ route('jobs.show', $job) }}" class="rounded border border-slate-200 bg-slate-50/50 p-3 transition hover:border-slate-300 hover:bg-white hover:shadow-xs">
                            <div class="flex items-start justify-between gap-2">
                                <h3 class="line-clamp-2 text-xs font-bold text-slate-900 leading-snug">{{ $job->title }}</h3>
                                @if ($job->deadline)
                                    <span class="shrink-0 rounded bg-slate-200 px-1.5 py-0.5 text-[10px] font-semibold text-slate-700">{{ $job->deadline->format('d/m') }}</span>
                                @endif
                            </div>
                            <p class="mt-2 text-xs text-slate-600 truncate">{{ $job->company ?? 'Exploitation agricole' }}</p>
                            <p class="mt-0.5 text-[11px] text-slate-500 truncate">{{ $job->location ?? 'Sénégal' }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- CTA Banner --}}
    <section class="border-b border-slate-200 bg-[#7D5A50] text-white py-7">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row md:items-center md:justify-between gap-5">
            <div>
                <h2 class="text-lg font-bold text-white sm:text-xl">Vous souhaitez vendre du matériel, du bétail ou une récolte ?</h2>
                <p class="mt-1 text-xs sm:text-sm text-white/80 max-w-2xl">Publiez gratuitement votre annonce sur AgriKoom et entrez en relation directe avec les acheteurs et professionnels du Sénégal.</p>
            </div>
            <div class="shrink-0">
                <a href="{{ route('listings.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-white px-6 py-3 text-sm font-bold uppercase tracking-wide text-[#7D5A50] shadow hover:bg-slate-50 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Publier une annonce gratuite
                </a>
            </div>
        </div>
    </section>

    {{-- Listing Search (Livewire) --}}
    <div id="categories" class="bg-slate-50 py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-5 rounded-md border border-[#7D5A50]/20 bg-[#7D5A50]/10 px-4 py-3 text-xs font-semibold text-[#5a3f38]">
                    {{ session('status') }}
                </div>
            @endif

            <livewire:listing-search />
        </div>
    </div>

    {{-- Section Blog & Conseils agricoles (Placée en bas de la page d'accueil) --}}
    @if (($latestArticles ?? collect())->isNotEmpty())
        <section class="border-t border-slate-200 bg-white py-10">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-6">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-[#7D5A50]/10 border border-[#7D5A50]/20 px-3 py-1 text-xs font-bold text-[#7D5A50]">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                Blog AgriKoom
                            </span>
                            <span class="text-xs text-slate-500 font-medium">Conseils &amp; Bonnes pratiques agricoles</span>
                        </div>
                        <h2 class="text-xl font-black text-slate-950 tracking-tight sm:text-2xl">Guides pour réussir vos productions</h2>
                        <p class="mt-1 text-xs text-slate-500 max-w-xl">Articles et retours d'expérience pour développer votre exploitation agricole au Sénégal.</p>
                    </div>
                    <a href="{{ route('blog.index') }}" class="hidden sm:inline-flex items-center gap-1.5 rounded-lg border border-[#7D5A50]/30 bg-[#7D5A50]/5 px-4 py-2 text-xs font-bold text-[#7D5A50] hover:bg-[#7D5A50] hover:text-white transition whitespace-nowrap">
                        Voir tout le blog
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>

                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($latestArticles as $art)
                        <a href="{{ route('blog.show', $art->slug) }}"
                           class="group flex flex-col justify-between overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xs transition hover:border-[#7D5A50]/50 hover:shadow-lg hover:-translate-y-0.5 duration-200">
                            <div>
                                <div class="aspect-[16/9] overflow-hidden bg-slate-100 relative">
                                    <img src="{{ $art->image_url }}" alt="{{ $art->title }}"
                                         class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                                    <div class="absolute top-3 left-3">
                                        <span class="rounded-full bg-[#7D5A50] px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-white shadow">
                                            {{ $art->category }}
                                        </span>
                                    </div>
                                </div>
                                <div class="p-4 sm:p-5">
                                    <div class="flex items-center gap-2 text-[11px] text-slate-400 mb-2">
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>{{ $art->reading_time }} min de lecture</span>
                                        @if ($art->published_at)
                                            <span>&bull;</span>
                                            <span>{{ $art->published_at->format('d M Y') }}</span>
                                        @endif
                                    </div>
                                    <h3 class="text-sm font-extrabold text-slate-950 group-hover:text-[#7D5A50] transition line-clamp-2 leading-snug mb-2">
                                        {{ $art->title }}
                                    </h3>
                                    <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed">
                                        {{ $art->excerpt }}
                                    </p>
                                </div>
                            </div>
                            <div class="px-4 pb-4 sm:px-5 sm:pb-5 flex items-center justify-between">
                                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-[#7D5A50] group-hover:gap-2 transition-all">
                                    Lire l'article
                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>

                {{-- Bouton mobile vers le blog --}}
                <div class="mt-6 text-center sm:hidden">
                    <a href="{{ route('blog.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-[#7D5A50] px-5 py-2.5 text-xs font-bold text-white shadow hover:bg-[#6b4d44] transition">
                        Voir tous les articles
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </section>
    @endif
</x-app-layout>
