<x-app-layout>
    <section class="border-b border-slate-200 bg-white">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_340px] lg:items-end">
                <div>
                    <p class="text-sm font-extrabold uppercase tracking-wide text-[#7D5A50]">Emploi agricole</p>
                    <h1 class="mt-2 text-3xl font-black text-slate-950">Offres d'emploi AgriKoom</h1>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600">Opportunites agricoles, agroalimentaires, nutrition, terrain, production et commerce pour les profils du secteur.</p>
                </div>
                <div class="rounded-lg border border-[#7D5A50]/20 bg-[#7D5A50]/5 p-4">
                    <p class="text-3xl font-black text-slate-950">{{ number_format($jobCount, 0, ',', ' ') }}</p>
                    <p class="mt-1 text-sm font-bold text-[#5a3f38]">offres actives disponibles</p>
                </div>
            </div>

            <form method="GET" action="{{ route('jobs.index') }}" class="mt-6 rounded-lg border border-slate-200 bg-slate-50 p-3">
                <div class="grid gap-3 md:grid-cols-[minmax(0,1fr)_260px_120px]">
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Poste, entreprise, competence..."
                        class="min-h-11 rounded-md border-slate-300 text-sm shadow-sm focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                    <input type="text" name="location" value="{{ request('location') }}" placeholder="Lieu"
                        class="min-h-11 rounded-md border-slate-300 text-sm shadow-sm focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                    <button class="min-h-11 rounded-md bg-[#7D5A50] px-4 text-sm font-extrabold text-white hover:bg-[#6b4d44]">Filtrer</button>
                </div>
            </form>
        </div>
    </section>

    <section class="bg-slate-50 py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-4 lg:grid-cols-2">
                @forelse ($jobs as $job)
                    <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <a href="{{ route('jobs.show', $job) }}" class="text-lg font-black text-slate-950 hover:text-[#7D5A50]">{{ $job->title }}</a>
                                <p class="mt-1 text-sm font-semibold text-slate-600">{{ $job->company ?? 'Entreprise non renseignee' }}</p>
                            </div>
                            @if ($job->deadline)
                                <span class="w-fit rounded-md bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-800">Avant {{ $job->deadline->format('d/m/Y') }}</span>
                            @endif
                        </div>

                        <div class="mt-4 flex flex-wrap gap-2 text-xs font-bold text-slate-600">
                            @if ($job->location)
                                <span class="rounded-md bg-slate-100 px-2.5 py-1">{{ $job->location }}</span>
                            @endif
                            @if ($job->contract_type)
                                <span class="rounded-md bg-slate-100 px-2.5 py-1">{{ $job->contract_type }}</span>
                            @endif
                        </div>

                        <p class="mt-4 line-clamp-3 text-sm leading-6 text-slate-600">{{ $job->summary }}</p>

                        <div class="mt-5 flex flex-wrap gap-2">
                            <a href="{{ route('jobs.show', $job) }}" class="inline-flex min-h-10 items-center rounded-md border border-slate-300 px-4 text-sm font-bold text-slate-700 hover:bg-slate-50">Voir details</a>
                            @if ($job->apply_email)
                                <a href="mailto:{{ $job->apply_email }}?subject=Candidature - {{ rawurlencode($job->title) }}" class="inline-flex min-h-10 items-center rounded-md bg-[#7D5A50] px-4 text-sm font-bold text-white hover:bg-[#6b4d44]">Postuler par email</a>
                            @elseif ($job->apply_url)
                                <a href="{{ $job->apply_url }}" target="_blank" rel="noopener" class="inline-flex min-h-10 items-center rounded-md bg-[#7D5A50] px-4 text-sm font-bold text-white hover:bg-[#6b4d44]">Postuler</a>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="rounded-lg border border-slate-200 bg-white p-8 text-center lg:col-span-2">
                        <p class="font-bold text-slate-950">Aucune offre ne correspond a votre recherche.</p>
                        <a href="{{ route('jobs.index') }}" class="mt-3 inline-flex text-sm font-bold text-[#7D5A50] hover:text-[#5a3f38]">Voir toutes les offres</a>
                    </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $jobs->links() }}
            </div>
        </div>
    </section>
</x-app-layout>
