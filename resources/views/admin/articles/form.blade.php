<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <a href="{{ route('admin.articles.index') }}" class="inline-flex items-center text-xs font-bold text-[#7D5A50] hover:text-[#5a3f38] mb-1">
                    <svg class="mr-1 h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                    Retour aux articles
                </a>
                <h1 class="text-2xl font-black text-slate-950">
                    {{ $article->exists ? 'Modifier l\'article' : 'Rédiger un nouvel article de blog' }}
                </h1>
                <p class="mt-1 text-xs text-slate-500">Partagez vos conseils, actualités et guides pratiques avec la communauté agricole.</p>
            </div>
            @if ($article->exists)
                <div class="flex items-center gap-2">
                    <a href="{{ route('blog.show', $article->slug) }}" target="_blank"
                       class="rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                        Aperçu en direct
                    </a>
                    <form method="POST" action="{{ route('admin.articles.destroy', $article) }}" onsubmit="return confirm('Confirmer la suppression ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded-lg border border-rose-200 bg-rose-50 px-3.5 py-2 text-xs font-bold text-rose-700 hover:bg-rose-100 transition">
                            Supprimer
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-8 bg-slate-50 min-h-screen">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <form method="POST"
                  action="{{ $article->exists ? route('admin.articles.update', $article) : route('admin.articles.store') }}"
                  enctype="multipart/form-data"
                  class="space-y-6">
                @csrf
                @if ($article->exists)
                    @method('PATCH')
                @endif

                <!-- Card 1: Titre & Informations de base -->
                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-2xs space-y-4">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 border-b border-slate-100 pb-2">
                        1. Titre &amp; Filière
                    </h2>

                    <div>
                        <label for="title" class="block text-xs font-bold text-slate-700 mb-1">Titre de l'article <span class="text-rose-500">*</span></label>
                        <input id="title" name="title" type="text" required
                               value="{{ old('title', $article->title) }}"
                               placeholder="Ex: Guide pratique : Réussir la culture de l'oignon dans les Niayes"
                               class="w-full rounded-lg border-slate-300 text-base font-extrabold text-slate-950 shadow-xs focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                        @error('title') <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid gap-4 sm:grid-cols-3">
                        <div>
                            <label for="category" class="block text-xs font-bold text-slate-700 mb-1">Catégorie / Filière <span class="text-rose-500">*</span></label>
                            <input id="category" name="category" type="text" list="categories-list" required
                                   value="{{ old('category', $article->category) }}"
                                   placeholder="Conseils & Pratiques, Machinisme..."
                                   class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                            <datalist id="categories-list">
                                <option value="Conseils & Pratiques">
                                <option value="Machinisme & Équipement">
                                <option value="Élevage & Bétail">
                                <option value="Irrigation & Eau">
                                <option value="Marchés & Économie">
                                <option value="Intrants & Semences">
                                <option value="Innovations & Tech">
                            </datalist>
                            @error('category') <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="author_name" class="block text-xs font-bold text-slate-700 mb-1">Nom de l'auteur <span class="text-rose-500">*</span></label>
                            <input id="author_name" name="author_name" type="text" required
                                   value="{{ old('author_name', $article->author_name) }}"
                                   placeholder="Rédaction AgriKoom"
                                   class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                            @error('author_name') <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="reading_time" class="block text-xs font-bold text-slate-700 mb-1">Temps de lecture (minutes)</label>
                            <input id="reading_time" name="reading_time" type="number" min="1" max="120"
                                   value="{{ old('reading_time', $article->reading_time ?? 5) }}"
                                   class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                            @error('reading_time') <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Card 2: Image de couverture & Résumé -->
                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-2xs space-y-4">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 border-b border-slate-100 pb-2">
                        2. Image de couverture &amp; Chapeau
                    </h2>

                    <div>
                        <label for="excerpt" class="block text-xs font-bold text-slate-700 mb-1">Chapeau / Résumé d'accroche <span class="text-rose-500">*</span></label>
                        <textarea id="excerpt" name="excerpt" rows="3" required
                                  placeholder="Bref résumé accrocheur affiché sur la liste et en haut de l'article (2 à 3 phrases)..."
                                  class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-[#7D5A50] focus:ring-[#7D5A50]">{{ old('excerpt', $article->excerpt) }}</textarea>
                        <p class="mt-1 text-[11px] text-slate-400">Ce texte résume l'article sur la page d'accueil et le blog.</p>
                        @error('excerpt') <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="image" class="block text-xs font-bold text-slate-700 mb-1">Image de couverture (JPEG, PNG, WebP — max 4 Mo)</label>
                        @if ($article->image_path)
                            <div class="mb-3 flex items-center gap-3 rounded-lg border border-slate-200 bg-slate-50 p-2.5">
                                <img src="{{ $article->image_url }}" alt="Aperçu" class="h-16 w-24 object-cover rounded">
                                <div class="text-xs text-slate-500">
                                    <p class="font-bold text-slate-700">Image actuelle :</p>
                                    <p class="text-[11px] truncate max-w-xs">{{ $article->image_path }}</p>
                                    <p class="text-[10px] text-slate-400">Sélectionnez un nouveau fichier pour la remplacer.</p>
                                </div>
                            </div>
                        @endif
                        <input id="image" name="image" type="file" accept="image/*"
                               class="w-full rounded-lg border border-slate-300 text-xs text-slate-700 file:mr-4 file:rounded-md file:border-0 file:bg-[#7D5A50]/10 file:px-4 file:py-2.5 file:text-xs file:font-bold file:text-[#7D5A50] hover:file:bg-[#7D5A50]/20">
                        @error('image') <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Card 3: Corps de l'article -->
                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-2xs space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800">
                            3. Contenu de l'article <span class="text-rose-500">*</span>
                        </h2>
                        <span class="text-xs text-slate-400">Markdown supporté (### Titre, > Citation, - Puces)</span>
                    </div>

                    <div>
                        <textarea id="content" name="content" rows="16" required
                                  placeholder="Rédigez ici le contenu de votre article...&#10;&#10;### 1. Premier point important&#10;Explications détaillées...&#10;&#10;> Astuce d'expert ou conseil clé&#10;&#10;### 2. Deuxième étape..."
                                  class="w-full font-mono rounded-lg border-slate-300 text-sm leading-relaxed shadow-xs focus:border-[#7D5A50] focus:ring-[#7D5A50]">{{ old('content', $article->content) }}</textarea>
                        @error('content') <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Card 4: Statut et Publication -->
                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-2xs space-y-4">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 border-b border-slate-100 pb-2">
                        4. Publication &amp; Visibilité
                    </h2>

                    <div class="grid gap-4 sm:grid-cols-3 items-center">
                        <div>
                            <label for="status" class="block text-xs font-bold text-slate-700 mb-1">Statut de diffusion</label>
                            <select id="status" name="status" class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                                <option value="published" @selected(old('status', $article->status) === 'published')>Publié en ligne</option>
                                <option value="draft" @selected(old('status', $article->status) === 'draft')>Brouillon (non visible)</option>
                                <option value="archived" @selected(old('status', $article->status) === 'archived')>Archivé</option>
                            </select>
                            @error('status') <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="published_at" class="block text-xs font-bold text-slate-700 mb-1">Date de publication</label>
                            <input id="published_at" name="published_at" type="datetime-local"
                                   value="{{ old('published_at', $article->published_at ? $article->published_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}"
                                   class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                            @error('published_at') <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p> @enderror
                        </div>

                        <div class="pt-3">
                            <label class="flex items-center gap-2.5 cursor-pointer">
                                <input type="checkbox" name="is_featured" value="1"
                                       @checked(old('is_featured', $article->is_featured))
                                       class="h-4 w-4 rounded border-slate-300 text-[#7D5A50] focus:ring-[#7D5A50]">
                                <span class="text-xs sm:text-sm font-bold text-slate-800">Mettre cet article "À la une"</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('admin.articles.index') }}"
                       class="rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50 transition">
                        Annuler
                    </a>
                    <button type="submit"
                            class="rounded-lg bg-[#7D5A50] px-6 py-2.5 text-sm font-extrabold text-white shadow-xs hover:bg-[#6b4d44] transition">
                        {{ $article->exists ? 'Enregistrer les modifications' : 'Publier l\'article' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

