<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-semibold text-slate-950">{{ $listing ? 'Modifier l annonce' : 'Publier une annonce' }}</h1>
            <p class="mt-1 text-sm text-slate-600">Ajoutez les informations utiles pour que les acheteurs trouvent vite le bon produit.</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <livewire:listing-form :listing="$listing" />
        </div>
    </div>
</x-app-layout>
