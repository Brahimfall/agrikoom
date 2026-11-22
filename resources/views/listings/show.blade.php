<x-app-layout>
    @php
        $phone = preg_replace('/\D+/', '', $listing->user->phone ?? '');
        $telPhone = preg_replace('/\s+/', '', $listing->user->phone ?? '');
        $whatsAppText = urlencode('Bonjour, je vous contacte depuis AgriKoom pour votre annonce: '.$listing->title);
    @endphp

    <div class="bg-slate-50 py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 rounded-md border border-[#7D5A50]/20 bg-[#7D5A50]/10 px-4 py-3 text-sm text-[#5a3f38]">
                    {{ session('status') }}
                </div>
            @endif

            <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <a href="{{ route('listings.index') }}#categories" class="text-sm font-bold text-[#7D5A50] hover:text-[#4a342e]">Retour au marketplace</a>
                    <div class="mt-3 flex flex-wrap items-center gap-2">
                        <span class="rounded-md px-2.5 py-1 text-xs font-bold text-white {{ $listing->type === 'offer' ? 'bg-[#7D5A50]' : 'bg-[#5d463c]' }}">{{ $listing->type === 'offer' ? 'Offre' : 'Demande' }}</span>
                        <span class="rounded-md bg-white px-2.5 py-1 text-xs font-bold text-slate-700 shadow-sm">{{ $listing->category->name }}</span>
                        @if ($listing->is_negotiable)
                            <span class="rounded-md bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-900">Prix negociable</span>
                        @endif
                    </div>
                    <h1 class="mt-3 max-w-3xl text-3xl font-bold leading-tight text-slate-950">{{ $listing->title }}</h1>
                </div>

                @auth
                    @if (auth()->id() === $listing->user_id || auth()->user()?->is_admin)
                        <div class="flex items-center gap-2">
                            @if (auth()->user()?->is_admin && auth()->id() !== $listing->user_id)
                                <span class="rounded-full bg-[#7D5A50]/10 border border-[#7D5A50]/20 px-2.5 py-1 text-[11px] font-bold text-[#7D5A50]">
                                    Mode Admin (Vendeur : {{ $listing->user->name ?? 'Tierce' }})
                                </span>
                            @endif
                            <a href="{{ route('listings.edit', $listing) }}" class="inline-flex min-h-10 items-center justify-center gap-1.5 rounded-lg border border-slate-300 bg-white px-4 text-xs font-bold text-slate-800 hover:bg-slate-50 shadow-2xs">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                <span>Modifier l'annonce</span>
                            </a>
                        </div>
                    @endif
                @endauth
            </div>

            <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_380px]">
                <section class="space-y-6">
                    <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-2xs">
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
                            $fallbackShow = asset('storage/listings/demo/'.($categoryFallbacks[$listing->category->name ?? ''] ?? 'agri-products.png'));
                        @endphp
                        @if ($listing->photos->isNotEmpty())
                            <div class="grid gap-2 p-1.5 sm:p-2 sm:grid-cols-2">
                                @foreach ($listing->photos as $photo)
                                    <img src="{{ Storage::url($photo->path) }}" alt="{{ $listing->title }}"
                                        onerror="this.onerror=null;this.src='{{ $fallbackShow }}';"
                                        class="h-64 sm:h-80 w-full rounded object-cover">
                                @endforeach
                            </div>
                        @else
                            <img src="{{ $fallbackShow }}" alt="{{ $listing->title }}" class="h-64 sm:h-80 w-full object-cover">
                        @endif
                    </div>

                    <div class="rounded-md border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 class="text-lg font-bold text-slate-950">Description du produit</h2>
                        <p class="mt-3 whitespace-pre-line leading-7 text-slate-700">{{ $listing->description ?: 'Aucune description ajoutee pour cette annonce.' }}</p>
                    </div>
                </section>

                <aside class="space-y-5 lg:sticky lg:top-28 lg:self-start">
                    <section class="rounded-md border border-slate-200 bg-white p-5 shadow-sm">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Prix</p>
                        <p class="mt-1 text-3xl font-extrabold text-slate-950">{{ $listing->price ? number_format((float) $listing->price, 0, ',', ' ').' FCFA' : 'A negocier' }}</p>

                        <dl class="mt-5 grid grid-cols-2 gap-3 text-sm">
                            <div class="rounded-md bg-slate-50 p-3">
                                <dt class="text-slate-500">Quantite</dt>
                                <dd class="mt-1 font-bold text-slate-900">{{ $listing->quantity ? rtrim(rtrim($listing->quantity, '0'), '.').' '.$listing->unit : 'A preciser' }}</dd>
                            </div>
                            <div class="rounded-md bg-slate-50 p-3">
                                <dt class="text-slate-500">Localite</dt>
                                <dd class="mt-1 font-bold text-slate-900">{{ $listing->location }}</dd>
                            </div>
                            <div class="rounded-md bg-slate-50 p-3">
                                <dt class="text-slate-500">Produit</dt>
                                <dd class="mt-1 font-bold text-slate-900">{{ $listing->product }}</dd>
                            </div>
                            <div class="rounded-md bg-slate-50 p-3">
                                <dt class="text-slate-500">Disponibilité</dt>
                                <dd class="mt-1">
                                    @if ($listing->is_available_immediately)
                                        <span class="inline-flex items-center gap-1.5 font-bold text-emerald-700">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                            Immédiate (En stock)
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 font-bold text-amber-700">
                                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                            À partir du {{ $listing->available_from->format('d/m/Y') }}
                                        </span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </section>

                    <section class="rounded-md border border-slate-200 bg-white p-5 shadow-sm">
                        <h2 class="text-lg font-bold text-slate-950">Vendeur</h2>
                        <div class="mt-4 rounded-md bg-slate-50 p-4">
                            <p class="font-bold text-slate-950">{{ $listing->user->name }}</p>
                            <p class="mt-1 text-sm text-slate-600">{{ $listing->user->location ?? 'Localisation non renseignee' }}</p>
                            <p class="mt-1 text-sm font-semibold {{ $listing->user->phone_verified_at ? 'text-[#7D5A50]' : 'text-amber-700' }}">
                                {{ $listing->user->phone_verified_at ? 'Telephone verifie' : 'Telephone non verifie' }}
                            </p>
                        </div>

                        <div class="mt-4 grid gap-2">
                            @if ($phone)
                                <a href="https://wa.me/{{ $phone }}?text={{ $whatsAppText }}" target="_blank" rel="noopener"
                                   data-track-listing="{{ $listing->id }}" data-track-channel="whatsapp"
                                   class="inline-flex min-h-12 items-center justify-center gap-2 rounded-md px-4 text-sm font-bold text-white shadow-sm transition hover:opacity-90 active:scale-[0.98]"
                                   style="background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);">
                                    <svg class="h-4 w-4" viewBox="0 0 175.216 175.552" xmlns="http://www.w3.org/2000/svg" fill="white">
                                        <path d="M87.44 0C38.986 0 .215 38.683.215 87.03c0 15.323 3.996 30.278 11.602 43.42L.001 175.552l46.3-12.14C59.126 170.68 73.14 174.19 87.44 174.19c48.455 0 87.776-38.683 87.776-87.03C175.216 38.683 135.895 0 87.44 0zm0 160.49c-13.457 0-26.636-3.62-38.07-10.46l-2.73-1.62-28.282 7.414 7.56-27.59-1.774-2.828C17.6 114.218 13.825 100.84 13.825 87.03c0-40.593 33.027-73.62 73.615-73.62 40.59 0 73.616 33.027 73.616 73.62 0 40.59-33.027 73.46-73.616 73.46z"/>
                                        <path d="M129.08 104.918c-2.21-1.106-13.09-6.456-15.117-7.19-2.028-.737-3.504-1.106-4.98 1.106-1.476 2.21-5.718 7.19-7.011 8.666-1.292 1.476-2.584 1.66-4.797.554-2.21-1.106-9.335-3.44-17.78-10.975-6.572-5.868-11.013-13.113-12.305-15.323-1.292-2.21-.137-3.408 .97-4.51 .993-.993 2.21-2.585 3.317-3.878 1.106-1.292 1.476-2.21 2.21-3.686.737-1.476.37-2.769-.184-3.878-.554-1.106-4.98-12.027-6.826-16.46-1.797-4.325-3.624-3.737-4.98-3.804-1.292-.062-2.769-.075-4.244-.075-1.476 0-3.878.554-5.903 2.769-2.028 2.21-7.747 7.57-7.747 18.49s7.932 21.443 9.038 22.919c1.106 1.476 15.606 23.84 37.825 33.43 5.286 2.284 9.414 3.646 12.63 4.667 5.306 1.687 10.137 1.448 13.955.877 4.257-.636 13.09-5.351 14.937-10.516 1.845-5.165 1.845-9.593 1.291-10.516-.554-.922-2.028-1.476-4.24-2.582z"/>
                                    </svg>
                                    Contacter sur WhatsApp
                                </a>
                                <a href="tel:{{ $telPhone }}"
                                   data-track-listing="{{ $listing->id }}" data-track-channel="phone"
                                   class="inline-flex min-h-11 items-center justify-center rounded-md border border-slate-300 px-4 text-sm font-bold text-slate-800 hover:bg-slate-50">Appeler le vendeur</a>
                            @endif
                        </div>
                    </section>

                    <section class="rounded-md border border-slate-200 bg-white p-5 shadow-sm">
                        <h2 class="text-lg font-bold text-slate-950">Envoyer une demande</h2>
                        @auth
                            @if (auth()->id() !== $listing->user_id)
                                <form method="POST" action="{{ route('listings.contact', $listing) }}" class="mt-4 space-y-4">
                                    @csrf
                                    <div>
                                        <label for="buyer_phone" class="block text-sm font-semibold text-slate-700">Votre telephone</label>
                                        <input id="buyer_phone" name="buyer_phone" value="{{ old('buyer_phone', auth()->user()->phone) }}" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                                        <x-input-error :messages="$errors->get('buyer_phone')" class="mt-2" />
                                    </div>
                                    <div>
                                        <label for="message" class="block text-sm font-semibold text-slate-700">Message</label>
                                        <textarea id="message" name="message" rows="4" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-[#7D5A50] focus:ring-[#7D5A50]">{{ old('message') }}</textarea>
                                        <x-input-error :messages="$errors->get('message')" class="mt-2" />
                                    </div>
                                    <button class="w-full min-h-11 rounded-md bg-slate-950 px-4 text-sm font-bold text-white hover:bg-slate-800">Envoyer la demande</button>
                                </form>
                            @else
                                <p class="mt-3 text-sm text-slate-600">Ceci est votre annonce.</p>
                            @endif
                        @else
                            <p class="mt-3 text-sm text-slate-600">Connectez-vous pour envoyer une demande de contact au vendeur.</p>
                            <a href="{{ route('login') }}" class="mt-4 inline-flex min-h-11 items-center rounded-md bg-slate-950 px-4 text-sm font-bold text-white hover:bg-slate-800">Connexion</a>
                        @endauth
                    </section>
                </aside>
            </div>
        </div>
    </div>
</x-app-layout>
