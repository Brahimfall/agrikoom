<x-app-layout>
    {{-- ══════════════════════════════════════════════════════════════
         PAGE PRODUITS — AgriKoom
         Même disposition, même rendu et même taille que la page d'accueil
    ══════════════════════════════════════════════════════════════ --}}

    {{-- ── Hero compact ── --}}
    <section class="relative overflow-hidden border-b border-slate-200 bg-gradient-to-br from-[#4a342e] via-[#7D5A50] to-[#5a3f38] py-8 sm:py-10">
        <div class="pointer-events-none absolute inset-0 opacity-10"
             style="background-image: url(\"data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E\");">
        </div>

        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    {{-- Breadcrumb --}}
                    <nav class="mb-2 flex items-center gap-1.5 text-xs text-white/60">
                        <a href="{{ route('home') }}" class="hover:text-white transition">Accueil</a>
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        <span class="font-semibold text-white">Produits agricoles</span>
                    </nav>
                    <h1 class="text-2xl font-black tracking-tight text-white sm:text-3xl">
                        Tous les produits agricoles
                    </h1>
                    <p class="mt-1 text-xs sm:text-sm text-white/75 max-w-xl">
                        Matériel, bétail, céréales, légumes et intrants.
                        <span class="font-bold text-white">{{ number_format($totalProducts, 0, ',', ' ') }} offres actives</span>
                        en direct des producteurs et vendeurs du Sénégal.
                    </p>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('listings.create') }}"
                       class="inline-flex items-center gap-1.5 rounded-lg bg-white px-4 py-2 text-xs font-bold uppercase tracking-wider text-[#7D5A50] shadow hover:bg-slate-50 transition">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        Publier une annonce
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ── Corps principal (Exactement comme sur la page d'accueil) ── --}}
    <div class="bg-slate-50 py-6" x-data="{ viewMode: 'list', mobileFiltersOpen: false }">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-6 lg:grid-cols-[240px_minmax(0,1fr)]">

                {{-- ══════════════════ SIDEBAR FILTRES (Comme sur l'accueil) ══════════════════ --}}
                <aside :class="mobileFiltersOpen ? 'fixed inset-0 z-50 bg-black/50 flex flex-col justify-end p-4' : 'hidden lg:block'"
                       class="space-y-4 lg:sticky lg:top-28 lg:self-start">
                    <section class="rounded-lg border border-slate-200 bg-white p-4 shadow-lg lg:shadow-2xs max-h-[85vh] overflow-y-auto">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800">
                                Filtres de recherche
                            </h2>
                            <div class="flex items-center gap-2">
                                @if (request()->hasAny(['search', 'category', 'location', 'price_min', 'price_max']))
                                    <a href="{{ route('products.index') }}" class="text-xs font-semibold text-[#7D5A50] hover:underline">
                                        Réinitialiser
                                    </a>
                                @endif
                                <button type="button" @click="mobileFiltersOpen = false" class="lg:hidden text-slate-400 hover:text-slate-700 p-1" title="Fermer les filtres">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </div>

                        <form method="GET" action="{{ route('products.index') }}" id="filter-form">
                            {{-- Mot-clé --}}
                            <div class="mt-4">
                                <label for="search-input" class="text-xs font-semibold text-slate-700">Mot-clé</label>
                                <div class="relative mt-1.5">
                                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-2.5 text-slate-400">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    </span>
                                    <input id="search-input" type="search" name="search" value="{{ request('search') }}"
                                           placeholder="Tracteur, riz, bélier..."
                                           class="block w-full rounded border-slate-300 bg-white py-1.5 pl-8 pr-3 text-xs shadow-2xs focus:border-[#7D5A50] focus:ring-1 focus:ring-[#7D5A50]">
                                </div>
                            </div>

                            {{-- Catégorie / Rubrique --}}
                            <div class="mt-4 border-t border-slate-100 pt-3">
                                <label for="filterCategory" class="text-xs font-semibold text-slate-700">Rubrique</label>
                                <select id="filterCategory" name="category" onchange="this.form.submit()"
                                        class="mt-1.5 block w-full rounded border-slate-300 text-xs shadow-2xs focus:border-[#7D5A50] focus:ring-1 focus:ring-[#7D5A50]">
                                    <option value="">Toutes les familles ({{ $totalProducts }})</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->slug }}" @selected(request('category') == $cat->slug || request('category') == (string) $cat->id)>
                                            {{ $cat->name }} ({{ $cat->active_count }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Localisation --}}
                            <div class="mt-4 border-t border-slate-100 pt-3">
                                <label for="filterLocation" class="text-xs font-semibold text-slate-700">Région / Localité</label>
                                <div class="relative mt-1.5">
                                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-2.5 text-slate-400">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </span>
                                    <input id="filterLocation" type="text" name="location" value="{{ request('location') }}"
                                           placeholder="Thiès, Saint-Louis..."
                                           class="block w-full rounded border-slate-300 bg-white py-1.5 pl-8 pr-3 text-xs shadow-2xs focus:border-[#7D5A50] focus:ring-1 focus:ring-[#7D5A50]">
                                </div>
                            </div>

                            {{-- Budget --}}
                            <div class="mt-4 border-t border-slate-100 pt-3">
                                <label class="text-xs font-semibold text-slate-700">Prix (FCFA)</label>
                                <div class="mt-1.5 grid grid-cols-2 gap-2">
                                    <input aria-label="Prix minimum en FCFA" type="number" min="0" name="price_min" value="{{ request('price_min') }}" placeholder="Min"
                                           class="block w-full rounded border-slate-300 text-xs shadow-2xs focus:border-[#7D5A50] focus:ring-1 focus:ring-[#7D5A50]">
                                    <input aria-label="Prix maximum en FCFA" type="number" min="0" name="price_max" value="{{ request('price_max') }}" placeholder="Max"
                                           class="block w-full rounded border-slate-300 text-xs shadow-2xs focus:border-[#7D5A50] focus:ring-1 focus:ring-[#7D5A50]">
                                </div>
                            </div>

                            {{-- Classement --}}
                            <div class="mt-4 border-t border-slate-100 pt-3">
                                <label for="sort" class="text-xs font-semibold text-slate-700">Classement</label>
                                <select id="sort" name="sort" onchange="this.form.submit()"
                                        class="mt-1.5 block w-full rounded border-slate-300 text-xs shadow-2xs focus:border-[#7D5A50] focus:ring-1 focus:ring-[#7D5A50]">
                                    <option value="recent"     {{ request('sort', 'recent') === 'recent'     ? 'selected' : '' }}>Plus récentes en premier</option>
                                    <option value="price_asc"  {{ request('sort') === 'price_asc'            ? 'selected' : '' }}>Prix croissant</option>
                                    <option value="price_desc" {{ request('sort') === 'price_desc'           ? 'selected' : '' }}>Prix décroissant</option>
                                    <option value="alpha"      {{ request('sort') === 'alpha'                ? 'selected' : '' }}>A → Z</option>
                                </select>
                            </div>

                            <div class="mt-4 pt-2 flex flex-col gap-2">
                                <button type="submit" class="w-full rounded bg-[#7D5A50] py-2 text-xs font-bold text-white hover:bg-[#6b4d44] transition shadow-2xs">
                                    Filtrer les produits
                                </button>
                                <a href="{{ route('products.index') }}" class="w-full text-center rounded border border-slate-200 bg-slate-50 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-100">
                                    Effacer les filtres
                                </a>
                            </div>
                        </form>
                    </section>
                </aside>

                {{-- ══════════════════ RÉSULTATS & FICHES PRODUITS (Style Agriaffaires) ══════════════════ --}}
                <section class="min-w-0 space-y-3">

                    {{-- Header des résultats --}}
                    <div class="flex flex-col gap-2 rounded-lg border border-slate-200 bg-white p-3 shadow-2xs sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="text-sm font-bold text-slate-900 sm:text-base">
                                    {{ $products->total() }} produit{{ $products->total() > 1 ? 's' : '' }} trouvé{{ $products->total() > 1 ? 's' : '' }}
                                </h2>
                                @if (!empty($selectedCategory))
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-[#7D5A50]/10 px-3 py-0.5 text-xs font-bold text-[#7D5A50] border border-[#7D5A50]/20">
                                        <span>Catégorie : {{ $selectedCategory->name }}</span>
                                        <a href="{{ route('products.index', request()->except('category')) }}" class="hover:text-red-600 transition font-black ml-0.5 text-sm" title="Retirer ce filtre">&times;</a>
                                    </span>
                                @endif
                            </div>
                            <p class="text-[11px] text-slate-500">Matériel agricole, bétail, récoltes et intrants</p>
                        </div>

                        <div class="flex items-center gap-2 sm:gap-3 justify-between sm:justify-end">
                            {{-- Bouton filtre mobile --}}
                            <button type="button" @click="mobileFiltersOpen = true"
                                    class="flex lg:hidden items-center gap-1.5 rounded border border-slate-300 bg-white px-3 py-1 text-xs font-bold text-slate-800 shadow-2xs hover:bg-slate-50">
                                <svg class="h-3.5 w-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                                <span>Filtres</span>
                                @if (request()->hasAny(['search', 'category', 'location', 'price_min', 'price_max']))
                                    <span class="h-2 w-2 rounded-full bg-[#7D5A50]"></span>
                                @endif
                            </button>

                            {{-- Bascule vue Liste / Grille (Desktop) --}}
                            <div class="hidden sm:flex items-center rounded border border-slate-200 p-0.5 bg-slate-50">
                                <button type="button" @click="viewMode = 'list'"
                                        :class="viewMode === 'list' ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-400 hover:text-slate-700'"
                                        class="rounded p-1" title="Vue Liste (Recommandée)">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                                </button>
                                <button type="button" @click="viewMode = 'grid'"
                                        :class="viewMode === 'grid' ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-400 hover:text-slate-700'"
                                        class="rounded p-1" title="Vue Grille">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Badges des filtres actifs --}}
                    @if (request()->hasAny(['search', 'category', 'location', 'price_min', 'price_max']))
                        <div class="flex flex-wrap gap-1.5">
                            @if (request('search'))
                                <span class="inline-flex items-center gap-1 rounded-full bg-[#7D5A50]/10 border border-[#7D5A50]/20 px-2.5 py-0.5 text-[11px] font-bold text-[#7D5A50]">
                                    Mot-clé : {{ request('search') }}
                                    <a href="{{ request()->fullUrlWithQuery(['search' => null, 'page' => null]) }}" class="hover:text-[#4a342e] ml-0.5">×</a>
                                </span>
                            @endif
                            @if (!empty($selectedCategory))
                                <span class="inline-flex items-center gap-1 rounded-full bg-[#7D5A50]/10 border border-[#7D5A50]/20 px-2.5 py-0.5 text-[11px] font-bold text-[#7D5A50]">
                                    {{ $selectedCategory->name }}
                                    <a href="{{ request()->fullUrlWithQuery(['category' => null, 'page' => null]) }}" class="hover:text-[#4a342e] ml-0.5">×</a>
                                </span>
                            @endif
                            @if (request('location'))
                                <span class="inline-flex items-center gap-1 rounded-full bg-[#7D5A50]/10 border border-[#7D5A50]/20 px-2.5 py-0.5 text-[11px] font-bold text-[#7D5A50]">
                                    📍 {{ request('location') }}
                                    <a href="{{ request()->fullUrlWithQuery(['location' => null, 'page' => null]) }}" class="hover:text-[#4a342e] ml-0.5">×</a>
                                </span>
                            @endif
                            @if (request('price_min') || request('price_max'))
                                <span class="inline-flex items-center gap-1 rounded-full bg-[#7D5A50]/10 border border-[#7D5A50]/20 px-2.5 py-0.5 text-[11px] font-bold text-[#7D5A50]">
                                    💰 {{ request('price_min') ? number_format(request('price_min'),0,',',' ').' FCFA' : '0' }} – {{ request('price_max') ? number_format(request('price_max'),0,',',' ').' FCFA' : '∞' }}
                                    <a href="{{ request()->fullUrlWithQuery(['price_min' => null, 'price_max' => null, 'page' => null]) }}" class="hover:text-[#4a342e] ml-0.5">×</a>
                                </span>
                            @endif
                        </div>
                    @endif

                    {{-- Entête de colonnes pour la vue Liste sur grand écran (Identique à l'accueil) --}}
                    <div x-show="viewMode === 'list'"
                         class="hidden rounded border border-slate-200 bg-slate-100 px-4 py-2 text-[11px] font-bold uppercase tracking-wider text-slate-600 lg:grid lg:grid-cols-[112px_minmax(0,1.3fr)_120px_90px_minmax(110px,0.8fr)_180px] lg:gap-3">
                        <span>Photo</span>
                        <span>Produit / Modèle</span>
                        <span>Prix (FCFA)</span>
                        <span>Quantité</span>
                        <span>Localisation</span>
                        <span>Action</span>
                    </div>

                    @php
                        $categoryFallbacks = [
                            'Cereales'            => 'riz.png',
                            'Legumes'             => 'tomates.png',
                            'Elevage'             => 'elevage.jpg',
                            'Materiel agricole'   => 'materiel.jpg',
                            'Intrants'            => 'intrants.png',
                            'Fruits'              => 'mangues.png',
                            'Legumineuses'        => 'agri-products.png',
                            'Produits horticoles' => 'mangues.png',
                            'Tubercules'          => 'manioc.png',
                        ];
                    @endphp

                    {{-- ── Liste des produits (Format identique à la page d'accueil) ── --}}
                    <div class="space-y-3 sm:space-y-2.5">
                        @forelse ($products as $product)
                            @php
                                $sellerPhone = $product->user->phone ?? '';
                                $phoneDigits = preg_replace('/\D+/', '', $sellerPhone);
                                $telPhone = preg_replace('/\s+/', '', $sellerPhone);
                                $whatsAppMessage = urlencode('Bonjour, je vous contacte depuis AgriKoom pour votre annonce : '.$product->title);
                                $firstPhoto = $product->photos->first();

                                $fallbackFile = $categoryFallbacks[$product->category->name ?? ''] ?? 'agri-products.png';
                                $imgSrc = $firstPhoto ? \Illuminate\Support\Facades\Storage::url($firstPhoto->path) : asset('storage/listings/demo/'.$fallbackFile);
                                $fallbackSrc = asset('storage/listings/demo/'.$fallbackFile);
                            @endphp

                            {{-- ── FORMAT CARTE STYLE AGRIAFAIRES (VUE LISTE PAR DÉFAUT) ── --}}
                            <article x-show="viewMode === 'list'"
                                     class="group rounded-lg border border-slate-200 bg-white shadow-2xs overflow-hidden transition hover:border-slate-300 hover:shadow-sm lg:grid lg:grid-cols-[112px_minmax(0,1.3fr)_120px_90px_minmax(110px,0.8fr)_180px] lg:items-center lg:gap-3 lg:p-3 lg:border-l-3 lg:border-l-[#7D5A50]">

                                {{-- VUE MOBILE (flex row horizontal compact 96px x 72px) --}}
                                <div class="flex flex-row p-2 sm:p-3 gap-2.5 sm:gap-3 lg:hidden items-start">
                                    {{-- Vignette photo gauche compacte (96px x 72px) --}}
                                    <div class="relative shrink-0" style="width: 96px; height: 72px;">
                                        <a href="{{ route('listings.show', $product) }}" class="relative block w-full h-full overflow-hidden rounded bg-slate-100 border border-slate-200">
                                            <img src="{{ $imgSrc }}" alt="{{ $product->title }}"
                                                 onerror="this.onerror=null;this.src='{{ $fallbackSrc }}';"
                                                 class="h-full w-full object-cover">

                                            {{-- Badge nombre de photos compact --}}
                                            <span class="absolute bottom-1 left-1/2 -translate-x-1/2 flex items-center gap-0.5 rounded bg-black/75 px-1 py-0.2 text-[8px] font-bold text-white shadow-xs">
                                                <svg class="h-2 w-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                {{ max(1, $product->photos->count()) }}
                                            </span>
                                        </a>
                                    </div>

                                    {{-- Colonne droite mobile --}}
                                    <div class="flex-1 min-w-0 flex flex-col justify-between">
                                        <div>
                                            <div class="flex items-start justify-between gap-1">
                                                <h3 class="text-xs sm:text-sm font-bold text-slate-900 leading-snug line-clamp-2">
                                                    <a href="{{ route('listings.show', $product) }}" class="hover:text-[#7D5A50] transition">
                                                        {{ $product->title }}
                                                    </a>
                                                </h3>
                                            </div>

                                            <p class="mt-0.5 text-[11px] text-slate-500 font-medium truncate">
                                                {{ $product->product }}
                                                @if ($product->quantity)
                                                    &bull; {{ rtrim(rtrim($product->quantity, '0'), '.').' '.$product->unit }}
                                                @endif
                                                @if ($product->is_negotiable)
                                                    &bull; <span class="text-amber-700">Négociable</span>
                                                @endif
                                            </p>

                                            <p class="text-[11px] font-semibold text-slate-600 truncate">
                                                {{ $product->category->name }}
                                            </p>

                                            {{-- Prix rouge/bordeaux style Agriaffaires --}}
                                            <div class="mt-1">
                                                @if ($product->price)
                                                    <p class="text-xs sm:text-sm font-black text-[#b91c1c]">
                                                        {{ number_format((float) $product->price, 0, ',', ' ') }} FCFA
                                                        @if ($product->unit)
                                                            <span class="text-[10px] font-normal text-slate-500">/ {{ $product->unit }}</span>
                                                        @endif
                                                    </p>
                                                @else
                                                    <p class="text-xs font-bold text-[#b91c1c]">Prix sur demande</p>
                                                @endif
                                            </div>

                                            {{-- Vendeur & Région / Ville --}}
                                            <div class="mt-1 flex items-center gap-1 text-[10px] text-slate-500 truncate">
                                                <svg class="h-3 w-3 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                                <span class="font-semibold text-slate-700 truncate">{{ $product->user->name }}</span>
                                                <span>&bull;</span>
                                                <span class="truncate">{{ $product->location }}</span>
                                            </div>
                                        </div>

                                        {{-- Actions rapides sur mobile --}}
                                        <div class="mt-2 flex items-center gap-1.5 pt-1.5 border-t border-slate-100">
                                            @if ($phoneDigits)
                                                <a href="https://wa.me/{{ $phoneDigits }}?text={{ $whatsAppMessage }}" target="_blank" rel="noopener"
                                                   data-track-listing="{{ $product->id }}" data-track-channel="whatsapp"
                                                   class="flex-1 flex items-center justify-center gap-1.5 rounded-md py-1 text-[11px] font-bold text-white shadow-sm transition hover:opacity-90"
                                                   style="background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);">
                                                    <svg class="h-3.5 w-3.5 shrink-0" viewBox="0 0 175.216 175.552" xmlns="http://www.w3.org/2000/svg" fill="white">
                                                        <path d="M87.44 0C38.986 0 .215 38.683.215 87.03c0 15.323 3.996 30.278 11.602 43.42L.001 175.552l46.3-12.14C59.126 170.68 73.14 174.19 87.44 174.19c48.455 0 87.776-38.683 87.776-87.03C175.216 38.683 135.895 0 87.44 0zm0 160.49c-13.457 0-26.636-3.62-38.07-10.46l-2.73-1.62-28.282 7.414 7.56-27.59-1.774-2.828C17.6 114.218 13.825 100.84 13.825 87.03c0-40.593 33.027-73.62 73.615-73.62 40.59 0 73.616 33.027 73.616 73.62 0 40.59-33.027 73.46-73.616 73.46z"/>
                                                        <path d="M129.08 104.918c-2.21-1.106-13.09-6.456-15.117-7.19-2.028-.737-3.504-1.106-4.98 1.106-1.476 2.21-5.718 7.19-7.011 8.666-1.292 1.476-2.584 1.66-4.797.554-2.21-1.106-9.335-3.44-17.78-10.975-6.572-5.868-11.013-13.113-12.305-15.323-1.292-2.21-.137-3.408 .97-4.51 .993-.993 2.21-2.585 3.317-3.878 1.106-1.292 1.476-2.21 2.21-3.686.737-1.476.37-2.769-.184-3.878-.554-1.106-4.98-12.027-6.826-16.46-1.797-4.325-3.624-3.737-4.98-3.804-1.292-.062-2.769-.075-4.244-.075-1.476 0-3.878.554-5.903 2.769-2.028 2.21-7.747 7.57-7.747 18.49s7.932 21.443 9.038 22.919c1.106 1.476 15.606 23.84 37.825 33.43 5.286 2.284 9.414 3.646 12.63 4.667 5.306 1.687 10.137 1.448 13.955.877 4.257-.636 13.09-5.351 14.937-10.516 1.845-5.165 1.845-9.593 1.291-10.516-.554-.922-2.028-1.476-4.24-2.582z"/>
                                                    </svg>
                                                    <span>WhatsApp</span>
                                                </a>
                                                <a href="tel:{{ $telPhone }}" data-track-listing="{{ $product->id }}" data-track-channel="phone"
                                                   class="flex-1 flex items-center justify-center gap-1 rounded border border-slate-300 bg-white py-1 text-[11px] font-semibold text-slate-800 shadow-2xs hover:bg-slate-50">
                                                    <svg class="h-3 w-3 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                                    <span>Appeler</span>
                                                </a>
                                                <a href="{{ route('listings.show', $product) }}"
                                                   class="rounded border border-slate-200 bg-slate-50 px-2 py-1 text-[11px] font-semibold text-slate-700 hover:bg-slate-100">
                                                    Détails
                                                </a>
                                            @else
                                                <a href="{{ route('listings.show', $product) }}"
                                                   class="flex-1 flex items-center justify-center rounded bg-[#7D5A50] py-1 text-[11px] font-semibold text-white">
                                                    Voir l'annonce &rarr;
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- DESKTOP : Colonne Photo compacte (88px x 112px, style Agriaffaires) --}}
                                <div class="hidden lg:block">
                                    <a href="{{ route('listings.show', $product) }}" class="relative block h-22 w-28 overflow-hidden rounded bg-slate-100 border border-slate-200 shrink-0">
                                        <img src="{{ $imgSrc }}" alt="{{ $product->title }}"
                                             onerror="this.onerror=null;this.src='{{ $fallbackSrc }}';"
                                             class="h-full w-full object-cover transition duration-150 group-hover:scale-105">

                                        @if ($product->photos->count() > 1)
                                            <span class="absolute bottom-1 right-1 rounded bg-black/75 px-1 py-0.5 text-[9px] font-semibold text-white">
                                                {{ $product->photos->count() }} photos
                                            </span>
                                        @endif
                                    </a>
                                </div>

                                {{-- DESKTOP : Colonne Titre & Infos --}}
                                <div class="hidden lg:block min-w-0">
                                    <div class="flex items-center gap-2 text-xs">
                                        <span class="font-bold text-[#7D5A50]">{{ $product->category->name }}</span>
                                        <span class="text-slate-300">|</span>
                                        <span class="font-semibold text-slate-500">Offre</span>
                                    </div>
                                    <h3 class="mt-0.5 text-sm font-bold text-slate-900 leading-snug">
                                        <a href="{{ route('listings.show', $product) }}" class="hover:text-[#7D5A50] transition">
                                            {{ $product->title }}
                                        </a>
                                    </h3>
                                    <p class="mt-0.5 text-xs text-slate-500 flex items-center gap-1.5 flex-wrap">
                                        <span>Produit : <strong class="text-slate-700">{{ $product->product }}</strong></span>
                                        @if ($product->is_available_immediately)
                                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Immédiat
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-[11px] font-medium text-amber-700 bg-amber-50 px-1.5 py-0.5 rounded">
                                                Dispo {{ $product->available_from->format('d/m/Y') }}
                                            </span>
                                        @endif
                                    </p>
                                </div>

                                {{-- DESKTOP : Colonne Prix --}}
                                <div class="hidden lg:block">
                                    @if ($product->price)
                                        <p class="text-sm font-bold text-[#b91c1c]">
                                            {{ number_format((float) $product->price, 0, ',', ' ') }} <span class="text-xs text-slate-600 font-normal">FCFA</span>
                                            @if ($product->unit)
                                                <span class="block text-[10px] font-normal text-slate-500">/ {{ $product->unit }}</span>
                                            @endif
                                        </p>
                                    @else
                                        <span class="text-xs font-bold text-[#b91c1c]">Prix sur demande</span>
                                    @endif
                                </div>

                                {{-- DESKTOP : Colonne Quantité --}}
                                <div class="hidden lg:block">
                                    <p class="text-xs font-semibold text-slate-800">
                                        {{ $product->quantity ? rtrim(rtrim($product->quantity, '0'), '.').' '.$product->unit : '-' }}
                                    </p>
                                </div>

                                {{-- DESKTOP : Colonne Localisation & Vendeur --}}
                                <div class="hidden lg:block min-w-0">
                                    <p class="flex items-center gap-1 text-xs font-semibold text-slate-800">
                                        <svg class="h-3 w-3 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        <span>{{ $product->location }}</span>
                                    </p>
                                    <p class="truncate text-[11px] text-slate-500 mt-0.5">
                                        {{ $product->user->name }}
                                    </p>
                                </div>

                                {{-- DESKTOP : Colonne Actions (WhatsApp, Appel, Détails) --}}
                                <div class="hidden lg:block">
                                    @if ($phoneDigits)
                                        <div class="grid grid-cols-2 gap-1.5 sm:grid-cols-3">
                                            <a href="https://wa.me/{{ $phoneDigits }}?text={{ $whatsAppMessage }}" target="_blank" rel="noopener"
                                               data-track-listing="{{ $product->id }}" data-track-channel="whatsapp"
                                               class="flex items-center justify-center gap-1 rounded bg-[#25D366] px-2 py-1.5 text-xs font-bold text-white transition hover:bg-[#20ba59]"
                                               style="background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);" title="WhatsApp">
                                                <svg class="h-3.5 w-3.5 shrink-0" viewBox="0 0 175.216 175.552" xmlns="http://www.w3.org/2000/svg" fill="white">
                                                    <path d="M87.44 0C38.986 0 .215 38.683.215 87.03c0 15.323 3.996 30.278 11.602 43.42L.001 175.552l46.3-12.14C59.126 170.68 73.14 174.19 87.44 174.19c48.455 0 87.776-38.683 87.776-87.03C175.216 38.683 135.895 0 87.44 0zm0 160.49c-13.457 0-26.636-3.62-38.07-10.46l-2.73-1.62-28.282 7.414 7.56-27.59-1.774-2.828C17.6 114.218 13.825 100.84 13.825 87.03c0-40.593 33.027-73.62 73.615-73.62 40.59 0 73.616 33.027 73.616 73.62 0 40.59-33.027 73.46-73.616 73.46z"/>
                                                    <path d="M129.08 104.918c-2.21-1.106-13.09-6.456-15.117-7.19-2.028-.737-3.504-1.106-4.98 1.106-1.476 2.21-5.718 7.19-7.011 8.666-1.292 1.476-2.584 1.66-4.797.554-2.21-1.106-9.335-3.44-17.78-10.975-6.572-5.868-11.013-13.113-12.305-15.323-1.292-2.21-.137-3.408 .97-4.51 .993-.993 2.21-2.585 3.317-3.878 1.106-1.292 1.476-2.21 2.21-3.686.737-1.476.37-2.769-.184-3.878-.554-1.106-4.98-12.027-6.826-16.46-1.797-4.325-3.624-3.737-4.98-3.804-1.292-.062-2.769-.075-4.244-.075-1.476 0-3.878.554-5.903 2.769-2.028 2.21-7.747 7.57-7.747 18.49s7.932 21.443 9.038 22.919c1.106 1.476 15.606 23.84 37.825 33.43 5.286 2.284 9.414 3.646 12.63 4.667 5.306 1.687 10.137 1.448 13.955.877 4.257-.636 13.09-5.351 14.937-10.516 1.845-5.165 1.845-9.593 1.291-10.516-.554-.922-2.028-1.476-4.24-2.582z"/>
                                                </svg>
                                                <span>WA</span>
                                            </a>
                                            <a href="tel:{{ $telPhone }}" data-track-listing="{{ $product->id }}" data-track-channel="phone"
                                               class="flex items-center justify-center gap-1 rounded border border-slate-300 bg-white px-2 py-1.5 text-xs font-semibold text-slate-800 transition hover:bg-slate-50" title="Appeler">
                                                <svg class="h-3 w-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                                <span>Appel</span>
                                            </a>
                                            <a href="{{ route('listings.show', $product) }}"
                                               class="col-span-2 sm:col-span-1 flex items-center justify-center rounded border border-slate-300 bg-slate-50 px-2 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                                                Détails
                                            </a>
                                        </div>
                                    @else
                                        <a href="{{ route('listings.show', $product) }}"
                                           class="flex w-full items-center justify-center rounded bg-[#7D5A50] py-1.5 text-xs font-semibold text-white hover:bg-[#6b4d44]">
                                            Consulter
                                        </a>
                                    @endif
                                </div>
                            </article>

                            {{-- ── FORMAT GRILLE (Agriaffaires) ── --}}
                            <article x-show="viewMode === 'grid'"
                                     class="group rounded-lg border border-slate-200 bg-white shadow-2xs overflow-hidden transition hover:border-slate-300">
                                <div class="relative h-44 sm:h-48 w-full bg-slate-100 overflow-hidden">
                                    <img src="{{ $imgSrc }}" alt="{{ $product->title }}"
                                         onerror="this.onerror=null;this.src='{{ $fallbackSrc }}';"
                                         class="h-full w-full object-cover transition duration-150 group-hover:scale-102">
                                    <span class="absolute left-2.5 top-2.5 rounded px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider text-white bg-[#7D5A50]">
                                        Offre
                                    </span>
                                    @if ($product->photos->count() > 1)
                                        <span class="absolute right-2.5 top-2.5 rounded bg-black/75 px-1.5 py-0.5 text-[9px] font-semibold text-white backdrop-blur-xs">
                                            {{ $product->photos->count() }} photos
                                        </span>
                                    @endif
                                </div>
                                <div class="p-3.5">
                                    <div class="flex items-center justify-between text-xs text-slate-500">
                                        <span class="font-bold text-[#7D5A50]">{{ $product->category->name }}</span>
                                        <span class="flex items-center gap-1 font-medium">
                                            <svg class="h-3 w-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            {{ $product->location }}
                                        </span>
                                    </div>
                                    <h3 class="mt-1 text-sm font-bold text-slate-900 line-clamp-1">
                                        <a href="{{ route('listings.show', $product) }}" class="hover:text-[#7D5A50]">
                                            {{ $product->title }}
                                        </a>
                                    </h3>
                                    <p class="mt-2 text-base font-bold text-slate-900">
                                        @if ($product->price)
                                            {{ number_format((float) $product->price, 0, ',', ' ') }} <span class="text-xs text-[#7D5A50]">FCFA</span>
                                        @else
                                            <span class="text-xs text-slate-500 font-semibold">Prix à débattre</span>
                                        @endif
                                    </p>
                                    <div class="mt-3 flex items-center justify-between border-t border-slate-100 pt-2.5 text-xs">
                                        <span class="text-[11px] text-slate-500 truncate">{{ $product->user->name }}</span>
                                        <a href="{{ route('listings.show', $product) }}" class="font-bold text-[#7D5A50] hover:underline">
                                            Voir détails &rarr;
                                        </a>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="rounded-lg border border-slate-200 bg-white p-10 text-center">
                                <p class="text-sm font-bold text-slate-900">Aucun produit ne correspond à votre recherche</p>
                                <p class="mt-1 text-xs text-slate-500">Modifiez vos mots-clés ou élargissez la zone géographique.</p>
                                <a href="{{ route('products.index') }}" class="mt-3 inline-block rounded bg-[#7D5A50] px-3.5 py-1.5 text-xs font-semibold text-white hover:bg-[#6b4d44]">
                                    Effacer les filtres
                                </a>
                            </div>
                        @endforelse
                    </div>

                    {{-- Pagination --}}
                    <div class="pt-3">
                        {{ $products->links() }}
                    </div>

                </section>
            </div>
        </div>
    </div>

    {{-- CTA Bas de page --}}
    <section class="border-t border-[#5a3f38] bg-[#7D5A50] text-white py-8">
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

</x-app-layout>
