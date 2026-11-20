<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tableau de bord
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-4">
                    <p>Bienvenue sur AgriKoom.</p>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('listings.create') }}" class="inline-flex items-center rounded-md bg-[#7D5A50] px-4 py-2 text-sm font-semibold text-white hover:bg-[#6b4d44]">Publier une annonce</a>
                        <a href="{{ route('listings.mine') }}" class="inline-flex items-center rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Voir mes annonces</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
