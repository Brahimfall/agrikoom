<?php

use App\Models\Category;
use App\Models\Listing;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public string $search = '';
    public string $type = '';
    public string $category = '';
    public string $location = '';
    public string $minPrice = '';
    public string $maxPrice = '';
    public string $sort = 'newest';
    public string $segment = 'for_you';
    public bool $availableNow = false;

    public function mount(): void
    {
        $this->search = (string) request('search', '');
        $this->type = in_array(request('type'), ['offer', 'request'], true) ? (string) request('type') : '';
        $this->location = (string) request('location', '');
        $this->availableNow = request('available_now') === '1';
        $requestedCategory = (string) request('category', '');

        if ($requestedCategory !== '' && Category::query()->whereKey($requestedCategory)->exists()) {
            $this->category = $requestedCategory;
        }
    }

    public function updated(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'type', 'category', 'location', 'minPrice', 'maxPrice', 'sort', 'segment', 'availableNow']);
        $this->sort = 'newest';
        $this->segment = 'for_you';
        $this->resetPage();
    }

    public function chooseSegment(string $segment): void
    {
        $this->segment = $segment;
        $this->sort = $segment === 'trending' ? 'trending' : 'newest';
        $this->resetPage();
    }

    public function render()
    {
        $query = Listing::query()
            ->active()
            ->with(['category', 'photos', 'user'])
            ->withCount('contactRequests')
            ->latest();

        $query->when($this->search, function ($query) {
            $term = '%'.$this->search.'%';

            $query->where(function ($query) use ($term) {
                $query->where('title', 'like', $term)
                    ->orWhere('product', 'like', $term)
                    ->orWhere('description', 'like', $term);
            });
        });

        $query->when($this->type, fn ($query) => $query->where('type', $this->type));
        $query->when($this->category, fn ($query) => $query->where('category_id', $this->category));
        $query->when($this->location, fn ($query) => $query->where('location', 'like', '%'.$this->location.'%'));
        $query->when($this->minPrice !== '', fn ($query) => $query->where('price', '>=', (float) $this->minPrice));
        $query->when($this->maxPrice !== '', fn ($query) => $query->where('price', '<=', (float) $this->maxPrice));
        $query->when($this->availableNow, function ($query) {
            $query->where(function ($query) {
                $query->whereNull('available_from')->orWhere('available_from', '<=', now()->toDateString());
            })->where(function ($query) {
                $query->whereNull('quantity')->orWhere('quantity', '>', 0);
            });
        });

        match ($this->sort) {
            'price_asc' => $query->reorder()->orderByRaw('price is null')->orderBy('price'),
            'price_desc' => $query->reorder()->orderByDesc('price'),
            'trending' => $query->reorder()->orderByDesc('contact_requests_count')->latest(),
            default => $query->reorder()->latest(),
        };

        return $this->view([
            'categories' => Category::query()
                ->where('is_active', true)
                ->withCount(['listings as active_listings_count' => fn ($query) => $query->active()])
                ->orderBy('name')
                ->get(),
            'listings' => $query->paginate(15),
        ]);
    }
};
?>

