<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-[#7D5A50]">Super admin</p>
                <h1 class="mt-1 text-2xl font-bold text-slate-950">Offres d'emploi</h1>
                <p class="mt-1 text-sm text-slate-500">Ajoutez et pilotez les opportunites visibles sur AgriKoom.</p>
            </div>
            <a href="{{ route('admin.jobs.create') }}" class="inline-flex min-h-10 items-center justify-center rounded-md bg-[#7D5A50] px-4 text-sm font-bold text-white hover:bg-[#6b4d44]">
                Ajouter une offre
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-5 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-md border border-[#7D5A50]/30 bg-[#7D5A50]/10 px-4 py-3 text-sm font-semibold text-[#5a3f38]">
                    {{ session('status') }}
                </div>
            @endif

            <div class="rounded-lg border border-slate-200 bg-white p-4">
                <form method="GET" action="{{ route('admin.jobs.index') }}" class="grid gap-3 sm:grid-cols-[minmax(0,1fr)_120px]">
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Rechercher par poste, entreprise ou lieu"
                        class="min-h-11 rounded-md border-slate-300 text-sm shadow-sm focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                    <button class="min-h-11 rounded-md border border-slate-300 bg-white px-4 text-sm font-bold text-slate-700 hover:bg-slate-50">
                        Filtrer
                    </button>
                </form>
            </div>

            <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-5 py-3">Poste</th>
                                <th class="px-5 py-3">Entreprise</th>
                                <th class="px-5 py-3">Lieu</th>
                                <th class="px-5 py-3">Statut</th>
                                <th class="px-5 py-3">Date limite</th>
                                <th class="px-5 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($jobs as $job)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-5 py-4">
                                        <a href="{{ route('jobs.show', $job) }}" class="font-black text-slate-950 hover:text-[#7D5A50]">{{ $job->title }}</a>
                                        @if ($job->is_featured)
                                            <span class="ml-2 rounded bg-amber-50 px-2 py-0.5 text-[11px] font-bold text-amber-800">Mis en avant</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 font-semibold text-slate-700">{{ $job->company ?? '-' }}</td>
                                    <td class="px-5 py-4 text-slate-600">{{ $job->location ?? '-' }}</td>
                                    <td class="px-5 py-4">
                                        <span class="rounded-md px-2.5 py-1 text-xs font-bold {{ $job->status === 'active' ? 'bg-[#7D5A50]/10 text-[#5a3f38]' : ($job->status === 'draft' ? 'bg-slate-100 text-slate-700' : 'bg-rose-50 text-rose-800') }}">
                                            {{ $job->status === 'active' ? 'Active' : ($job->status === 'draft' ? 'Brouillon' : 'Fermee') }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 font-semibold text-slate-700">{{ $job->deadline?->format('d/m/Y') ?? '-' }}</td>
                                    <td class="px-5 py-4">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('admin.jobs.edit', $job) }}" class="rounded-md border border-slate-300 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50">Modifier</a>
                                            <form method="POST" action="{{ route('admin.jobs.destroy', $job) }}" onsubmit="return confirm('Supprimer cette offre ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="rounded-md border border-rose-200 px-3 py-2 text-xs font-bold text-rose-700 hover:bg-rose-50">Supprimer</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-8 text-center text-sm text-slate-500">Aucune offre d'emploi trouvee.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{ $jobs->links() }}
        </div>
    </div>
</x-app-layout>
