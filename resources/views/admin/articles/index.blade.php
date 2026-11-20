<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <div class="flex items-center gap-2">
                    <span class="rounded bg-[#7D5A50]/15 px-2 py-0.5 text-xs font-bold uppercase tracking-wider text-[#7D5A50]">Super admin</span>
                    <span class="text-xs text-slate-400">&bull;</span>
                    <span class="text-xs font-semibold text-slate-500">Contenu éditorial</span>
                </div>
                <h1 class="mt-1 text-2xl font-black text-slate-950">Gestion du Blog Agricole</h1>
                <p class="mt-1 text-sm text-slate-500">Rédigez et publiez des guides, conseils techniques et actualités pour les producteurs et acheteurs.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.dashboard') }}" class="inline-flex min-h-10 items-center rounded-md border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Tableau de bord
                </a>
                <a href="{{ route('blog.index') }}" target="_blank" class="inline-flex min-h-10 items-center rounded-md border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    <svg class="mr-1.5 h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    Voir le blog public
                </a>
                <a href="{{ route('admin.articles.create') }}" class="inline-flex min-h-10 items-center rounded-md bg-[#7D5A50] px-4 text-sm font-bold text-white shadow-xs hover:bg-[#6b4d44]">
                    <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Nouvel article
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-50 min-h-screen">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="rounded-lg border border-[#7D5A50]/20 bg-white p-4 text-sm font-semibold text-[#5a3f38] shadow-xs flex items-center gap-2">
                    <svg class="h-5 w-5 text-[#7D5A50]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <!-- Mini stats -->
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-2xs">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total articles</p>
                    <p class="mt-2 text-2xl font-black text-slate-950">{{ $stats['total'] }}</p>
                    <p class="mt-1 text-[11px] text-slate-400">rédigés</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-2xs">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">En ligne (Publiés)</p>
                    <p class="mt-2 text-2xl font-black text-[#7D5A50]">{{ $stats['published'] }}</p>
                    <p class="mt-1 text-[11px] text-slate-400">visibles aux lecteurs</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-2xs">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Brouillons</p>
                    <p class="mt-2 text-2xl font-black text-amber-700">{{ $stats['draft'] }}</p>
                    <p class="mt-1 text-[11px] text-slate-400">en attente</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-2xs">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Lectures totales</p>
                    <p class="mt-2 text-2xl font-black text-slate-950">{{ number_format($stats['views'], 0, ',', ' ') }}</p>
                    <p class="mt-1 text-[11px] text-slate-400">vues cumulées</p>
                </div>
            </div>

            <!-- Filtres et recherche -->
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-2xs">
                <form method="GET" action="{{ route('admin.articles.index') }}" class="grid gap-3 sm:grid-cols-2 md:grid-cols-4">
                    <div class="sm:col-span-2">
                        <label for="search" class="block text-xs font-bold text-slate-700 mb-1">Recherche</label>
                        <input id="search" type="search" name="search" value="{{ request('search') }}"
                               placeholder="Titre, catégorie, auteur..."
                               class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                    </div>

                    <div>
                        <label for="category" class="block text-xs font-bold text-slate-700 mb-1">Filière</label>
                        <select id="category" name="category" class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                            <option value="">Toutes les filières</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat }}" @selected(request('category') === $cat)>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-end gap-2">
                        <div class="flex-1">
                            <label for="status" class="block text-xs font-bold text-slate-700 mb-1">Statut</label>
                            <select id="status" name="status" class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                                <option value="">Tous</option>
                                <option value="published" @selected(request('status') === 'published')>Publiés</option>
                                <option value="draft" @selected(request('status') === 'draft')>Brouillons</option>
                            </select>
                        </div>
                        <button type="submit" class="rounded-lg bg-[#7D5A50] px-4 py-2 text-sm font-bold text-white hover:bg-[#6b4d44]">
                            Filtrer
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tableau des articles -->
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-2xs">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-700">
                        <thead class="border-b border-slate-200 bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-600">
                            <tr>
                                <th class="px-4 py-3.5">Article</th>
                                <th class="px-4 py-3.5">Filière</th>
                                <th class="px-4 py-3.5">Auteur</th>
                                <th class="px-4 py-3.5">Statut</th>
                                <th class="px-4 py-3.5">Vues</th>
                                <th class="px-4 py-3.5">Date</th>
                                <th class="px-4 py-3.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($articles as $item)
                                <tr class="hover:bg-slate-50/80 transition {{ $item->status === 'draft' ? 'bg-slate-50/50' : '' }}">
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="h-12 w-16 shrink-0 overflow-hidden rounded-md bg-slate-100 border border-slate-200">
                                                <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="h-full w-full object-cover">
                                            </div>
                                            <div class="min-w-0">
                                                <div class="flex items-center gap-1.5">
                                                    <a href="{{ route('admin.articles.edit', $item) }}" class="font-extrabold text-slate-950 hover:text-[#7D5A50] transition truncate max-w-md block">
                                                        {{ $item->title }}
                                                    </a>
                                                    @if ($item->is_featured)
                                                        <span class="rounded bg-amber-100 px-1.5 py-0.2 text-[10px] font-bold text-amber-900">À la une</span>
                                                    @endif
                                                </div>
                                                <p class="mt-0.5 text-xs text-slate-500 line-clamp-1 max-w-md">{{ $item->excerpt }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-4 py-4">
                                        <span class="inline-block rounded-md px-2 py-0.5 text-xs font-bold border {{ $item->category_badge_classes }}">
                                            {{ $item->category }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-4 text-xs font-medium text-slate-700">
                                        {{ $item->author_name }}
                                    </td>

                                    <td class="px-4 py-4">
                                        <form method="POST" action="{{ route('admin.articles.toggle', $item) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-bold transition {{ $item->status === 'published' ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-amber-100 text-amber-800 hover:bg-amber-200' }}">
                                                <span class="h-1.5 w-1.5 rounded-full {{ $item->status === 'published' ? 'bg-emerald-600' : 'bg-amber-600' }}"></span>
                                                {{ $item->status === 'published' ? 'En ligne' : 'Brouillon' }}
                                            </button>
                                        </form>
                                    </td>

                                    <td class="px-4 py-4 text-xs font-bold text-slate-700">
                                        {{ number_format($item->views_count, 0, ',', ' ') }}
                                    </td>

                                    <td class="px-4 py-4 text-xs text-slate-500 whitespace-nowrap">
                                        {{ optional($item->published_at)->format('d/m/Y') ?? 'Non publié' }}
                                    </td>

                                    <td class="px-4 py-4 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="{{ route('blog.show', $item->slug) }}" target="_blank"
                                               class="rounded-md border border-slate-200 bg-white p-1.5 text-slate-500 hover:text-[#7D5A50] hover:bg-slate-50 transition" title="Voir l'article">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </a>
                                            <a href="{{ route('admin.articles.edit', $item) }}"
                                               class="rounded-md border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                                                Modifier
                                            </a>
                                            <form method="POST" action="{{ route('admin.articles.destroy', $item) }}" onsubmit="return confirm('Supprimer cet article ?');">
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
                                    <td colspan="7" class="px-4 py-12 text-center">
                                        <div class="mx-auto max-w-sm text-center">
                                            <svg class="mx-auto h-10 w-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                                            <p class="mt-3 text-sm font-bold text-slate-800">Aucun article enregistré</p>
                                            <p class="mt-1 text-xs text-slate-500">Commencez dès maintenant en publiant votre premier guide agricole.</p>
                                            <a href="{{ route('admin.articles.create') }}" class="mt-4 inline-flex items-center rounded-lg bg-[#7D5A50] px-4 py-2 text-xs font-bold text-white hover:bg-[#6b4d44]">
                                                + Rédiger un article
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($articles->hasPages())
                    <div class="border-t border-slate-200 bg-white p-4">
                        {{ $articles->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

