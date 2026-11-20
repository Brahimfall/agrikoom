<x-app-layout>
    {{-- Hero Section du Blog --}}
    <section class="border-b border-slate-200 bg-white">
        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="rounded bg-[#7D5A50]/15 px-2.5 py-1 text-xs font-bold uppercase tracking-wider text-[#7D5A50]">
                            Conseils &amp; Actualités
                        </span>
                        <span class="text-xs text-slate-400">&bull;</span>
                        <span class="text-xs font-semibold text-slate-500">{{ $totalArticles }} articles publiés</span>
                    </div>
                    <h1 class="mt-3 text-3xl sm:text-4xl font-black text-slate-950 tracking-tight">
                        Le Blog Agricole d'AgriKoom
                    </h1>
                    <p class="mt-2 text-sm sm:text-base text-slate-600 max-w-2xl leading-relaxed">
                        Guides techniques, analyses des cours, machinisme et astuces de terrain pour les agriculteurs, éleveurs et acheteurs au Sénégal.
                    </p>
                </div>

                @auth
                    @if (Auth::user()->is_admin)
                        <div class="shrink-0 flex items-center gap-2">
                            <a href="{{ route('admin.articles.create') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-[#7D5A50] px-4 py-2.5 text-xs font-bold text-white shadow-xs hover:bg-[#6b4d44] transition">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                <span>Rédiger un article</span>
                            </a>
                            <a href="{{ route('admin.articles.index') }}" class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                                ⚙️ Gestion articles
                            </a>
                        </div>
                    @endif
                @endauth
            </div>

            <!-- Barre de recherche et filtres de catégorie -->
            <div class="mt-8 rounded-xl border border-slate-200 bg-slate-50/70 p-3 sm:p-4">
                <form method="GET" action="{{ route('blog.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
                    <div class="relative flex-1 w-full">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </span>
                        <input type="search" name="search" value="{{ request('search') }}"
                               placeholder="Rechercher un article : oignon, tracteur, irrigation, Ladoum..."
                               class="w-full rounded-lg border-slate-300 bg-white py-2 pl-9 pr-4 text-xs sm:text-sm text-slate-900 shadow-2xs focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                    </div>

                    @if (request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif

                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <button type="submit" class="w-full sm:w-auto rounded-lg bg-[#7D5A50] px-5 py-2 text-xs font-bold text-white hover:bg-[#6b4d44] transition shadow-xs">
                            Rechercher
                        </button>
                        @if (request('search') || request('category'))
                            <a href="{{ route('blog.index') }}" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                                Réinitialiser
                            </a>
                        @endif
                    </div>
                </form>

                <!-- Onglets catégories -->
                <div class="mt-3 flex flex-wrap items-center gap-1.5 pt-3 border-t border-slate-200/80 text-xs">
                    <span class="font-bold text-slate-500 mr-1">Filières :</span>
                    <a href="{{ route('blog.index', array_filter(['search' => request('search')])) }}"
                       class="rounded-full px-3 py-1 transition font-bold {{ ! request('category') ? 'bg-[#7D5A50] text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-100' }}">
                        Tous les articles
                    </a>
                    @foreach ($categories as $cat)
                        <a href="{{ route('blog.index', array_filter(['category' => $cat->category, 'search' => request('search')])) }}"
                           class="rounded-full px-3 py-1 transition font-semibold {{ request('category') === $cat->category ? 'bg-[#7D5A50] text-white shadow-xs font-bold' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-100' }}">
                            {{ $cat->category }} ({{ $cat->count }})
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <div class="bg-slate-50 py-10 min-h-screen">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-10">

            {{-- Grand Article à la Une (Featured) si premier affichage --}}
            @if ($featuredArticle)
                <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:shadow-md lg:grid lg:grid-cols-[1.2fr_1fr]">
                    <div class="relative min-h-[260px] lg:min-h-[380px] overflow-hidden bg-slate-900">
                        <img src="{{ $featuredArticle->image_url }}" alt="{{ $featuredArticle->title }}"
                             class="h-full w-full object-cover transition duration-500 hover:scale-105">
                        <span class="absolute top-4 left-4 rounded-full bg-[#7D5A50] px-3 py-1 text-xs font-bold uppercase tracking-wider text-white shadow">
                            À la une
                        </span>
                    </div>
                    <div class="flex flex-col justify-between p-6 sm:p-8">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="rounded px-2.5 py-0.5 text-xs font-extrabold border {{ $featuredArticle->category_badge_classes }}">
                                    {{ $featuredArticle->category }}
                                </span>
                                <span class="text-xs text-slate-400">&bull;</span>
                                <span class="text-xs text-slate-500 font-medium">Lecture {{ $featuredArticle->reading_time }} min</span>
                            </div>

                            <h2 class="mt-3 text-2xl font-black text-slate-950 sm:text-3xl leading-snug hover:text-[#7D5A50] transition">
                                <a href="{{ route('blog.show', $featuredArticle->slug) }}">
                                    {{ $featuredArticle->title }}
                                </a>
                            </h2>

                            <p class="mt-3 text-sm text-slate-600 leading-relaxed line-clamp-3">
                                {{ $featuredArticle->excerpt }}
                            </p>
                        </div>

                        <div class="mt-6 pt-6 border-t border-slate-100 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="h-8 w-8 rounded-full bg-[#7D5A50]/20 flex items-center justify-center font-bold text-xs text-[#7D5A50]">
                                    {{ substr($featuredArticle->author_name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-slate-900">{{ $featuredArticle->author_name }}</p>
                                    <p class="text-[11px] text-slate-400">{{ optional($featuredArticle->published_at)->format('d F Y') }}</p>
                                </div>
                            </div>
                            <a href="{{ route('blog.show', $featuredArticle->slug) }}"
                               class="inline-flex items-center gap-1.5 rounded-lg bg-[#7D5A50] px-4 py-2 text-xs font-bold text-white hover:bg-[#6b4d44] transition shadow-2xs">
                                <span>Lire l'article</span>
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                </article>
            @endif

            {{-- Grille d'articles --}}
            <div>
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-black text-slate-950 sm:text-xl">
                        {{ request('category') ? 'Articles : '.request('category') : (request('search') ? 'Résultats pour "'.request('search').'"' : 'Derniers articles publiés') }}
                    </h2>
                    <span class="text-xs font-semibold text-slate-500">{{ $articles->total() }} articles trouvés</span>
                </div>

                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @forelse ($articles as $article)
                        <article class="group flex flex-col justify-between overflow-hidden rounded-xl border border-slate-200 bg-white shadow-2xs transition hover:border-[#7D5A50]/50 hover:shadow-md">
                            <div>
                                <!-- Image de couverture -->
                                <a href="{{ route('blog.show', $article->slug) }}" class="relative block aspect-[16/10] overflow-hidden bg-slate-100">
                                    <img src="{{ $article->image_url }}" alt="{{ $article->title }}"
                                         class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                                    <span class="absolute top-2.5 left-2.5 rounded-md px-2 py-0.5 text-[10px] font-extrabold uppercase tracking-wide border bg-white/95 shadow-xs {{ $article->category_badge_classes }}">
                                        {{ $article->category }}
                                    </span>
                                </a>

                                <div class="p-5">
                                    <div class="flex items-center gap-2 text-xs text-slate-400">
                                        <span>{{ optional($article->published_at)->format('d/m/Y') }}</span>
                                        <span>&bull;</span>
                                        <span>{{ $article->reading_time }} min de lecture</span>
                                    </div>

                                    <h3 class="mt-2 text-base font-extrabold text-slate-950 leading-snug group-hover:text-[#7D5A50] transition line-clamp-2">
                                        <a href="{{ route('blog.show', $article->slug) }}">
                                            {{ $article->title }}
                                        </a>
                                    </h3>

                                    <p class="mt-2.5 text-xs sm:text-sm text-slate-600 line-clamp-3 leading-relaxed">
                                        {{ $article->excerpt }}
                                    </p>
                                </div>
                            </div>

                            <div class="p-5 pt-0 mt-2 flex items-center justify-between border-t border-slate-100/80 pt-3 text-xs">
                                <span class="font-bold text-slate-500 truncate max-w-[140px]">{{ $article->author_name }}</span>
                                <a href="{{ route('blog.show', $article->slug) }}"
                                   class="font-black text-[#7D5A50] hover:text-[#5a3f38] transition inline-flex items-center gap-1">
                                    <span>Lire la suite</span>
                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </div>
                        </article>
                    @empty
                        <div class="sm:col-span-2 lg:col-span-3 rounded-2xl border border-slate-200 bg-white p-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                            <h3 class="mt-3 text-base font-bold text-slate-800">Aucun article ne correspond à votre recherche</h3>
                            <p class="mt-1 text-xs text-slate-500">Essayez un autre mot-clé ou consultez toutes nos filières.</p>
                            <a href="{{ route('blog.index') }}" class="mt-4 inline-flex items-center rounded-lg bg-[#7D5A50] px-4 py-2 text-xs font-bold text-white hover:bg-[#6b4d44]">
                                Voir tous les articles
                            </a>
                        </div>
                    @endforelse
                </div>

                @if ($articles->hasPages())
                    <div class="mt-8">
                        {{ $articles->links() }}
                    </div>
                @endif
            </div>

            <!-- Bandeau CTA : Partager ses annonces -->
            <div class="rounded-2xl border border-[#7D5A50]/20 bg-[#7D5A50] p-6 sm:p-8 text-white flex flex-col md:flex-row md:items-center md:justify-between gap-6 shadow-md">
                <div class="max-w-xl">
                    <span class="rounded bg-white/20 px-2.5 py-1 text-xs font-bold uppercase tracking-wider text-white">Marketplace AgriKoom</span>
                    <h3 class="mt-2 text-xl sm:text-2xl font-black">Vous vendez du matériel, du bétail ou des récoltes ?</h3>
                    <p class="mt-1 text-xs sm:text-sm text-white/85">Diffusez gratuitement votre offre auprès des milliers d'agriculteurs et acheteurs qui consultent AgriKoom chaque jour.</p>
                </div>
                <div class="shrink-0 flex items-center gap-3">
                    <a href="{{ route('listings.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-white px-5 py-3 text-xs sm:text-sm font-extrabold uppercase tracking-wide text-[#7D5A50] shadow-sm hover:bg-slate-50 transition active:scale-98">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        <span>Publier une annonce</span>
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