<div class="space-y-4" x-data="{ viewMode: 'list', mobileFiltersOpen: false }">
    <div class="grid gap-6 lg:grid-cols-[240px_minmax(0,1fr)]">
        <!-- Sidebar Filters (Drawer on mobile, static on desktop) -->
        <aside :class="mobileFiltersOpen ? 'fixed inset-0 z-50 bg-black/50 flex flex-col justify-end p-4' : 'hidden lg:block'" class="space-y-4 lg:sticky lg:top-20 lg:self-start">
            <section class="rounded-lg border border-slate-200 bg-white p-4 shadow-lg lg:shadow-2xs max-h-[85vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800">
                        Filtres de recherche
                    </h2>
                    <div class="flex items-center gap-2">
                        @if ($search || $type || $category || $location || $minPrice || $maxPrice || $availableNow)
                            <button type="button" wire:click="resetFilters" class="text-xs font-semibold text-[#7D5A50] hover:underline">
                                Réinitialiser
                            </button>
                        @endif
                        <button type="button" @click="mobileFiltersOpen = false" class="lg:hidden text-slate-400 hover:text-slate-700 p-1" title="Fermer les filtres">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>

                <!-- Type d'offre -->
                <div class="mt-4">
                    <label class="text-xs font-semibold text-slate-700">Type d'offre</label>
                    <div class="mt-1.5 grid grid-cols-3 gap-1 rounded border border-slate-200 bg-slate-50 p-1 text-xs font-semibold text-slate-600">
                        <button type="button" wire:click="$set('type', '')" class="rounded py-1 {{ $type === '' ? 'bg-white text-slate-900 shadow-2xs font-bold' : 'hover:bg-white/60' }}">Toutes</button>
                        <button type="button" wire:click="$set('type', 'offer')" class="rounded py-1 {{ $type === 'offer' ? 'bg-[#7D5A50] text-white shadow-2xs font-bold' : 'hover:bg-white/60' }}">Offres</button>
                        <button type="button" wire:click="$set('type', 'request')" class="rounded py-1 {{ $type === 'request' ? 'bg-amber-600 text-white shadow-2xs font-bold' : 'hover:bg-white/60' }}">Demandes</button>
                    </div>
                </div>

                <!-- Disponibilité immédiate -->
                <div class="mt-4">
                    <label for="availableNow" class="flex cursor-pointer items-center gap-2 rounded border border-slate-200 bg-slate-50 p-2 text-xs font-medium text-slate-800 hover:bg-slate-100">
                        <input id="availableNow" type="checkbox" wire:model.live="availableNow" class="rounded border-slate-300 text-[#7D5A50] focus:ring-[#7D5A50]">
                        <span>Disponible de suite</span>
                    </label>
                </div>

                <!-- Catégorie -->
                <div class="mt-4 border-t border-slate-100 pt-3">
                    <label for="filterCategory" class="text-xs font-semibold text-slate-700">Rubrique</label>
                    <select id="filterCategory" wire:model.live="category" class="mt-1.5 block w-full rounded border-slate-300 text-xs shadow-2xs focus:border-[#7D5A50] focus:ring-1 focus:ring-[#7D5A50]">
                        <option value="">Toutes les familles ({{ $categories->sum('active_listings_count') }})</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }} ({{ $cat->active_listings_count }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Budget -->
                <div class="mt-4 border-t border-slate-100 pt-3">
                    <label class="text-xs font-semibold text-slate-700">Prix (FCFA)</label>
                    <div class="mt-1.5 grid grid-cols-2 gap-2">
                        <input aria-label="Prix minimum en FCFA" type="number" min="0" wire:model.live.debounce.350ms="minPrice" placeholder="Min"
                            class="block w-full rounded border-slate-300 text-xs shadow-2xs focus:border-[#7D5A50] focus:ring-1 focus:ring-[#7D5A50]">
                        <input aria-label="Prix maximum en FCFA" type="number" min="0" wire:model.live.debounce.350ms="maxPrice" placeholder="Max"
                            class="block w-full rounded border-slate-300 text-xs shadow-2xs focus:border-[#7D5A50] focus:ring-1 focus:ring-[#7D5A50]">
                    </div>
                </div>

                <!-- Tri -->
                <div class="mt-4 border-t border-slate-100 pt-3">
                    <label for="sort" class="text-xs font-semibold text-slate-700">Classement</label>
                    <select id="sort" wire:model.live="sort" class="mt-1.5 block w-full rounded border-slate-300 text-xs shadow-2xs focus:border-[#7D5A50] focus:ring-1 focus:ring-[#7D5A50]">
                        <option value="newest">Plus récentes en premier</option>
                        <option value="price_asc">Prix croissant</option>
                        <option value="price_desc">Prix décroissant</option>
                        <option value="trending">Plus consultées</option>
                    </select>
                </div>

                <div class="mt-4 pt-2">
                    <button type="button" wire:click="resetFilters" class="w-full rounded border border-slate-200 bg-slate-50 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-100">
                        Effacer les filtres
                    </button>
                </div>
            </section>
        </aside>

        <!-- Résultats & Fiches d'annonces -->
        <section class="min-w-0 space-y-3">
            <!-- Header des résultats -->
            <div class="flex flex-col gap-2 rounded-lg border border-slate-200 bg-white p-3 shadow-2xs sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 sm:text-base">
                        {{ $listings->total() }} annonce{{ $listings->total() > 1 ? 's' : '' }} trouvée{{ $listings->total() > 1 ? 's' : '' }}
                    </h2>
                    <p class="text-[11px] text-slate-500">Matériel agricole, bétail et récoltes</p>
                </div>

                <div class="flex items-center gap-2 sm:gap-3 justify-between sm:justify-end">
                    <button type="button" @click="mobileFiltersOpen = true"
                        class="flex lg:hidden items-center gap-1.5 rounded border border-slate-300 bg-white px-3 py-1 text-xs font-bold text-slate-800 shadow-2xs hover:bg-slate-50">
                        <svg class="h-3.5 w-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        <span>Filtres</span>
                        @if ($search || $type || $category || $location || $minPrice || $maxPrice || $availableNow)
                            <span class="h-2 w-2 rounded-full bg-[#7D5A50]"></span>
                        @endif
                    </button>

                    <div class="grid grid-cols-3 gap-1 rounded bg-slate-100 p-0.5 text-xs font-semibold text-slate-600">
                        <button type="button" wire:click="chooseSegment('for_you')" class="rounded px-2 sm:px-2.5 py-1 {{ $segment === 'for_you' ? 'bg-white text-slate-900 shadow-2xs font-bold' : 'hover:bg-white/60' }}">Toutes</button>
                        <button type="button" wire:click="chooseSegment('new')" class="rounded px-2 sm:px-2.5 py-1 {{ $segment === 'new' ? 'bg-white text-slate-900 shadow-2xs font-bold' : 'hover:bg-white/60' }}">Récentes</button>
                        <button type="button" wire:click="chooseSegment('trending')" class="rounded px-2 sm:px-2.5 py-1 {{ $segment === 'trending' ? 'bg-white text-slate-900 shadow-2xs font-bold' : 'hover:bg-white/60' }}">Top</button>
                    </div>

                    <!-- Bascule vue Liste / Grille (Desktop) -->
                    <div class="hidden sm:flex items-center rounded border border-slate-200 p-0.5 bg-slate-50">
                        <button type="button" @click="viewMode = 'list'" :class="viewMode === 'list' ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-400 hover:text-slate-700'" class="rounded p-1" title="Vue Liste">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                        </button>
                        <button type="button" @click="viewMode = 'grid'" :class="viewMode === 'grid' ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-400 hover:text-slate-700'" class="rounded p-1" title="Vue Grille">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Entête de colonnes pour la vue Liste sur grand écran -->
            <div x-show="viewMode === 'list'" class="hidden rounded border border-slate-200 bg-slate-100 px-4 py-2 text-[11px] font-bold uppercase tracking-wider text-slate-600 lg:grid lg:grid-cols-[112px_minmax(0,1.3fr)_120px_90px_minmax(110px,0.8fr)_180px] lg:gap-3">
                <span>Photo</span>
                <span>Annonce / Modèle</span>
                <span>Prix (FCFA)</span>
                <span>Quantité</span>
                <span>Localisation</span>
                <span>Action</span>
            </div>

            @php
                $categoryFallbacks = [
                    'Cereales' => 'riz.png',
                    'Legumes' => 'tomates.png',
                    'Elevage' => 'elevage.jpg',
                    'Materiel agricole' => 'materiel.jpg',
                    'Intrants' => 'intrants.png',
                    'Fruits' => 'mangues.png',
                    'Legumineuses' => 'agri-products.png',
                    'Produits horticoles' => 'mangues.png',
                    'Tubercules' => 'manioc.png',
                ];
            @endphp

            <!-- Liste des annonces -->
            <div class="space-y-3 sm:space-y-2.5">
                @forelse ($listings as $listing)
                    @php
                        $sellerPhone = $listing->user->phone ?? '';
                        $phoneDigits = preg_replace('/\D+/', '', $sellerPhone);
                        $telPhone = preg_replace('/\s+/', '', $sellerPhone);
                        $whatsAppMessage = urlencode('Bonjour, je vous contacte depuis AgriKoom pour votre annonce : '.$listing->title);
                        $firstPhoto = $listing->photos->first();

                        $fallbackFile = $categoryFallbacks[$listing->category->name ?? ''] ?? 'agri-products.png';
                        $imgSrc = $firstPhoto ? Storage::url($firstPhoto->path) : asset('storage/listings/demo/'.$fallbackFile);
                        $fallbackSrc = asset('storage/listings/demo/'.$fallbackFile);
                    @endphp

                    <!-- FORMAT CARTE STYLE AGRIAFAIRES : VUE HORIZONTALE COMPACTE SUR MOBILE, LIGNE SUR DESKTOP -->
                    <article x-show="viewMode === 'list'" class="group rounded-lg border border-slate-200 bg-white shadow-2xs overflow-hidden transition hover:border-slate-300 hover:shadow-sm lg:grid lg:grid-cols-[112px_minmax(0,1.3fr)_120px_90px_minmax(110px,0.8fr)_180px] lg:items-center lg:gap-3 lg:p-3 {{ $listing->type === 'offer' ? 'lg:border-l-3 lg:border-l-[#7D5A50]' : 'lg:border-l-3 lg:border-l-amber-600' }}">
                        
                        <!-- VUE MOBILE (flex row horizontal ultra-compact style Agriaffaires) -->
                        <div class="flex flex-row p-2 sm:p-3 gap-2.5 sm:gap-3 lg:hidden items-start">
                            <!-- Vignette photo gauche compacte (style Agriaffaires 96px x 72px) -->
                            <div class="relative shrink-0" style="width: 96px; height: 72px;">
                                <a href="{{ route('listings.show', $listing) }}" class="relative block w-full h-full overflow-hidden rounded bg-slate-100 border border-slate-200">
                                    <img src="{{ $imgSrc }}" alt="{{ $listing->title }}"
                                        onerror="this.onerror=null;this.src='{{ $fallbackSrc }}';"
                                        class="h-full w-full object-cover">
                                    
                                    <!-- Badge nombre de photos compact (comme sur la capture Agriaffaires) -->
                                    <span class="absolute bottom-1 left-1/2 -translate-x-1/2 flex items-center gap-0.5 rounded bg-black/75 px-1 py-0.2 text-[8px] font-bold text-white shadow-xs">
                                        <svg class="h-2 w-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        {{ max(1, $listing->photos->count()) }}
                                    </span>
                                </a>
                            </div>

                            <!-- Colonne droite : Titre, Caractéristiques, Catégorie, Prix rouge, Vendeur/Lieu, Actions -->
                            <div class="flex-1 min-w-0 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-start justify-between gap-1">
                                        <h3 class="text-xs sm:text-sm font-bold text-slate-900 leading-snug line-clamp-2">
                                            <a href="{{ route('listings.show', $listing) }}" class="hover:text-[#7D5A50] transition">
                                                {{ $listing->title }}
                                            </a>
                                        </h3>
                                        <button type="button" aria-label="Favoris" class="text-slate-300 hover:text-red-500 shrink-0 p-0.5 transition">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                        </button>
                                    </div>

                                    <!-- Détails / Caractéristiques (ex: 2021 - 709 h - 9,30 m style Agriaffaires) -->
                                    <p class="mt-0.5 text-[11px] text-slate-500 font-medium truncate">
                                        {{ $listing->product }}
                                        @if ($listing->quantity)
                                            • {{ rtrim(rtrim($listing->quantity, '0'), '.').' '.$listing->unit }}
                                        @endif
                                        @if ($listing->is_negotiable)
                                            • <span class="text-amber-700">Négociable</span>
                                        @endif
                                    </p>

                                    <!-- Catégorie -->
                                    <p class="text-[11px] font-semibold text-slate-600 truncate">
                                        {{ $listing->category->name }}
                                    </p>

                                    <!-- Prix en rouge/bordeaux style Agriaffaires -->
                                    <div class="mt-1">
                                        @if ($listing->price)
                                            <p class="text-xs sm:text-sm font-black text-[#b91c1c]">
                                                {{ number_format((float) $listing->price, 0, ',', ' ') }} FCFA
                                                @if ($listing->unit)
                                                    <span class="text-[10px] font-normal text-slate-500">/ {{ $listing->unit }}</span>
                                                @endif
                                            </p>
                                        @else
                                            <p class="text-xs font-bold text-[#b91c1c]">Prix sur demande</p>
                                        @endif
                                    </div>

                                    <!-- Vendeur & Région / Ville -->
                                    <div class="mt-1 flex items-center gap-1 text-[10px] text-slate-500 truncate">
                                        <svg class="h-3 w-3 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                        <span class="font-semibold text-slate-700 truncate">{{ $listing->user->name }}</span>
                                        <span>•</span>
                                        <span class="truncate">{{ $listing->location }}</span>
                                    </div>
                                </div>

                                <!-- Actions rapides sur mobile -->
                                <div class="mt-2 flex items-center gap-1.5 pt-1.5 border-t border-slate-100">
                                    @if ($phoneDigits)
                                        <a href="https://wa.me/{{ $phoneDigits }}?text={{ $whatsAppMessage }}" target="_blank" rel="noopener" data-track-listing="{{ $listing->id }}" data-track-channel="whatsapp"
                                            class="flex-1 flex items-center justify-center gap-1.5 rounded-md py-1 text-[11px] font-bold text-white shadow-sm transition hover:opacity-90" style="background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);">
                                            <svg class="h-3.5 w-3.5 shrink-0" viewBox="0 0 175.216 175.552" xmlns="http://www.w3.org/2000/svg" fill="white"><path d="M87.44 0C38.986 0 .215 38.683.215 87.03c0 15.323 3.996 30.278 11.602 43.42L.001 175.552l46.3-12.14C59.126 170.68 73.14 174.19 87.44 174.19c48.455 0 87.776-38.683 87.776-87.03C175.216 38.683 135.895 0 87.44 0zm0 160.49c-13.457 0-26.636-3.62-38.07-10.46l-2.73-1.62-28.282 7.414 7.56-27.59-1.774-2.828C17.6 114.218 13.825 100.84 13.825 87.03c0-40.593 33.027-73.62 73.615-73.62 40.59 0 73.616 33.027 73.616 73.62 0 40.59-33.027 73.46-73.616 73.46z"/><path d="M129.08 104.918c-2.21-1.106-13.09-6.456-15.117-7.19-2.028-.737-3.504-1.106-4.98 1.106-1.476 2.21-5.718 7.19-7.011 8.666-1.292 1.476-2.584 1.66-4.797.554-2.21-1.106-9.335-3.44-17.78-10.975-6.572-5.868-11.013-13.113-12.305-15.323-1.292-2.21-.137-3.408 .97-4.51 .993-.993 2.21-2.585 3.317-3.878 1.106-1.292 1.476-2.21 2.21-3.686.737-1.476.37-2.769-.184-3.878-.554-1.106-4.98-12.027-6.826-16.46-1.797-4.325-3.624-3.737-4.98-3.804-1.292-.062-2.769-.075-4.244-.075-1.476 0-3.878.554-5.903 2.769-2.028 2.21-7.747 7.57-7.747 18.49s7.932 21.443 9.038 22.919c1.106 1.476 15.606 23.84 37.825 33.43 5.286 2.284 9.414 3.646 12.63 4.667 5.306 1.687 10.137 1.448 13.955.877 4.257-.636 13.09-5.351 14.937-10.516 1.845-5.165 1.845-9.593 1.291-10.516-.554-.922-2.028-1.476-4.24-2.582z"/></svg>
                                            <span>WhatsApp</span>
                                        </a>
                                        <a href="tel:{{ $telPhone }}" data-track-listing="{{ $listing->id }}" data-track-channel="phone"
                                            class="flex-1 flex items-center justify-center gap-1 rounded border border-slate-300 bg-white py-1 text-[11px] font-semibold text-slate-800 shadow-2xs hover:bg-slate-50">
                                            <svg class="h-3 w-3 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                            <span>Appeler</span>
                                        </a>
                                        <a href="{{ route('listings.show', $listing) }}"
                                            class="rounded border border-slate-200 bg-slate-50 px-2 py-1 text-[11px] font-semibold text-slate-700 hover:bg-slate-100">
                                            Détails
                                        </a>
                                    @else
                                        <a href="{{ route('listings.show', $listing) }}"
                                            class="flex-1 flex items-center justify-center rounded bg-[#7D5A50] py-1 text-[11px] font-semibold text-white">
                                            Voir l'annonce &rarr;
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- DESKTOP : Colonne Photo (lg) -->
                        <div class="hidden lg:block">
                            <a href="{{ route('listings.show', $listing) }}" class="relative block h-22 w-28 overflow-hidden rounded bg-slate-100 border border-slate-200 shrink-0">
                                <img src="{{ $imgSrc }}" alt="{{ $listing->title }}"
                                    onerror="this.onerror=null;this.src='{{ $fallbackSrc }}';"
                                    class="h-full w-full object-cover transition duration-150 group-hover:scale-105">
                                
                                @if ($listing->photos->count() > 1)
                                    <span class="absolute bottom-1 right-1 rounded bg-black/75 px-1 py-0.5 text-[9px] font-semibold text-white">
                                        {{ $listing->photos->count() }} photos
                                    </span>
                                @endif
                            </a>
                        </div>

                        <!-- DESKTOP : Colonne Titre & Infos -->
                        <div class="hidden lg:block min-w-0">
                            <div class="flex items-center gap-2 text-xs">
                                <span class="font-bold text-[#7D5A50]">{{ $listing->category->name }}</span>
                                <span class="text-slate-300">•</span>
                                <span class="font-semibold text-slate-500">{{ $listing->type === 'offer' ? 'Offre' : 'Demande' }}</span>
                            </div>
                            <h3 class="mt-0.5 text-sm font-bold text-slate-900 leading-snug">
                                <a href="{{ route('listings.show', $listing) }}" class="hover:text-[#7D5A50] transition">
                                    {{ $listing->title }}
                                </a>
                            </h3>
                            <p class="mt-0.5 text-xs text-slate-500 flex items-center gap-1.5 flex-wrap">
                                <span>Produit : <strong class="text-slate-700">{{ $listing->product }}</strong></span>
                                @if ($listing->is_available_immediately)
                                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Immédiat
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[11px] font-medium text-amber-700 bg-amber-50 px-1.5 py-0.5 rounded">
                                        Dispo {{ $listing->available_from->format('d/m/Y') }}
                                    </span>
                                @endif
                            </p>
                        </div>

                        <!-- DESKTOP : Colonne Prix -->
                        <div class="hidden lg:block">
                            @if ($listing->price)
                                <p class="text-sm font-bold text-[#b91c1c]">
                                    {{ number_format((float) $listing->price, 0, ',', ' ') }} <span class="text-xs text-slate-600 font-normal">FCFA</span>
                                    @if ($listing->unit)
                                        <span class="block text-[10px] font-normal text-slate-500">/ {{ $listing->unit }}</span>
                                    @endif
                                </p>
                            @else
                                <span class="text-xs font-bold text-[#b91c1c]">Prix sur demande</span>
                            @endif
                        </div>

                        <!-- DESKTOP : Colonne Quantité -->
                        <div class="hidden lg:block">
                            <p class="text-xs font-semibold text-slate-800">
                                {{ $listing->quantity ? rtrim(rtrim($listing->quantity, '0'), '.').' '.$listing->unit : '—' }}
                            </p>
                        </div>

                        <!-- DESKTOP : Colonne Localisation & Vendeur -->
                        <div class="hidden lg:block min-w-0">
                            <p class="flex items-center gap-1 text-xs font-semibold text-slate-800">
                                <svg class="h-3 w-3 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span>{{ $listing->location }}</span>
                            </p>
                            <p class="truncate text-[11px] text-slate-500 mt-0.5">
                                {{ $listing->user->name }}
                            </p>
                        </div>

                        <!-- DESKTOP : Colonne Actions -->
                        <div class="hidden lg:block">
                            @if ($phoneDigits)
                                <div class="grid grid-cols-2 gap-1.5 sm:grid-cols-3">
                                    <a href="https://wa.me/{{ $phoneDigits }}?text={{ $whatsAppMessage }}" target="_blank" rel="noopener" data-track-listing="{{ $listing->id }}" data-track-channel="whatsapp"
                                        class="flex items-center justify-center gap-1 rounded bg-[#25D366] px-2 py-1.5 text-xs font-bold text-slate-900 transition hover:bg-[#20ba59]" title="WhatsApp">
                                        <svg class="h-3.5 w-3.5 shrink-0" viewBox="0 0 175.216 175.552" xmlns="http://www.w3.org/2000/svg" fill="white"><path d="M87.44 0C38.986 0 .215 38.683.215 87.03c0 15.323 3.996 30.278 11.602 43.42L.001 175.552l46.3-12.14C59.126 170.68 73.14 174.19 87.44 174.19c48.455 0 87.776-38.683 87.776-87.03C175.216 38.683 135.895 0 87.44 0zm0 160.49c-13.457 0-26.636-3.62-38.07-10.46l-2.73-1.62-28.282 7.414 7.56-27.59-1.774-2.828C17.6 114.218 13.825 100.84 13.825 87.03c0-40.593 33.027-73.62 73.615-73.62 40.59 0 73.616 33.027 73.616 73.62 0 40.59-33.027 73.46-73.616 73.46z"/><path d="M129.08 104.918c-2.21-1.106-13.09-6.456-15.117-7.19-2.028-.737-3.504-1.106-4.98 1.106-1.476 2.21-5.718 7.19-7.011 8.666-1.292 1.476-2.584 1.66-4.797.554-2.21-1.106-9.335-3.44-17.78-10.975-6.572-5.868-11.013-13.113-12.305-15.323-1.292-2.21-.137-3.408 .97-4.51 .993-.993 2.21-2.585 3.317-3.878 1.106-1.292 1.476-2.21 2.21-3.686.737-1.476.37-2.769-.184-3.878-.554-1.106-4.98-12.027-6.826-16.46-1.797-4.325-3.624-3.737-4.98-3.804-1.292-.062-2.769-.075-4.244-.075-1.476 0-3.878.554-5.903 2.769-2.028 2.21-7.747 7.57-7.747 18.49s7.932 21.443 9.038 22.919c1.106 1.476 15.606 23.84 37.825 33.43 5.286 2.284 9.414 3.646 12.63 4.667 5.306 1.687 10.137 1.448 13.955.877 4.257-.636 13.09-5.351 14.937-10.516 1.845-5.165 1.845-9.593 1.291-10.516-.554-.922-2.028-1.476-4.24-2.582z"/></svg>
                                        <span>WA</span>
                                    </a>
                                    <a href="tel:{{ $telPhone }}" data-track-listing="{{ $listing->id }}" data-track-channel="phone"
                                        class="flex items-center justify-center gap-1 rounded border border-slate-300 bg-white px-2 py-1.5 text-xs font-semibold text-slate-800 transition hover:bg-slate-50" title="Appeler">
                                        <svg class="h-3 w-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        <span>Appel</span>
                                    </a>
                                    <a href="{{ route('listings.show', $listing) }}"
                                        class="col-span-2 sm:col-span-1 flex items-center justify-center rounded border border-slate-300 bg-slate-50 px-2 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                                        Détails
                                    </a>
                                </div>
                            @else
                                <a href="{{ route('listings.show', $listing) }}"
                                    class="flex w-full items-center justify-center rounded bg-[#7D5A50] py-1.5 text-xs font-semibold text-white hover:bg-[#6b4d44]">
                                    Consulter
                                </a>
                            @endif
                        </div>
                    </article>

                    <!-- FORMAT GRILLE (Agriaffaires) -->
                    <article x-show="viewMode === 'grid'" class="group rounded-lg border border-slate-200 bg-white shadow-2xs overflow-hidden transition hover:border-slate-300">
                        <div class="relative h-48 sm:h-52 w-full bg-slate-100 overflow-hidden">
                            <img src="{{ $imgSrc }}" alt="{{ $listing->title }}"
                                onerror="this.onerror=null;this.src='{{ $fallbackSrc }}';"
                                class="h-full w-full object-cover transition duration-150 group-hover:scale-102">
                            <span class="absolute left-2.5 top-2.5 rounded px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider text-white {{ $listing->type === 'offer' ? 'bg-[#7D5A50]' : 'bg-amber-600' }}">
                                {{ $listing->type === 'offer' ? 'Offre' : 'Demande' }}
                            </span>
                            @if ($listing->photos->count() > 1)
                                <span class="absolute right-2.5 top-2.5 rounded bg-black/75 px-1.5 py-0.5 text-[9px] font-semibold text-white backdrop-blur-xs">
                                    {{ $listing->photos->count() }} photos
                                </span>
                            @endif
                        </div>
                        <div class="p-3.5">
                            <div class="flex items-center justify-between text-xs text-slate-500">
                                <span class="font-bold text-[#7D5A50]">{{ $listing->category->name }}</span>
                                <span class="flex items-center gap-1 font-medium">
                                    <svg class="h-3 w-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    {{ $listing->location }}
                                </span>
                            </div>
                            <h3 class="mt-1 text-sm font-bold text-slate-900 line-clamp-1">
                                <a href="{{ route('listings.show', $listing) }}" class="hover:text-[#7D5A50]">
                                    {{ $listing->title }}
                                </a>
                            </h3>
                            <p class="mt-2 text-base font-bold text-slate-900">
                                @if ($listing->price)
                                    {{ number_format((float) $listing->price, 0, ',', ' ') }} <span class="text-xs text-[#7D5A50]">FCFA</span>
                                @else
                                    <span class="text-xs text-slate-500 font-semibold">Prix à débattre</span>
                                @endif
                            </p>
                            <div class="mt-3 flex items-center justify-between border-t border-slate-100 pt-2.5 text-xs">
                                <span class="text-[11px] text-slate-500 truncate">{{ $listing->user->name }}</span>
                                <a href="{{ route('listings.show', $listing) }}" class="font-bold text-[#7D5A50] hover:underline">
                                    Voir détails &rarr;
                                </a>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-lg border border-slate-200 bg-white p-10 text-center">
                        <p class="text-sm font-bold text-slate-900">Aucune annonce ne correspond à votre recherche</p>
                        <p class="mt-1 text-xs text-slate-500">Modifiez vos mots-clés ou élargissez la zone géographique.</p>
                        <button type="button" wire:click="resetFilters" class="mt-3 rounded bg-[#7D5A50] px-3.5 py-1.5 text-xs font-semibold text-white hover:bg-[#6b4d44]">
                            Effacer les filtres
                        </button>
                    </div>
                @endforelse
            </div>

            <div class="pt-3">
                {{ $listings->links() }}
            </div>
        </section>
    </div>
</div>
