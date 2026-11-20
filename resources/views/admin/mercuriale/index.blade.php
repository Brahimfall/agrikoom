<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <div class="flex items-center gap-2">
                    <span class="rounded bg-[#7D5A50]/15 px-2 py-0.5 text-xs font-bold uppercase tracking-wider text-[#7D5A50]">Super admin</span>
                    <span class="text-xs text-slate-400">&bull;</span>
                    <span class="text-xs font-semibold text-slate-500">Données de marché</span>
                </div>
                <h1 class="mt-1 text-2xl font-black text-slate-950">Prix du marché &amp; Cotations</h1>
                <p class="mt-1 text-sm text-slate-500">Gérez en direct les prix affichés sur la page d'accueil pour informer les acheteurs et vendeurs.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.dashboard') }}" class="inline-flex min-h-10 items-center rounded-md border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Tableau de bord
                </a>
                <a href="{{ route('home') }}#mercuriale" target="_blank" class="inline-flex min-h-10 items-center rounded-md border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    <svg class="mr-1.5 h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    Aperçu sur le site
                </a>
                <a href="{{ route('admin.mercuriale.create') }}" class="inline-flex min-h-10 items-center rounded-md bg-[#7D5A50] px-4 text-sm font-bold text-white shadow-xs hover:bg-[#6b4d44]">
                    <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Ajouter un cours
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-50 min-h-screen">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="rounded-lg border border-[#7D5A50]/20 bg-white p-4 text-sm font-semibold text-[#5a3f38] shadow-xs flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="h-5 w-5 text-[#7D5A50]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('status') }}</span>
                    </div>
                </div>
            @endif

            <!-- Mini stats -->
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-2xs">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total des cours</p>
                    <p class="mt-2 text-2xl font-black text-slate-950">{{ $stats['total'] }}</p>
                    <p class="mt-1 text-[11px] text-slate-400">enregistrés en base</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-2xs">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">En ligne (actifs)</p>
                    <p class="mt-2 text-2xl font-black text-[#7D5A50]">{{ $stats['active'] }}</p>
                    <p class="mt-1 text-[11px] text-slate-400">visibles sur l'accueil</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-2xs">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Masqués (inactifs)</p>
                    <p class="mt-2 text-2xl font-black text-slate-600">{{ $stats['inactive'] }}</p>
                    <p class="mt-1 text-[11px] text-slate-400">hors ligne</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-2xs">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Catégories</p>
                    <p class="mt-2 text-2xl font-black text-slate-950">{{ $categories->count() }}</p>
                    <p class="mt-1 text-[11px] text-slate-400">filières suivies</p>
                </div>
            </div>

            <!-- Filtres et recherche -->
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-2xs">
                <form method="GET" action="{{ route('admin.mercuriale.index') }}" class="grid gap-3 sm:grid-cols-2 md:grid-cols-4">
                    <div class="sm:col-span-2">
                        <label for="search" class="block text-xs font-bold text-slate-700 mb-1">Recherche</label>
                        <input id="search" type="search" name="search" value="{{ request('search') }}"
                               placeholder="Produit, lieu, prix..."
                               class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                    </div>

                    <div>
                        <label for="category" class="block text-xs font-bold text-slate-700 mb-1">Catégorie</label>
                        <select id="category" name="category" class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                            <option value="">Toutes les catégories</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat }}" @selected(request('category') === $cat)>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-end gap-2">
                        <div class="flex-1">
                            <label for="status" class="block text-xs font-bold text-slate-700 mb-1">Visibilité</label>
                            <select id="status" name="status" class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                                <option value="">Tous</option>
                                <option value="active" @selected(request('status') === 'active')>Actifs uniquement</option>
                                <option value="inactive" @selected(request('status') === 'inactive')>Inactifs uniquement</option>
                            </select>
                        </div>
                        <button type="submit" class="rounded-lg bg-[#7D5A50] px-4 py-2 text-sm font-bold text-white hover:bg-[#6b4d44]">
                            Filtrer
                        </button>
                    </div>
                </form>
            </div>

            <!-- Liste des cours de marché -->
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-2xs">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-700">
                        <thead class="border-b border-slate-200 bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-600">
                            <tr>
                                <th class="px-4 py-3.5">Ordre</th>
                                <th class="px-4 py-3.5">Produit &amp; Ticker</th>
                                <th class="px-4 py-3.5">Cours Actuel</th>
                                <th class="px-4 py-3.5">Variation Bourse</th>
                                <th class="px-4 py-3.5">Conditionnement</th>
                                <th class="px-4 py-3.5">Zone &amp; Source</th>
                                <th class="px-4 py-3.5">Visibilité</th>
                                <th class="px-4 py-3.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($marketPrices as $item)
                                <tr class="hover:bg-slate-50/80 transition {{ ! $item->is_active ? 'opacity-60 bg-slate-50/50' : '' }}">
                                    <td class="px-4 py-4 text-xs font-bold text-slate-400">
                                        #{{ $item->sort_order }}
                                    </td>

                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-1.5">
                                            <span class="font-mono text-[10px] font-black uppercase tracking-wider rounded bg-slate-900 text-amber-300 px-1.5 py-0.5">
                                                {{ $item->ticker_display }}
                                            </span>
                                            <span class="font-extrabold text-slate-950">{{ $item->product_name }}</span>
                                        </div>
                                        <span class="inline-block mt-1 rounded bg-slate-100 px-1.5 py-0.5 text-[10px] font-semibold text-slate-600">
                                            {{ $item->category }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-4">
                                        <div class="font-black text-base text-[#7D5A50]">
                                            {{ $item->price_range }}
                                        </div>
                                        @if ($item->previous_price)
                                            <div class="text-[11px] text-slate-400">
                                                Réf. préc. : {{ $item->previous_price }}
                                            </div>
                                        @endif
                                    </td>

                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-1.5">
                                            <span class="inline-flex items-center gap-1 rounded-md border px-2 py-0.5 text-xs font-extrabold {{ $item->bourse_badge_classes }}">
                                                <span class="font-mono">{{ $item->bourse_arrow }}</span>
                                                <span>{{ $item->formatted_change }}</span>
                                            </span>
                                            <span class="text-[10px] font-bold uppercase tracking-wider {{ $item->is_cheaper ? 'text-emerald-700' : ($item->is_more_expensive ? 'text-rose-700' : 'text-slate-500') }}">
                                                @if ($item->is_cheaper)
                                                    Moins cher
                                                @elseif ($item->is_more_expensive)
                                                    Plus cher
                                                @else
                                                    Stable
                                                @endif
                                            </span>
                                        </div>
                                    </td>

                                    <td class="px-4 py-4 text-xs font-medium text-slate-600">
                                        {{ $item->unit ?: '—' }}
                                    </td>

                                    <td class="px-4 py-4">
                                        <div class="text-xs font-semibold text-slate-800">{{ $item->location ?: 'Sénégal' }}</div>
                                        <div class="text-[11px] text-slate-400 line-clamp-1">{{ $item->source }}</div>
                                    </td>

                                    <td class="px-4 py-4">
                                        <form method="POST" action="{{ route('admin.mercuriale.toggle', $item) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-bold transition {{ $item->is_active ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-slate-200 text-slate-600 hover:bg-slate-300' }}">
                                                <span class="h-1.5 w-1.5 rounded-full {{ $item->is_active ? 'bg-emerald-600' : 'bg-slate-400' }}"></span>
                                                {{ $item->is_active ? 'En ligne' : 'Masqué' }}
                                            </button>
                                        </form>
                                    </td>

                                    <td class="px-4 py-4 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="{{ route('admin.mercuriale.edit', $item) }}"
                                               class="rounded-md border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition">
                                                Modifier
                                            </a>
                                            <form method="POST" action="{{ route('admin.mercuriale.destroy', $item) }}" onsubmit="return confirm('Confirmer la suppression de ce cours de marché ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="rounded-md border border-rose-200 bg-rose-50 px-2 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-100 transition">
                                                    Supprimer
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-12 text-center">
                                        <div class="mx-auto max-w-sm text-center">
                                            <svg class="mx-auto h-10 w-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                            <p class="mt-3 text-sm font-bold text-slate-800">Aucun cours trouvé</p>
                                            <p class="mt-1 text-xs text-slate-500">Ajoutez votre premier relevé de prix pour alimenter la mercuriale.</p>
                                            <a href="{{ route('admin.mercuriale.create') }}" class="mt-4 inline-flex items-center rounded-lg bg-[#7D5A50] px-4 py-2 text-xs font-bold text-white hover:bg-[#6b4d44]">
                                                + Ajouter un cours
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($marketPrices->hasPages())
                    <div class="border-t border-slate-200 bg-white p-4">
                        {{ $marketPrices->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

