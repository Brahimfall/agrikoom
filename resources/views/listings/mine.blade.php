<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-bold text-slate-950">
                        {{ $isAdmin ? 'Gestion des annonces & produits' : 'Mes annonces' }}
                    </h1>
                    @if ($isAdmin)
                        <span class="rounded-full bg-[#7D5A50] px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider text-white">Super Admin</span>
                    @endif
                </div>
                <p class="mt-1 text-sm text-slate-600">
                    {{ $isAdmin ? 'Consultez, modifiez ou ajoutez des annonces pour vous-même ou pour d\'autres vendeurs.' : 'Suivez vos annonces actives, vendues ou expirées.' }}
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @if ($isAdmin)
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 transition">
                        Tableau de bord
                    </a>
                    <a href="{{ route('listings.create', ['vendor_mode' => 'new']) }}" class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-emerald-700 px-3.5 py-2 text-xs font-bold uppercase tracking-wider text-white shadow-xs hover:bg-emerald-800 transition">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        <span>+ Nouveau client / vendeur</span>
                    </a>
                @endif
                <a href="{{ route('listings.create') }}" class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-[#7D5A50] px-4 py-2 text-xs font-bold uppercase tracking-wider text-white shadow-xs hover:bg-[#6b4d44] transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    <span>{{ $isAdmin ? 'Publier (Moi / Existant)' : 'Publier' }}</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
                    ✓ {{ session('status') }}
                </div>
            @endif

            {{-- ══════════════════ ONGLET DE FILTRAGE ADMIN ══════════════════ --}}
            @if ($isAdmin)
                <div class="mb-6 flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-xs">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('listings.mine', ['filter' => 'all']) }}"
                               class="rounded-lg px-3.5 py-1.5 text-xs font-bold transition {{ $currentFilter === 'all' && !request('user_id') ? 'bg-[#7D5A50] text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                                Toutes les annonces de la plateforme ({{ $allCount }})
                            </a>
                            <a href="{{ route('listings.mine', ['filter' => 'mine']) }}"
                               class="rounded-lg px-3.5 py-1.5 text-xs font-bold transition {{ $currentFilter === 'mine' ? 'bg-[#7D5A50] text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                                Mes propres annonces ({{ $myCount }})
                            </a>
                        </div>
                    </div>

                    {{-- Recherche & filtre par vendeur --}}
                    <form method="GET" action="{{ route('listings.mine') }}" class="flex flex-wrap items-center gap-3">
                        <input type="hidden" name="filter" value="{{ $currentFilter }}">
                        <div class="flex-1 min-w-[200px]">
                            <input type="search" name="search" value="{{ request('search') }}" placeholder="Rechercher par titre, produit, localisation ou vendeur..."
                                class="w-full rounded-lg border-slate-300 py-1.5 pl-3 text-xs focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                        </div>
                        @if ($sellers->isNotEmpty())
                            <div class="min-w-[180px]">
                                <select name="user_id" onchange="this.form.submit()" class="w-full rounded-lg border-slate-300 py-1.5 text-xs focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                                    <option value="">Tous les vendeurs ({{ $sellers->count() }})</option>
                                    @foreach ($sellers as $seller)
                                        <option value="{{ $seller->id }}" {{ (string) request('user_id') === (string) $seller->id ? 'selected' : '' }}>
                                            {{ $seller->name }} {{ $seller->phone ? '('.$seller->phone.')' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <button type="submit" class="rounded-lg bg-slate-800 px-3.5 py-1.5 text-xs font-bold text-white hover:bg-black transition">
                            Filtrer
                        </button>
                        @if (request()->hasAny(['search', 'user_id', 'status']))
                            <a href="{{ route('listings.mine', ['filter' => $currentFilter]) }}" class="text-xs text-red-600 hover:underline">
                                Réinitialiser
                            </a>
                        @endif
                    </form>
                </div>
            @endif

            <div class="overflow-hidden bg-white shadow-xs rounded-2xl border border-slate-200">
                <div class="divide-y divide-slate-100">
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
                    @forelse ($listings as $listing)
                        @php
                            $firstPhoto = $listing->photos->first();
                            $fallbackFile = $categoryFallbacks[$listing->category->name ?? ''] ?? 'agri-products.png';
                            $imgSrc = $firstPhoto ? Storage::url($firstPhoto->path) : asset('storage/listings/demo/'.$fallbackFile);
                            $fallbackSrc = asset('storage/listings/demo/'.$fallbackFile);
                            $isMyListing = $listing->user_id === Auth::id();
                        @endphp
                        <div class="flex flex-col gap-3 p-3.5 sm:p-5 lg:flex-row lg:items-center lg:justify-between hover:bg-slate-50/50 transition">
                            <div class="flex flex-row gap-3 min-w-0 flex-1">
                                <a href="{{ route('listings.show', $listing) }}" class="relative block w-28 sm:w-32 aspect-[4/3] shrink-0 overflow-hidden rounded-xl bg-slate-100 border border-slate-200">
                                    <img src="{{ $imgSrc }}" alt="{{ $listing->title }}"
                                        onerror="this.onerror=null;this.src='{{ $fallbackSrc }}';"
                                        class="h-full w-full object-cover">
                                    <span class="absolute bottom-1 left-1/2 -translate-x-1/2 flex items-center gap-1 rounded bg-black/70 px-1.5 py-0.5 text-[9px] font-bold text-white shadow-xs">
                                        <svg class="h-2.5 w-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        {{ max(1, $listing->photos->count()) }}
                                    </span>
                                </a>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <h2 class="text-sm sm:text-base font-bold text-slate-950 line-clamp-1">
                                            <a href="{{ route('listings.show', $listing) }}" class="hover:text-[#7D5A50]">{{ $listing->title }}</a>
                                        </h2>
                                        <span class="rounded-full px-2 py-0.5 text-[10px] font-bold uppercase {{ $listing->status === 'active' ? 'bg-emerald-100 text-emerald-800' : ($listing->status === 'sold' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700') }}">
                                            {{ $listing->status === 'active' ? 'En ligne' : ($listing->status === 'sold' ? 'Vendue' : $listing->status) }}
                                        </span>
                                        <span class="rounded-full bg-[#7D5A50]/10 px-2 py-0.5 text-[10px] font-bold text-[#7D5A50]">
                                            {{ $listing->type === 'offer' ? 'Offre' : 'Demande' }}
                                        </span>
                                    </div>
                                    <p class="mt-0.5 text-xs text-slate-600 font-medium truncate">
                                        <span class="font-bold text-slate-800">{{ $listing->product }}</span> &bull; {{ $listing->location }} &bull; {{ $listing->category->name }}
                                        @if ($listing->price)
                                            &bull; <span class="font-bold text-[#7D5A50]">{{ number_format($listing->price, 0, ',', ' ') }} FCFA</span>
                                        @endif
                                    </p>

                                    {{-- Info Vendeur (Très utile pour le Super Admin) --}}
                                    @if ($isAdmin)
                                        <div class="mt-1 flex flex-wrap items-center gap-2 text-xs">
                                            <span class="inline-flex items-center gap-1 rounded bg-slate-100 px-2 py-0.5 font-semibold text-slate-700">
                                                <span>👤 Vendeur :</span>
                                                <a href="{{ route('listings.mine', ['user_id' => $listing->user_id, 'filter' => 'all']) }}" class="font-bold text-[#7D5A50] hover:underline">
                                                    {{ $listing->user->name ?? 'Utilisateur inconnu' }}
                                                </a>
                                                @if ($isMyListing)
                                                    <span class="ml-1 rounded bg-emerald-100 px-1 text-[9px] font-black text-emerald-700">Moi (Admin)</span>
                                                @endif
                                            </span>
                                            @if ($listing->user?->phone)
                                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $listing->user->phone) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-medium text-emerald-700 hover:underline">
                                                    📞 {{ $listing->user->phone }}
                                                </a>
                                            @endif
                                        </div>
                                    @endif

                                    <p class="mt-1 text-[11px] text-slate-400">Expire {{ optional($listing->expires_at)->diffForHumans() ?? 'sans date' }}</p>
                                </div>
                            </div>

                            <div class="flex flex-wrap items-center gap-2 self-start lg:self-center shrink-0">
                                <a href="{{ route('listings.edit', $listing) }}" class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-bold text-slate-700 hover:bg-slate-50 transition shadow-2xs">
                                    Modifier
                                </a>
                                @if ($listing->status === 'active')
                                    <form method="POST" action="{{ route('listings.sold', $listing) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-bold text-slate-700 hover:bg-slate-50 transition shadow-2xs">
                                            Marquer vendue
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('listings.renew', $listing) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="rounded-lg border border-emerald-300 bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-800 hover:bg-emerald-100 transition shadow-2xs">
                                            Remettre en vente
                                        </button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('listings.renew', $listing) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-bold text-slate-700 hover:bg-slate-50 transition shadow-2xs">
                                        Renouveler (30j)
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('listings.destroy', $listing) }}" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer définitivement cette annonce ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg bg-rose-50 border border-rose-200 px-3 py-1.5 text-xs font-bold text-rose-700 hover:bg-rose-100 transition shadow-2xs">
                                        Supprimer
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center">
                            <p class="font-bold text-slate-800 text-base">Aucune annonce trouvée.</p>
                            <p class="text-xs text-slate-500 mt-1">Commencez dès maintenant en publiant un produit pour vous ou un vendeur.</p>
                            <a href="{{ route('listings.create') }}" class="mt-4 inline-flex rounded-lg bg-[#7D5A50] px-4 py-2 text-xs font-bold uppercase tracking-wider text-white hover:bg-[#6b4d44] shadow-xs">
                                + Publier une annonce
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="mt-6">
                {{ $listings->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
