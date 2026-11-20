<x-app-layout>
    @php
        $shareUrl = urlencode(url()->current());
        $shareText = urlencode($article->title . ' - A lire sur AgriKoom Blog : ' . url()->current());
    @endphp

    <div class="bg-slate-50 py-8 min-h-screen">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Fil d'Ariane & Boutons d'action -->
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <nav class="flex items-center gap-2 text-xs font-semibold text-slate-500 overflow-x-auto whitespace-nowrap">
                    <a href="{{ route('home') }}" class="hover:text-[#7D5A50] transition">Accueil</a>
                    <span>/</span>
                    <a href="{{ route('blog.index') }}" class="hover:text-[#7D5A50] transition">Blog</a>
                    <span>/</span>
                    <a href="{{ route('blog.index', ['category' => $article->category]) }}" class="hover:text-[#7D5A50] transition">{{ $article->category }}</a>
                    <span>/</span>
                    <span class="text-slate-800 truncate max-w-[200px]">{{ $article->title }}</span>
                </nav>

                <div class="flex items-center gap-2 self-start sm:self-auto">
                    @auth
                        @if (Auth::user()->is_admin)
                            <a href="{{ route('admin.articles.edit', $article) }}"
                               class="inline-flex items-center gap-1.5 rounded-lg border border-[#7D5A50] bg-[#7D5A50] px-3 py-1.5 text-xs font-bold text-white shadow-2xs hover:bg-[#6b4d44] transition">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                <span>Modifier l'article</span>
                            </a>
                        @endif
                    @endauth
                    <a href="{{ route('blog.index') }}" class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-bold text-slate-700 hover:bg-slate-50 transition">
                        &larr; Tous les articles
                    </a>
                </div>
            </div>

            <!-- Corps principal de l'article -->
            <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <!-- En-tête de l'article -->
                <div class="p-6 sm:p-10 border-b border-slate-100">
                    <div class="flex flex-wrap items-center gap-2 mb-3">
                        <span class="rounded-md border px-2.5 py-0.5 text-xs font-extrabold uppercase tracking-wide {{ $article->category_badge_classes }}">
                            {{ $article->category }}
                        </span>
                        <span class="text-xs text-slate-400">&bull;</span>
                        <span class="text-xs font-semibold text-slate-500">Temps de lecture : {{ $article->reading_time }} min</span>
                        <span class="text-xs text-slate-400">&bull;</span>
                        <span class="text-xs font-semibold text-slate-500">{{ $article->views_count }} vues</span>
                    </div>

                    <h1 class="text-2xl sm:text-4xl font-black text-slate-950 leading-tight tracking-tight">
                        {{ $article->title }}
                    </h1>

                    <div class="mt-6 flex flex-wrap items-center justify-between gap-4 pt-6 border-t border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-full bg-[#7D5A50] text-white flex items-center justify-center font-bold text-sm shadow-xs">
                                {{ substr($article->author_name, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-xs sm:text-sm font-bold text-slate-950">{{ $article->author_name }}</p>
                                <p class="text-[11px] text-slate-500">Publié le {{ optional($article->published_at)->format('d F Y') }}</p>
                            </div>
                        </div>

                        <!-- Boutons de partage rapide -->
                        <div class="flex items-center gap-2">
                            <a href="https://wa.me/?text={{ $shareText }}" target="_blank" rel="noopener"
                               class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-emerald-700 transition shadow-2xs">
                                <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                                <span>Partager sur WhatsApp</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Grande Image de couverture -->
                <div class="relative w-full aspect-[16/9] sm:aspect-[21/9] bg-slate-900 overflow-hidden">
                    <img src="{{ $article->image_url }}" alt="{{ $article->title }}"
                         class="h-full w-full object-cover">
                </div>

                <!-- Contenu Rédactionnel -->
                <div class="p-6 sm:p-10 lg:p-12 space-y-6">
                    <!-- Chapeau / Résumé en gras -->
                    <div class="rounded-xl border-l-4 border-[#7D5A50] bg-[#7D5A50]/5 p-5 text-sm sm:text-base font-semibold text-slate-800 leading-relaxed">
                        {{ $article->excerpt }}
                    </div>

                    <!-- Texte principal formaté -->
                    <div class="prose max-w-none text-slate-800 text-sm sm:text-base leading-relaxed space-y-4">
                        @php
                            $paragraphs = preg_split('/\r\n\r\n|\n\n/', $article->content);
                        @endphp
                        @foreach ($paragraphs as $para)
                            @php
                                $trimmed = trim($para);
                            @endphp
                            @if (str_starts_with($trimmed, '### '))
                                <h3 class="text-xl sm:text-2xl font-black text-slate-950 pt-4 pb-1 text-[#7D5A50] border-b border-slate-100">
                                    {{ substr($trimmed, 4) }}
                                </h3>
                            @elseif (str_starts_with($trimmed, '## '))
                                <h2 class="text-2xl sm:text-3xl font-black text-slate-950 pt-5 pb-2 text-[#7D5A50]">
                                    {{ substr($trimmed, 3) }}
                                </h2>
                            @elseif (str_starts_with($trimmed, '> '))
                                <blockquote class="border-l-4 border-[#7D5A50] bg-slate-50 p-4 rounded-r-lg text-slate-700 italic my-3">
                                    {!! nl2br(e(substr($trimmed, 2))) !!}
                                </blockquote>
                            @elseif (str_starts_with($trimmed, '- ') || str_starts_with($trimmed, '1. '))
                                <div class="bg-slate-50/60 rounded-lg p-4 border border-slate-100 space-y-1.5">
                                    {!! nl2br(e($trimmed)) !!}
                                </div>
                            @else
                                <p class="text-slate-700 leading-7">
                                    {!! nl2br(e($trimmed)) !!}
                                </p>
                            @endif
                        @endforeach
                    </div>

                    <!-- Signature et avertissement -->
                    <div class="mt-8 pt-6 border-t border-slate-200/80 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 text-xs text-slate-500">
                        <p>&copy; {{ date('Y') }} AgriKoom Sénégal &bull; Tous droits réservés.</p>
                        <a href="https://wa.me/?text={{ $shareText }}" target="_blank" class="font-bold text-[#7D5A50] hover:underline">
                            Partager cet article avec d'autres producteurs &rarr;
                        </a>
                    </div>
                </div>
            </article>

            <!-- Articles en relation -->
            @if ($relatedArticles->isNotEmpty())
                <section class="pt-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-black text-slate-950 sm:text-xl">Articles similaires dans la même filière</h2>
                        <a href="{{ route('blog.index', ['category' => $article->category]) }}" class="text-xs font-bold text-[#7D5A50] hover:underline">
                            Voir plus dans {{ $article->category }} &rarr;
                        </a>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-3">
                        @foreach ($relatedArticles as $related)
                            <a href="{{ route('blog.show', $related->slug) }}"
                               class="group flex flex-col justify-between rounded-xl border border-slate-200 bg-white p-4 shadow-2xs hover:border-[#7D5A50]/60 hover:shadow-md transition">
                                <div class="aspect-[16/10] overflow-hidden rounded-lg bg-slate-100 mb-3">
                                    <img src="{{ $related->image_url }}" alt="{{ $related->title }}"
                                         class="h-full w-full object-cover group-hover:scale-105 transition duration-300">
                                </div>
                                <span class="text-[10px] font-bold text-[#7D5A50] uppercase">{{ $related->category }}</span>
                                <h3 class="mt-1 text-xs sm:text-sm font-extrabold text-slate-950 group-hover:text-[#7D5A50] transition line-clamp-2 leading-snug">
                                    {{ $related->title }}
                                </h3>
                                <p class="mt-2 text-[11px] text-slate-500 font-medium">{{ optional($related->published_at)->format('d/m/Y') }} &bull; {{ $related->reading_time }} min</p>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- Bannière retour aux annonces -->
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h3 class="text-base font-black text-slate-950">Besoin de matériel ou de récoltes pour votre exploitation ?</h3>
                    <p class="mt-0.5 text-xs text-slate-600">Parcourez les centaines d'annonces vérifiées de tracteurs, semences, bétail et productions locales.</p>
                </div>
                <a href="{{ route('listings.index') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-[#7D5A50] px-4 py-2.5 text-xs font-bold text-white hover:bg-[#6b4d44] transition shrink-0 shadow-2xs">
                    <span>Consulter les annonces</span>
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

        </div>
    </div>
</x-app-layout>

