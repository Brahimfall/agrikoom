<nav x-data="{ open: false }" class="fixed top-0 left-0 right-0 z-50 w-full border-b border-slate-200 bg-white shadow-sm">
    <!-- Top Utility Bar (Desktop only) -->
    <div class="hidden sm:block border-b border-[#5a3f38] bg-[#7D5A50] text-white/90">
        <div class="mx-auto flex h-8 max-w-7xl items-center justify-between px-4 text-xs sm:px-6 lg:px-8">
            <div class="flex items-center gap-4">
                <span class="flex items-center gap-1.5 font-semibold text-white">
                    <svg class="h-3.5 w-3.5 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Sénégal &amp; Sous-région
                </span>
                <span class="text-white/40">|</span>
                <span class="text-white/80">Devise : <strong>FCFA</strong></span>
                <span class="text-white/40">|</span>
                <a href="https://wa.me/221774882987" target="_blank" class="flex items-center gap-1.5 text-white/80 hover:text-white">
                    <svg class="h-3.5 w-3.5 text-white/80" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                    Assistance : +221 77 488 29 87
                </a>
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ route('blog.index') }}" class="hover:text-white {{ request()->routeIs('blog.*') ? 'font-bold underline' : '' }}">Blog &amp; Conseils</a>
                <span class="text-white/30">|</span>
                <a href="{{ route('jobs.index') }}" class="hover:text-white">Emplois agricoles</a>
                <span class="text-white/30">|</span>
                @auth
                    <a href="{{ route('listings.mine') }}" class="font-semibold text-white hover:underline">Mes annonces</a>
                    @if (Auth::user()->is_admin)
                        <a href="{{ route('admin.dashboard') }}" class="font-bold text-white/90 hover:underline">Administration</a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="hover:text-white">Connexion</a>
                    <a href="{{ route('register') }}" class="text-white hover:underline font-medium">Inscription</a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Main Navigation Bar -->
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 sm:h-16 items-center justify-between gap-3">
            <!-- Brand Logo (Discreet & Compact) -->
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                    <img src="{{ asset('assets/brand/agrikoom-logo-cropped.png') }}" alt="AgriKoom" class="h-8 w-auto sm:h-8 object-contain">
                    <span class="text-sm sm:text-base font-black tracking-tight text-[#7D5A50]">AGRIKOOM</span>
                </a>
            </div>

            <!-- Search Bar in Header (Desktop) -->
            <div class="hidden md:flex flex-1 max-w-md mx-3">
                <form method="GET" action="{{ route('listings.index') }}#categories" class="relative w-full">
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Tracteur, motopompe, semences, oignons..."
                        class="w-full rounded-md border border-slate-300 bg-slate-50 py-1.5 pl-3 pr-9 text-xs text-slate-900 placeholder-slate-400 transition focus:border-[#7D5A50] focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#7D5A50]">
                    <button type="submit" aria-label="Rechercher" class="absolute right-1 top-1 flex h-6 w-6 items-center justify-center rounded bg-[#7D5A50] text-white hover:bg-[#6b4d44]">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </button>
                </form>
            </div>

            <!-- Actions (Compact post ad button & account) -->
            <div class="hidden sm:flex sm:items-center sm:gap-2">
                <a href="{{ route('listings.create') }}" class="inline-flex items-center gap-1.5 rounded-md bg-[#7D5A50] px-3.5 py-1.5 text-xs font-bold uppercase tracking-wider text-white shadow-2xs transition hover:bg-[#6b4d44] active:scale-98">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    <span>Publier une annonce</span>
                </a>

                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center gap-1.5 rounded-md border border-slate-300 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">
                                <span class="max-w-[100px] truncate">{{ Auth::user()->name }}</span>
                                <svg class="h-3 w-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="border-b border-slate-100 px-4 py-2 text-xs text-slate-500">
                                Connecté : <strong class="text-slate-800">{{ Auth::user()->name }}</strong>
                            </div>
                            <x-dropdown-link :href="route('listings.mine')">{{ Auth::user()->is_admin ? '📦 Gestion des annonces' : 'Mes annonces' }}</x-dropdown-link>
                            <x-dropdown-link :href="route('profile.edit')">Paramètres</x-dropdown-link>
                            @if (Auth::user()->is_admin)
                                <div class="border-t border-slate-100"></div>
                                <x-dropdown-link :href="route('admin.dashboard')" class="font-bold text-[#7D5A50]">Administration</x-dropdown-link>
                                <x-dropdown-link :href="route('admin.mercuriale.index')">Prix du marché</x-dropdown-link>
                                <x-dropdown-link :href="route('admin.articles.index')">Articles de blog</x-dropdown-link>
                                <x-dropdown-link :href="route('admin.jobs.index')">Offres d'emploi</x-dropdown-link>
                            @endif
                            <div class="border-t border-slate-100"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="text-red-600">
                                    Déconnexion
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <a href="{{ route('login') }}" class="rounded-md border border-slate-300 bg-white px-3 py-1.5 text-xs font-bold text-slate-700 hover:bg-slate-50">
                        Mon compte
                    </a>
                @endauth
            </div>

            <!-- Mobile Hamburger Button & Quick Post Ad -->
            <div class="flex items-center gap-2 sm:hidden">
                <a href="{{ route('listings.create') }}" class="inline-flex items-center gap-1 rounded-lg bg-[#7D5A50] px-3 py-1.5 text-xs font-bold uppercase tracking-wider text-white shadow-xs transition active:scale-95">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    <span>Publier</span>
                </a>
                <button @click="open = ! open" aria-label="Menu" class="inline-flex items-center justify-center rounded-lg p-2 text-slate-700 hover:bg-slate-100 hover:text-slate-900 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Category Nav Strip (Horizontally Scrollable without breaking page width) -->
    <div class="w-full border-t border-slate-200 bg-slate-50 overflow-x-auto scrollbar-none">
        <div class="mx-auto flex max-w-7xl items-center gap-4 px-4 py-1.5 text-xs font-semibold text-slate-700 sm:px-6 lg:px-8 whitespace-nowrap min-w-max">
            <a href="{{ route('listings.index') }}" class="py-1 hover:text-[#7D5A50] transition {{ request()->routeIs('home') || (request()->routeIs('listings.index') && !request()->routeIs('products.*')) ? 'border-b-2 border-[#7D5A50] text-[#7D5A50] font-bold' : '' }}">
                Toutes les annonces
            </a>
            <a href="{{ route('products.index') }}" class="py-1 hover:text-[#7D5A50] transition {{ request()->routeIs('products.*') && !request('category') ? 'border-b-2 border-[#7D5A50] text-[#7D5A50] font-bold' : '' }}">
                🛒 Tous les produits
            </a>
            <a href="{{ route('products.index', ['category' => 7]) }}" class="py-1 hover:text-[#7D5A50] transition {{ request()->routeIs('products.*') && (string) request('category') === '7' ? 'border-b-2 border-[#7D5A50] text-[#7D5A50] font-bold' : '' }}">
                Tracteurs &amp; Matériel
            </a>
            <a href="{{ route('products.index', ['category' => 6]) }}" class="py-1 hover:text-[#7D5A50] transition {{ request()->routeIs('products.*') && (string) request('category') === '6' ? 'border-b-2 border-[#7D5A50] text-[#7D5A50] font-bold' : '' }}">
                Élevage &amp; Animaux
            </a>
            <a href="{{ route('products.index', ['category' => 1]) }}" class="py-1 hover:text-[#7D5A50] transition {{ request()->routeIs('products.*') && (string) request('category') === '1' ? 'border-b-2 border-[#7D5A50] text-[#7D5A50] font-bold' : '' }}">
                Céréales &amp; Récoltes
            </a>
            <a href="{{ route('products.index', ['category' => 3]) }}" class="py-1 hover:text-[#7D5A50] transition {{ request()->routeIs('products.*') && (string) request('category') === '3' ? 'border-b-2 border-[#7D5A50] text-[#7D5A50] font-bold' : '' }}">
                Légumes &amp; Maraîchage
            </a>
            <a href="{{ route('products.index', ['category' => 5]) }}" class="py-1 hover:text-[#7D5A50] transition {{ request()->routeIs('products.*') && (string) request('category') === '5' ? 'border-b-2 border-[#7D5A50] text-[#7D5A50] font-bold' : '' }}">
                Intrants &amp; Semences
            </a>
            <a href="{{ route('products.index', ['category' => 2]) }}" class="py-1 hover:text-[#7D5A50] transition {{ request()->routeIs('products.*') && (string) request('category') === '2' ? 'border-b-2 border-[#7D5A50] text-[#7D5A50] font-bold' : '' }}">
                Fruits
            </a>
            <a href="{{ route('products.index', ['category' => 4]) }}" class="py-1 hover:text-[#7D5A50] transition {{ request()->routeIs('products.*') && (string) request('category') === '4' ? 'border-b-2 border-[#7D5A50] text-[#7D5A50] font-bold' : '' }}">
                Tubercules
            </a>
            <a href="{{ route('jobs.index') }}" class="py-1 text-slate-800 hover:text-[#7D5A50] transition {{ request()->routeIs('jobs.*') ? 'border-b-2 border-[#7D5A50] text-[#7D5A50] font-bold' : '' }}">
                Emploi agricole
            </a>
            <a href="{{ route('blog.index') }}" class="py-1 text-slate-800 hover:text-[#7D5A50] transition {{ request()->routeIs('blog.*') ? 'border-b-2 border-[#7D5A50] text-[#7D5A50] font-bold' : '' }}">
                Blog &amp; Conseils
            </a>
        </div>
    </div>

    <!-- Mobile Drawer Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-slate-200 bg-white">
        <div class="p-3 border-b border-slate-100 bg-slate-50">
            <form method="GET" action="{{ route('products.index') }}" class="relative">
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Tracteur, bétail, récolte..."
                    class="w-full rounded-md border border-slate-300 py-1.5 pl-3 pr-8 text-xs">
                <button type="submit" aria-label="Rechercher" class="absolute right-1 top-1 rounded bg-[#7D5A50] p-1.5 text-white">
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
            </form>
        </div>

        <div class="space-y-0.5 px-3 py-2 text-xs font-medium text-slate-800">
            <a href="{{ route('listings.index') }}" class="block rounded-md px-3 py-1.5 hover:bg-slate-100">Toutes les annonces</a>
            <a href="{{ route('products.index') }}" class="block rounded-md px-3 py-1.5 font-bold text-[#7D5A50] hover:bg-slate-100">🛒 Tous les produits</a>
            <a href="{{ route('products.index', ['category' => 7]) }}" class="block rounded-md px-3 py-1.5 text-slate-600 hover:bg-slate-50">Tracteurs &amp; Matériel</a>
            <a href="{{ route('products.index', ['category' => 6]) }}" class="block rounded-md px-3 py-1.5 text-slate-600 hover:bg-slate-50">Élevage &amp; Bétail</a>
            <a href="{{ route('products.index', ['category' => 1]) }}" class="block rounded-md px-3 py-1.5 text-slate-600 hover:bg-slate-50">Céréales &amp; Récoltes</a>
            <a href="{{ route('products.index', ['category' => 3]) }}" class="block rounded-md px-3 py-1.5 text-slate-600 hover:bg-slate-50">Légumes &amp; Maraîchage</a>
            <a href="{{ route('products.index', ['category' => 5]) }}" class="block rounded-md px-3 py-1.5 text-slate-600 hover:bg-slate-50">Intrants &amp; Semences</a>
            <a href="{{ route('jobs.index') }}" class="block rounded-md px-3 py-1.5 text-slate-600 hover:bg-slate-50">Offres d'emploi agricole</a>
            <a href="{{ route('blog.index') }}" class="block rounded-md px-3 py-1.5 text-[#7D5A50] font-bold hover:bg-slate-50">Blog &amp; Conseils agricoles</a>

            @auth
                <div class="my-2 border-t border-slate-200"></div>
                <div class="px-3 py-1 text-[11px] font-bold uppercase text-slate-400">Compte : {{ Auth::user()->name }}</div>
                <a href="{{ route('listings.mine') }}" class="block rounded-md px-3 py-1.5 text-[#7D5A50] font-bold hover:bg-slate-100">{{ Auth::user()->is_admin ? '📦 Gestion des annonces' : 'Mes annonces' }}</a>
                <a href="{{ route('profile.edit') }}" class="block rounded-md px-3 py-1.5 hover:bg-slate-100">Profil</a>
                @if (Auth::user()->is_admin)
                    <a href="{{ route('admin.dashboard') }}" class="block rounded-md px-3 py-1.5 text-[#7D5A50] hover:bg-[#7D5A50]/5">Administration</a>
                    <a href="{{ route('admin.mercuriale.index') }}" class="block rounded-md px-3 py-1.5 text-slate-700 hover:bg-slate-50">Prix du marché</a>
                    <a href="{{ route('admin.articles.index') }}" class="block rounded-md px-3 py-1.5 text-slate-700 hover:bg-slate-50">Gestion Blog</a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left rounded-md px-3 py-1.5 text-red-600 hover:bg-red-50">Déconnexion</button>
                </form>
            @else
                <div class="my-2 border-t border-slate-200"></div>
                <div class="grid grid-cols-2 gap-2 pt-1">
                    <a href="{{ route('login') }}" class="rounded-md border border-slate-300 py-1.5 text-center text-xs font-bold text-slate-700">Connexion</a>
                    <a href="{{ route('register') }}" class="rounded-md bg-[#7D5A50] py-1.5 text-center text-xs font-bold text-white">S'inscrire</a>
                </div>
            @endauth
        </div>
    </div>
</nav>
