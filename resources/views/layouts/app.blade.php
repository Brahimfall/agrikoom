<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'AgriKoom') }} — Petites annonces agricoles et matériel au Sénégal</title>
        <meta name="description" content="Plateforme de petites annonces pour le matériel agricole, les tracteurs, le bétail, les intrants et les récoltes au Sénégal. Contact direct acheteur-vendeur.">

        <!-- Fonts (Inter & Outfit) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <style>
            body {
                font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            }
        </style>

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="bg-white text-slate-900 antialiased selection:bg-[#7D5A50]/20 selection:text-slate-950 overflow-x-hidden">
        {{-- pt compense la hauteur du header fixe (barre utilitaire ~32px + nav ~64px + strip ~34px) --}}
        <div class="min-h-screen flex flex-col justify-between w-full overflow-x-hidden">
            <div class="pt-[112px] sm:pt-[130px]">
                @include('layouts.navigation')

                <!-- Page Heading -->
                @isset($header)
                    <header class="border-b border-slate-200 bg-white shadow-2xs">
                        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main>
                    {{ $slot }}
                </main>
            </div>

            <!-- Footer Professionnel -->
            <footer class="mt-12 border-t border-[#5a3f38] bg-[#4a342e] text-slate-300">
                <!-- Bandeau supérieur du footer -->
                <div class="border-b border-white/10 bg-[#3d2b26] py-4">
                    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <img src="{{ asset('assets/brand/agrikoom-logo-cropped.png') }}" alt="AgriKoom" class="h-7 w-auto rounded bg-white p-0.5">
                            <div>
                                <p class="text-xs font-bold text-white tracking-wide">AgriKoom Sénégal</p>
                                <p class="text-[11px] text-slate-400">Portail de petites annonces de matériel et productions agricoles</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2.5">
                            <a href="{{ route('listings.create') }}" class="rounded bg-[#7D5A50] px-3.5 py-1.5 text-xs font-bold uppercase tracking-wider text-white shadow-2xs hover:bg-[#6b4d44]">
                                Publier une annonce
                            </a>
                            <a href="https://wa.me/221774882987" target="_blank" class="rounded border border-white/20 bg-white/10 px-3 py-1.5 text-xs font-semibold text-white/80 hover:text-white">
                                WhatsApp : +221 77 488 29 87
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Liens structurés par catégories & régions -->
                <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 grid gap-8 sm:grid-cols-2 md:grid-cols-4 text-xs">
                    <!-- Matériel & Équipements -->
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-white">Matériel &amp; Équipements</h3>
                        <ul class="mt-2.5 space-y-1.5 text-slate-300">
                            <li><a href="{{ route('products.index', ['category' => 'materiel-agricole']) }}" class="hover:text-white">Tracteurs agricoles</a></li>
                            <li><a href="{{ route('products.index', ['search' => 'Motopompe']) }}" class="hover:text-white">Motopompes &amp; kits solaires</a></li>
                            <li><a href="{{ route('products.index', ['search' => 'Charrue']) }}" class="hover:text-white">Outils de travail du sol</a></li>
                            <li><a href="{{ route('products.index', ['search' => 'Semoir']) }}" class="hover:text-white">Semoirs et repiqueuses</a></li>
                            <li><a href="{{ route('products.index', ['category' => 'materiel-agricole']) }}" class="hover:text-white">Tous les matériels</a></li>
                        </ul>
                    </div>

                    <!-- Bétail & Récoltes -->
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-white">Élevage &amp; Récoltes</h3>
                        <ul class="mt-2.5 space-y-1.5 text-slate-300">
                            <li><a href="{{ route('products.index', ['category' => 'elevage']) }}" class="hover:text-white">Élevage, ovins &amp; bovins</a></li>
                            <li><a href="{{ route('products.index', ['category' => 'cereales']) }}" class="hover:text-white">Céréales &amp; riz de la vallée</a></li>
                            <li><a href="{{ route('products.index', ['category' => 'legumes']) }}" class="hover:text-white">Légumes &amp; oignons des Niayes</a></li>
                            <li><a href="{{ route('products.index', ['category' => 'intrants']) }}" class="hover:text-white">Engrais, intrants &amp; semences</a></li>
                            <li><a href="{{ route('products.index', ['category' => 'fruits']) }}" class="hover:text-white">Fruits du terroir</a></li>
                        </ul>
                    </div>

                    <!-- Régions du Sénégal -->
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-white">Annonces par Région</h3>
                        <ul class="mt-2.5 space-y-1.5 text-slate-300">
                            <li><a href="{{ route('listings.index', ['location' => 'Saint-Louis']) }}#categories" class="hover:text-white">Saint-Louis &amp; Dagana</a></li>
                            <li><a href="{{ route('listings.index', ['location' => 'Thiès']) }}#categories" class="hover:text-white">Thiès, Niayes &amp; Mbour</a></li>
                            <li><a href="{{ route('listings.index', ['location' => 'Dakar']) }}#categories" class="hover:text-white">Dakar &amp; Rufisque</a></li>
                            <li><a href="{{ route('listings.index', ['location' => 'Kaolack']) }}#categories" class="hover:text-white">Kaolack, Fatick &amp; Kaffrine</a></li>
                            <li><a href="{{ route('listings.index', ['location' => 'Ziguinchor']) }}#categories" class="hover:text-white">Ziguinchor, Kolda &amp; Sédhiou</a></li>
                        </ul>
                    </div>

                    <!-- À propos & Sécurité -->
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-white">Informations &amp; Sécurité</h3>
                        <ul class="mt-2.5 space-y-1.5 text-slate-300">
                            <li><a href="{{ route('blog.index') }}" class="hover:text-white">Blog &amp; Conseils agricoles</a></li>
                            <li><a href="{{ route('jobs.index') }}" class="hover:text-white">Offres d'emploi agricole</a></li>
                            <li><a href="{{ route('listings.index') }}" class="hover:text-white">Consulter toutes les annonces</a></li>
                            @auth
                                <li><a href="{{ route('listings.mine') }}" class="hover:text-white">Gérer mes annonces</a></li>
                            @else
                                <li><a href="{{ route('register') }}" class="hover:text-white">Créer un compte</a></li>
                                <li><a href="{{ route('login') }}" class="hover:text-white">Se connecter</a></li>
                            @endauth
                        </ul>
                        <div class="mt-3 rounded border border-white/10 bg-white/5 p-2.5 text-[11px] text-slate-300">
                            Contact direct acheteur-vendeur. Vérifiez systématiquement le matériel ou la récolte sur place avant tout règlement.
                        </div>
                    </div>
                </div>

                <!-- Copyright -->
                <div class="border-t border-white/10 py-3.5 text-center text-xs text-slate-400">
                    <p>&copy; {{ date('Y') }} AgriKoom Sénégal — Portail d'annonces de matériel agricole, bétail et productions locales.</p>
                </div>
            </footer>
        </div>
        <script>
            document.addEventListener('click', function(e) {
                const el = e.target.closest('[data-track-listing]');
                if (!el) return;
                const listingId = el.getAttribute('data-track-listing');
                const channel = el.getAttribute('data-track-channel') || 'whatsapp';
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (listingId) {
                    fetch('/annonces/' + listingId + '/track-contact?channel=' + encodeURIComponent(channel), {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        },
                        keepalive: true
                    }).catch(function() {});
                }
            });
        </script>
        @livewireScripts
    </body>
</html>
