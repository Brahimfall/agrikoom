<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-[#7D5A50]">Super admin</p>
                <h1 class="mt-1 text-2xl font-bold text-slate-950">{{ $job->exists ? 'Modifier une offre' : 'Ajouter une offre d\'emploi' }}</h1>
                <p class="mt-1 text-sm text-slate-500">Les offres actives sont visibles publiquement dans la section Emplois.</p>
            </div>
            <a href="{{ route('admin.jobs.index') }}" class="inline-flex min-h-10 items-center justify-center rounded-md border border-slate-300 bg-white px-4 text-sm font-bold text-slate-700 hover:bg-slate-50">
                Retour
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ $job->exists ? route('admin.jobs.update', $job) : route('admin.jobs.store') }}" class="space-y-6 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                @csrf
                @if ($job->exists)
                    @method('PATCH')
                @endif

                <div class="grid gap-5 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label for="title" class="block text-sm font-bold text-slate-700">Titre du poste</label>
                        <input id="title" name="title" value="{{ old('title', $job->title) }}" required
                            class="mt-1 block min-h-11 w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                        @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="company" class="block text-sm font-bold text-slate-700">Entreprise</label>
                        <input id="company" name="company" value="{{ old('company', $job->company) }}"
                            class="mt-1 block min-h-11 w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                        @error('company') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="location" class="block text-sm font-bold text-slate-700">Lieu</label>
                        <input id="location" name="location" value="{{ old('location', $job->location) }}"
                            class="mt-1 block min-h-11 w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                        @error('location') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="contract_type" class="block text-sm font-bold text-slate-700">Type de contrat</label>
                        <input id="contract_type" name="contract_type" value="{{ old('contract_type', $job->contract_type) }}" placeholder="CDD, CDI, stage, projet..."
                            class="mt-1 block min-h-11 w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                        @error('contract_type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="deadline" class="block text-sm font-bold text-slate-700">Date limite</label>
                        <input id="deadline" name="deadline" type="date" value="{{ old('deadline', $job->deadline?->format('Y-m-d')) }}"
                            class="mt-1 block min-h-11 w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                        @error('deadline') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="apply_email" class="block text-sm font-bold text-slate-700">Email de candidature</label>
                        <input id="apply_email" name="apply_email" type="email" value="{{ old('apply_email', $job->apply_email) }}"
                            class="mt-1 block min-h-11 w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                        @error('apply_email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="apply_url" class="block text-sm font-bold text-slate-700">Lien de candidature</label>
                        <input id="apply_url" name="apply_url" type="url" value="{{ old('apply_url', $job->apply_url) }}"
                            class="mt-1 block min-h-11 w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                        @error('apply_url') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-bold text-slate-700">Statut</label>
                        <select id="status" name="status" class="mt-1 block min-h-11 w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                            <option value="active" @selected(old('status', $job->status) === 'active')>Active</option>
                            <option value="draft" @selected(old('status', $job->status) === 'draft')>Brouillon</option>
                            <option value="closed" @selected(old('status', $job->status) === 'closed')>Fermee</option>
                        </select>
                        @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <label class="flex min-h-11 items-center gap-3 rounded-md border border-slate-200 bg-slate-50 px-3 text-sm font-bold text-slate-700">
                        <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $job->is_featured)) class="rounded border-slate-300 text-[#7D5A50] focus:ring-[#7D5A50]">
                        Mettre en avant
                    </label>
                </div>

                <div>
                    <label for="summary" class="block text-sm font-bold text-slate-700">Resume court</label>
                    <textarea id="summary" name="summary" rows="3" required
                        class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-[#7D5A50] focus:ring-[#7D5A50]">{{ old('summary', $job->summary) }}</textarea>
                    @error('summary') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="details" class="block text-sm font-bold text-slate-700">Missions principales</label>
                    <textarea id="details" name="details" rows="5"
                        class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-[#7D5A50] focus:ring-[#7D5A50]">{{ old('details', $job->details) }}</textarea>
                    @error('details') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="profile" class="block text-sm font-bold text-slate-700">Profil recherche</label>
                    <textarea id="profile" name="profile" rows="5"
                        class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-[#7D5A50] focus:ring-[#7D5A50]">{{ old('profile', $job->profile) }}</textarea>
                    @error('profile') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex flex-wrap justify-end gap-3 border-t border-slate-200 pt-5">
                    <a href="{{ route('admin.jobs.index') }}" class="inline-flex min-h-11 items-center rounded-md border border-slate-300 px-4 text-sm font-bold text-slate-700 hover:bg-slate-50">Annuler</a>
                    <button class="inline-flex min-h-11 items-center rounded-md bg-[#7D5A50] px-5 text-sm font-black text-white hover:bg-[#6b4d44]">
                        {{ $job->exists ? 'Enregistrer' : 'Ajouter l\'offre' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
