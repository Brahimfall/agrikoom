<x-app-layout>
    <section class="border-b border-slate-200 bg-white">
        <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
            <a href="{{ route('jobs.index') }}" class="text-sm font-bold text-[#7D5A50] hover:text-[#5a3f38]">Retour aux offres d'emploi</a>

            <div class="mt-5 grid gap-6 lg:grid-cols-[minmax(0,1fr)_280px]">
                <div>
                    <p class="text-sm font-extrabold uppercase tracking-wide text-[#7D5A50]">Offre d'emploi</p>
                    <h1 class="mt-2 text-3xl font-black leading-tight text-slate-950">{{ $job->title }}</h1>
                    <p class="mt-3 text-base font-semibold text-slate-600">{{ $job->company ?? 'Entreprise non renseignee' }}</p>
                </div>

                <aside class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                    <dl class="space-y-3 text-sm">
                        @if ($job->location)
                            <div>
                                <dt class="font-bold text-slate-500">Lieu</dt>
                                <dd class="mt-1 font-semibold text-slate-950">{{ $job->location }}</dd>
                            </div>
                        @endif
                        @if ($job->contract_type)
                            <div>
                                <dt class="font-bold text-slate-500">Type</dt>
                                <dd class="mt-1 font-semibold text-slate-950">{{ $job->contract_type }}</dd>
                            </div>
                        @endif
                        @if ($job->deadline)
                            <div>
                                <dt class="font-bold text-slate-500">Date limite</dt>
                                <dd class="mt-1 font-semibold text-slate-950">{{ $job->deadline->format('d/m/Y') }}</dd>
                            </div>
                        @endif
                    </dl>

                    <div class="mt-5 space-y-2">
                        @if ($job->apply_email)
                            <a href="mailto:{{ $job->apply_email }}?subject=Candidature - {{ rawurlencode($job->title) }}" class="flex min-h-11 items-center justify-center rounded-md bg-[#7D5A50] px-4 text-sm font-black text-white hover:bg-[#6b4d44]">Postuler par email</a>
                            <p class="break-all text-xs font-semibold text-slate-500">{{ $job->apply_email }}</p>
                        @endif
                        @if ($job->apply_url)
                            <a href="{{ $job->apply_url }}" target="_blank" rel="noopener" class="flex min-h-11 items-center justify-center rounded-md {{ $job->apply_email ? 'border border-slate-300 bg-white text-slate-700 hover:bg-slate-50' : 'bg-[#7D5A50] text-white hover:bg-[#6b4d44]' }} px-4 text-sm font-black">Postuler sur le site</a>
                        @endif
                    </div>
                </aside>
            </div>
        </div>
    </section>

    <section class="bg-slate-50 py-8">
        <div class="mx-auto grid max-w-5xl gap-6 px-4 sm:px-6 lg:grid-cols-[minmax(0,1fr)_280px] lg:px-8">
            <article class="space-y-5 rounded-lg border border-slate-200 bg-white p-6">
                <div>
                    <h2 class="text-lg font-black text-slate-950">Resume du poste</h2>
                    <p class="mt-3 text-sm leading-7 text-slate-700">{{ $job->summary }}</p>
                </div>

                @if ($job->details)
                    <div>
                        <h2 class="text-lg font-black text-slate-950">Missions principales</h2>
                        <p class="mt-3 text-sm leading-7 text-slate-700">{{ $job->details }}</p>
                    </div>
                @endif

                @if ($job->profile)
                    <div>
                        <h2 class="text-lg font-black text-slate-950">Profil recherche</h2>
                        <p class="mt-3 text-sm leading-7 text-slate-700">{{ $job->profile }}</p>
                    </div>
                @endif
            </article>

            <aside class="space-y-3">
                <h2 class="text-sm font-black uppercase tracking-wide text-slate-500">Autres offres</h2>
                @foreach ($relatedJobs as $relatedJob)
                    <a href="{{ route('jobs.show', $relatedJob) }}" class="block rounded-lg border border-slate-200 bg-white p-4 hover:border-[#7D5A50]/50 hover:bg-[#7D5A50]/5">
                        <p class="font-black text-slate-950">{{ $relatedJob->title }}</p>
                        <p class="mt-1 text-sm font-semibold text-slate-500">{{ $relatedJob->company }}</p>
                    </a>
                @endforeach
            </aside>
        </div>
    </section>
</x-app-layout>
