<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <div class="flex items-center gap-2">
                    <span class="rounded bg-[#7D5A50]/15 px-2 py-0.5 text-xs font-bold uppercase tracking-wider text-[#7D5A50]">Super admin</span>
                    <span class="text-xs text-slate-400">&bull;</span>
                    <span class="text-xs font-semibold text-slate-500">Catalogue &amp; Filières</span>
                </div>
                <h1 class="mt-1 text-2xl font-black text-slate-950">Gestion des Catégories</h1>
                <p class="mt-1 text-sm text-slate-500">Modifiez, organisez, supprimez ou ajoutez les catégories de produits affichées sur AgriKoom.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.dashboard') }}" class="inline-flex min-h-10 items-center rounded-md border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Tableau de bord
                </a>
                <a href="{{ route('listings.create') }}" class="inline-flex min-h-10 items-center rounded-md border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Publier une annonce
                </a>
                <a href="{{ route('admin.categories.create') }}" class="inline-flex min-h-10 items-center rounded-md bg-[#7D5A50] px-4 text-sm font-bold text-white shadow-xs hover:bg-[#6b4d44]">
                    <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Nouvelle catégorie
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-50 min-h-screen">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-semibold text-emerald-900 shadow-xs flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="h-5 w-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('status') }}</span>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-900 shadow-xs">
                    <div class="flex items-start gap-2">
                        <svg class="h-5 w-5 text-red-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Mini stats -->
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-2xs">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total catégories</p>
                    <p class="mt-2 text-2xl font-black text-slate-950">{{ $stats['total'] }}</p>
                    <p class="mt-1 text-[11px] text-slate-400">filières configurées</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-2xs">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Actives (en ligne)</p>
                    <p class="mt-2 text-2xl font-black text-emerald-700">{{ $stats['active'] }}</p>
                    <p class="mt-1 text-[11px] text-slate-400">visibles sur le site</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-2xs">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Masquées</p>
                    <p class="mt-2 text-2xl font-black text-slate-600">{{ $stats['inactive'] }}</p>
                    <p class="mt-1 text-[11px] text-slate-400">désactivées</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-2xs">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total annonces</p>
                    <p class="mt-2 text-2xl font-black text-[#7D5A50]">{{ $stats['total_listings'] }}</p>
                    <p class="mt-1 text-[11px] text-slate-400">produits rattachés</p>
                </div>
            </div>

            <!-- Filtres et recherche -->
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-2xs">
                <form method="GET" action="{{ route('admin.categories.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <div class="relative flex-1">
                        <svg class="absolute left-3 top-2.5 h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher par nom, description ou slug..." class="w-full rounded-lg border-slate-300 pl-9 text-sm shadow-xs focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                    </div>
                    <div class="sm:w-48">
                        <select name="status" class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                            <option value="">Tous les statuts</option>
                            <option value="active" @selected(request('status') === 'active')>Actives uniquement</option>
                            <option value="inactive" @selected(request('status') === 'inactive')>Masquées uniquement</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                            Filtrer
                        </button>
                        @if (request()->hasAny(['search', 'status']))
                            <a href="{{ route('admin.categories.index') }}" class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                                Réinitialiser
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Liste des catégories -->
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-2xs">
                <div class="divide-y divide-slate-100">
                    @forelse ($categories as $cat)
                        <div class="flex flex-col gap-4 p-4 sm:flex-row sm:items-center sm:justify-between transition hover:bg-slate-50/70">
                            <div class="flex items-center gap-3.5 min-w-0">
                                <!-- Photo de la catégorie -->
                                <div class="relative h-14 w-14 shrink-0 overflow-hidden rounded-lg border border-slate-200 bg-slate-100 shadow-2xs">
                                    <img src="{{ $cat->image_url }}" alt="{{ $cat->name }}" class="h-full w-full object-cover">
                                    @if (! $cat->is_active)
                                        <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
                                            <span class="text-[9px] font-bold text-white uppercase tracking-wider">OFF</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="text-base font-extrabold text-slate-900 leading-snug truncate">
                                            {{ $cat->name }}
                                        </h3>
                                        <span class="rounded bg-slate-100 px-2 py-0.5 text-[11px] font-mono text-slate-600">
                                            {{ $cat->slug }}
                                        </span>
                                        @if ($cat->is_active)
                                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700 border border-emerald-200">
                                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                                Visible
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-600 border border-slate-200">
                                                <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                                                Masquée
                                            </span>
                                        @endif
                                    </div>

                                    <p class="mt-1 text-xs text-slate-500 line-clamp-1">
                                        {{ $cat->subtitle }}
                                    </p>

                                    <div class="mt-1.5 flex flex-wrap items-center gap-3 text-xs text-slate-500">
                                        <span class="font-medium text-slate-700">
                                            📦 <strong>{{ $cat->listings_count }}</strong> annonce(s) au total
                                        </span>
                                        <span>&bull;</span>
                                        <span class="text-emerald-700 font-semibold">
                                            🟢 <strong>{{ $cat->active_listings_count }}</strong> active(s) en ligne
                                        </span>
                                        <span>&bull;</span>
                                        <a href="{{ route('products.index', ['category' => $cat->id]) }}" target="_blank" class="text-[#7D5A50] hover:underline inline-flex items-center gap-1">
                                            Voir sur le site
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center gap-2 self-end sm:self-center shrink-0">
                                <!-- Basculer statut -->
                                <form method="POST" action="{{ route('admin.categories.toggle', $cat) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" title="{{ $cat->is_active ? 'Masquer cette catégorie' : 'Activer cette catégorie' }}" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                        {{ $cat->is_active ? 'Masquer' : 'Activer' }}
                                    </button>
                                </form>

                                <!-- Modifier -->
                                <a href="{{ route('admin.categories.edit', $cat) }}" class="inline-flex items-center gap-1 rounded-lg bg-[#7D5A50] px-3 py-1.5 text-xs font-bold text-white hover:bg-[#6b4d44] transition shadow-2xs">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    Modifier
                                </a>

                                <!-- Supprimer / Transférer -->
                                @if ($cat->listings_count === 0)
                                    <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}" onsubmit="return confirm('Confirmez-vous la suppression définitive de la catégorie {{ $cat->name }} ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 rounded-lg border border-red-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50">
                                            Supprimer
                                        </button>
                                    </form>
                                @else
                                    <button type="button" onclick="document.getElementById('modal-delete-{{ $cat->id }}').classList.remove('hidden')" class="inline-flex items-center gap-1 rounded-lg border border-red-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50" title="Cette catégorie contient des annonces">
                                        Supprimer...
                                    </button>

                                    <!-- Modal de suppression avec réassignation -->
                                    <div id="modal-delete-{{ $cat->id }}" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 hidden">
                                        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl text-left">
                                            <div class="flex items-center gap-3 text-red-600 mb-3">
                                                <div class="rounded-full bg-red-100 p-2">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                </div>
                                                <h3 class="text-lg font-bold text-slate-900">Supprimer la catégorie « {{ $cat->name }} »</h3>
                                            </div>

                                            <p class="text-sm text-slate-600 mb-4">
                                                Cette catégorie contient <strong>{{ $cat->listings_count }} annonce(s)</strong>. Pour éviter de perdre ces annonces, veuillez sélectionner une autre catégorie vers laquelle les transférer avant de supprimer :
                                            </p>

                                            <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}" class="space-y-4">
                                                @csrf
                                                @method('DELETE')

                                                <div>
                                                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Transférer les annonces vers :</label>
                                                    <select name="reassign_to_id" required class="w-full rounded-lg border-slate-300 text-sm focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                                                        <option value="">-- Choisir une catégorie de destination --</option>
                                                        @foreach ($categories as $targetCat)
                                                            @if ($targetCat->id !== $cat->id)
                                                                <option value="{{ $targetCat->id }}">{{ $targetCat->name }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="flex items-center justify-end gap-2 pt-2">
                                                    <button type="button" onclick="document.getElementById('modal-delete-{{ $cat->id }}').classList.add('hidden')" class="rounded-lg border border-slate-300 px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50">
                                                        Annuler
                                                    </button>
                                                    <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-xs font-bold text-white hover:bg-red-700 shadow-xs">
                                                        Transférer et supprimer
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-500">
                            <p class="font-semibold">Aucune catégorie trouvée.</p>
                            <p class="mt-1 text-sm">Créez votre première catégorie pour organiser les produits sur le site.</p>
                            <a href="{{ route('admin.categories.create') }}" class="mt-4 inline-flex items-center rounded-md bg-[#7D5A50] px-4 py-2 text-sm font-bold text-white hover:bg-[#6b4d44]">
                                + Créer une catégorie
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
