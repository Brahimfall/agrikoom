<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'AgriKoom') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 antialiased">
        <main class="min-h-screen bg-slate-50">
            <div class="grid min-h-screen lg:grid-cols-[minmax(0,0.95fr)_minmax(420px,0.75fr)]">
                <section class="relative hidden overflow-hidden bg-[#7D5A50] lg:block">
                    <img
                        src="{{ asset('assets/home/market-hero.png') }}"
                        alt="Marche agricole AgriKoom"
                        class="absolute inset-0 h-full w-full object-cover"
                    >
                    <div class="absolute inset-0 bg-gradient-to-br from-[#4a342e]/95 via-[#5a3f38]/82 to-[#7D5A50]/55"></div>

                    <div class="relative flex min-h-screen flex-col justify-between p-10 text-white">
                        <a href="{{ route('home') }}" class="inline-flex w-fit rounded-md bg-white p-2 shadow-xl">
                            <img src="{{ asset('assets/brand/agrikoom-logo-cropped.png') }}" alt="AgriKoom" class="h-16 w-auto">
                        </a>

                        <div class="max-w-xl pb-8">
                            <p class="inline-flex rounded-md bg-white px-3 py-1.5 text-xs font-bold uppercase tracking-wide text-[#7D5A50]">Espace vendeur</p>
                            <h1 class="mt-5 text-5xl font-extrabold leading-tight">Vendez plus vite avec un contact direct</h1>
                            <p class="mt-5 text-lg leading-8 text-white/90">Publiez vos produits, affichez vos prix, puis laissez les acheteurs vous joindre par WhatsApp ou par appel.</p>

                            <div class="mt-8 grid grid-cols-3 gap-3">
                                <div class="rounded-md border border-white/20 bg-white/10 p-4 backdrop-blur">
                                    <p class="text-2xl font-extrabold">3</p>
                                    <p class="mt-1 text-xs font-semibold text-white/80">infos pour creer le compte</p>
                                </div>
                                <div class="rounded-md border border-white/20 bg-white/10 p-4 backdrop-blur">
                                    <p class="text-2xl font-extrabold">30j</p>
                                    <p class="mt-1 text-xs font-semibold text-white/80">visibilite annonce</p>
                                </div>
                                <div class="rounded-md border border-white/20 bg-white/10 p-4 backdrop-blur">
                                    <p class="text-2xl font-extrabold">Direct</p>
                                    <p class="mt-1 text-xs font-semibold text-white/80">appel et WhatsApp</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="flex min-h-screen items-center justify-center px-4 py-8 sm:px-6 lg:px-10">
                    <div class="w-full max-w-md">
                        <div class="mb-7 flex items-center justify-between">
                            <a href="{{ route('home') }}" class="inline-flex items-center rounded-md bg-white p-2 shadow-sm ring-1 ring-slate-200 lg:hidden">
                                <img src="{{ asset('assets/brand/agrikoom-logo-cropped.png') }}" alt="AgriKoom" class="h-14 w-auto">
                            </a>
                            <a href="{{ route('home') }}" class="text-sm font-bold text-[#7D5A50] hover:text-[#4a342e]">Retour au marketplace</a>
                        </div>

                        <div class="overflow-hidden rounded-md border border-slate-200 bg-white shadow-xl shadow-slate-200/70">
                            <div class="border-b border-slate-100 bg-white px-6 py-5">
                                <p class="text-xs font-bold uppercase tracking-wide text-[#7D5A50]">AgriKoom vendeur</p>
                                <p class="mt-1 text-sm text-slate-500">Compte rapide, contact direct, publication simple.</p>
                            </div>

                            <div class="px-6 py-6">
                                {{ $slot }}
                            </div>
                        </div>

                        <p class="mt-5 text-center text-xs font-medium text-slate-500">
                            Pour le moment, l'espace prive est reserve aux vendeurs.
                        </p>
                    </div>
                </section>
            </div>
        </main>
    </body>
</html>
