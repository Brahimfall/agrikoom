<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.categories.index') }}" class="text-xs font-semibold text-[#7D5A50] hover:underline">&larr; Retour aux catégories</a>
                    <span class="text-xs text-slate-400">&bull;</span>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Super Admin</span>
                </div>
                <h1 class="mt-1 text-2xl font-black text-slate-950">
                    {{ $category->exists ? 'Modifier la catégorie : ' . $category->name : 'Ajouter une nouvelle catégorie' }}
                </h1>
                <p class="mt-1 text-sm text-slate-500">
                    Configurez le nom, la description et l'image d'illustration pour organiser les produits sur AgriKoom.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.categories.index') }}" class="inline-flex min-h-10 items-center rounded-md border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Annuler
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-50 min-h-screen">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">

            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-900 shadow-xs">
                    <p class="font-bold mb-1">Veuillez corriger les erreurs suivantes :</p>
                    <ul class="list-disc list-inside space-y-0.5 text-xs">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST"
                  action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}"
                  enctype="multipart/form-data"
                  class="space-y-6">
                @csrf
                @if ($category->exists)
                    @method('PATCH')
                @endif

                <!-- Bloc 1 : Informations Générales -->
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-2xs space-y-5">
                    <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">Informations de la catégorie</h2>

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label for="name" class="block text-sm font-bold text-slate-700">Nom de la catégorie <span class="text-red-500">*</span></label>
                            <input id="name" type="text" name="name" value="{{ old('name', $category->name) }}" required placeholder="Ex : Céréales, Élevage, Aviculture..." class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                            <p class="mt-1 text-xs text-slate-500">Le libellé exact qui apparaîtra sur les filtres et tuiles du site.</p>
                        </div>

                        <div>
                            <label for="slug" class="block text-sm font-bold text-slate-700">Identifiant URL (Slug)</label>
                            <input id="slug" type="text" name="slug" value="{{ old('slug', $category->slug) }}" placeholder="cereales, elevage-volaille..." class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-xs font-mono focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                            <p class="mt-1 text-xs text-slate-500">Optionnel : sera généré automatiquement si vous laissez vide.</p>
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-bold text-slate-700">Sous-titre / Description</label>
                        <input id="description" type="text" name="description" value="{{ old('description', $category->description) }}" placeholder="Ex : Oignons des Niayes, tomates, piments, carottes..." class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                        <p class="mt-1 text-xs text-slate-500">Affiché sous le nom de la catégorie sur la grille de la page d'accueil.</p>
                    </div>

                    <div class="pt-2">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->exists ? $category->is_active : true)) class="h-4 w-4 rounded border-slate-300 text-[#7D5A50] focus:ring-[#7D5A50]">
                            <div>
                                <span class="text-sm font-bold text-slate-800">Catégorie active et visible sur le site</span>
                                <p class="text-xs text-slate-500">Si décoché, la catégorie et ses filtres seront masqués des acheteurs.</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Bloc 2 : Image d'illustration -->
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-2xs space-y-5">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div>
                            <h2 class="text-base font-bold text-slate-900">Image d'illustration de la catégorie</h2>
                            <p class="text-xs text-slate-500">Utilisée sur les tuiles d'accueil et les en-têtes de catégorie.</p>
                        </div>
                        @if ($category->exists)
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-slate-400">Aperçu actuel :</span>
                                <img src="{{ $category->image_url }}" alt="Actuel" class="h-10 w-10 rounded-md object-cover border border-slate-200">
                            </div>
                        @endif
                    </div>

                    <!-- Choix 1 : Sélectionner une image prédéfinie -->
                    <div>
                        <label class="block text-sm font-bold text-slate-800 mb-2">Option A : Choisir parmi les photos haute définition disponibles</label>
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                            @foreach ($presets as $path => $label)
                                <label class="group relative block cursor-pointer rounded-xl border-2 p-1.5 transition hover:shadow-md {{ old('preset_image', $category->image) === $path ? 'border-[#7D5A50] bg-[#7D5A50]/5 ring-2 ring-[#7D5A50]/20' : 'border-slate-200 hover:border-slate-300' }}">
                                    <input type="radio" name="preset_image" value="{{ $path }}" @checked(old('preset_image', $category->image) === $path) class="sr-only">
                                    <div class="aspect-4/3 w-full overflow-hidden rounded-lg bg-slate-100">
                                        <img src="{{ asset($path) }}" alt="{{ $label }}" class="h-full w-full object-cover transition group-hover:scale-105">
                                    </div>
                                    <p class="mt-1.5 text-[11px] font-semibold text-slate-700 line-clamp-1 leading-tight">{{ $label }}</p>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Choix 2 : Téléverser un fichier personnalisé -->
                    <div class="pt-3 border-t border-slate-100">
                        <label for="image_file" class="block text-sm font-bold text-slate-800">Option B : Ou téléverser une photo personnalisée depuis votre appareil</label>
                        <input id="image_file" type="file" name="image_file" accept="image/*" class="mt-2 block w-full text-xs text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-[#7D5A50] file:px-3 file:py-2 file:text-xs file:font-bold file:text-white hover:file:bg-[#6b4d44]">
                        <p class="mt-1 text-xs text-slate-400">Formats acceptés : JPG, PNG, WEBP (Max : 3 Mo). Si vous téléversez une photo, elle remplace la photo prédéfinie.</p>
                    </div>

                    @if ($category->exists && $category->image)
                        <div class="pt-2">
                            <label class="inline-flex items-center gap-2 cursor-pointer text-xs text-red-600 hover:text-red-800">
                                <input type="checkbox" name="remove_image" value="1" class="rounded border-slate-300 text-red-600 focus:ring-red-500">
                                <span>Réinitialiser et supprimer l'image personnalisée (revenir au défaut)</span>
                            </label>
                        </div>
                    @endif
                </div>

                <!-- Information sur les annonces associées si modification -->
                @if ($category->exists)
                    <div class="rounded-xl border border-slate-200 bg-white p-4 text-xs text-slate-600 flex items-center justify-between">
                        <div>
                            <span>📦 <strong>{{ $listingsCount ?? $category->listings()->count() }}</strong> annonce(s) actuellement classée(s) dans cette catégorie.</span>
                        </div>
                        <a href="{{ route('listings.mine', ['category' => $category->id]) }}" class="font-bold text-[#7D5A50] hover:underline">
                            Gérer ces annonces &rarr;
                        </a>
                    </div>
                @endif

                <!-- Boutons d'action -->
                <div class="flex items-center justify-between pt-2">
                    <a href="{{ route('admin.categories.index') }}" class="rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        Annuler
                    </a>
                    <button type="submit" class="rounded-lg bg-[#7D5A50] px-6 py-2.5 text-sm font-bold uppercase tracking-wider text-white shadow-xs hover:bg-[#6b4d44] transition">
                        {{ $category->exists ? 'Mettre à jour la catégorie' : 'Créer la catégorie' }}
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
