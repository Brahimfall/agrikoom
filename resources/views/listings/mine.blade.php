<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-slate-950">Mes annonces</h1>
                <p class="mt-1 text-sm text-slate-600">Suivez vos annonces actives, vendues ou expirees.</p>
            </div>
            <a href="{{ route('listings.create') }}" class="inline-flex items-center justify-center rounded-md bg-[#7D5A50] px-4 py-2 text-sm font-semibold text-white hover:bg-[#6b4d44]">
                Publier
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 rounded-md border border-[#7D5A50]/20 bg-[#7D5A50]/10 px-4 py-3 text-sm text-[#5a3f38]">
                    {{ session('status') }}
                </div>
            @endif

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="divide-y divide-slate-100">
                    @php
                        $categoryFallbacks = [
                            'Cereales' => 'riz.png',
                            'Legumes' => 'tomates.png',
                            'Elevage' => 'elevage.jpg',
                            'Materiel agricole' => 'materiel.jpg',
                            'Intrants' => 'intrants.png',
                            'Fruits' => 'mangues.png',
                            'Legumineuses' => 'agri-products.png',
                            'Produits horticoles' => 'mangues.png',
                            'Tubercules' => 'manioc.png',
                        ];
                    @endphp
                    @forelse ($listings as $listing)
                        @php
                            $firstPhoto = $listing->photos->first();
                            $fallbackFile = $categoryFallbacks[$listing->category->name ?? ''] ?? 'agri-products.png';
                            $imgSrc = $firstPhoto ? Storage::url($firstPhoto->path) : asset('storage/listings/demo/'.$fallbackFile);
                            $fallbackSrc = asset('storage/listings/demo/'.$fallbackFile);
                        @endphp
                        <div class="flex flex-col gap-3 p-3.5 sm:p-5 lg:flex-row lg:items-center lg:justify-between">
                            <div class="flex flex-row gap-3 min-w-0">
                                <a href="{{ route('listings.show', $listing) }}" class="relative block w-28 sm:w-32 aspect-[4/3] shrink-0 overflow-hidden rounded bg-slate-100 border border-slate-200">
                                    <img src="{{ $imgSrc }}" alt="{{ $listing->title }}"
                                        onerror="this.onerror=null;this.src='{{ $fallbackSrc }}';"
                                        class="h-full w-full object-cover">
                                    <span class="absolute bottom-1 left-1/2 -translate-x-1/2 flex items-center gap-1 rounded bg-black/70 px-1.5 py-0.5 text-[9px] font-bold text-white shadow-xs">
                                        <svg class="h-2.5 w-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        {{ max(1, $listing->photos->count()) }}
                                    </span>
                                </a>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <h2 class="text-sm sm:text-base font-bold text-slate-950 line-clamp-1">
                                            <a href="{{ route('listings.show', $listing) }}" class="hover:text-[#7D5A50]">{{ $listing->title }}</a>
                                        </h2>
                                        <span class="rounded-full bg-slate-100 px-2 py-0.2 text-[10px] font-semibold text-slate-700">{{ $listing->status }}</span>
                                        <span class="rounded-full bg-[#7D5A50]/10 px-2 py-0.2 text-[10px] font-semibold text-[#7D5A50]">{{ $listing->type === 'offer' ? 'Offre' : 'Demande' }}</span>
                                    </div>
                                    <p class="mt-0.5 text-xs text-slate-600 font-medium truncate">{{ $listing->product }} &bull; {{ $listing->location }} &bull; {{ $listing->category->name }}</p>
                                    <p class="mt-0.5 text-[11px] text-slate-500">Expire {{ optional($listing->expires_at)->diffForHumans() ?? 'sans date' }}</p>
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('listings.edit', $listing) }}" class="rounded-md border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Modifier</a>
                                <form method="POST" action="{{ route('listings.sold', $listing) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="rounded-md border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Vendue</button>
                                </form>
                                <form method="POST" action="{{ route('listings.renew', $listing) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="rounded-md border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Renouveler</button>
                                </form>
                                <form method="POST" action="{{ route('listings.destroy', $listing) }}" onsubmit="return confirm('Supprimer cette annonce ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700">Supprimer</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center">
                            <p class="font-medium text-slate-950">Vous n'avez pas encore d'annonce.</p>
                            <a href="{{ route('listings.create') }}" class="mt-4 inline-flex rounded-md bg-[#7D5A50] px-4 py-2 text-sm font-semibold text-white hover:bg-[#6b4d44]">Publier maintenant</a>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="mt-6">
                {{ $listings->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
