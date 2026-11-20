<?php

use App\Models\Category;
use App\Models\Listing;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component
{
    use WithFileUploads;

    public ?Listing $listing = null;
    public string $type = 'offer';
    public string $title = '';
    public string $product = '';
    public string $description = '';
    public string $category_id = '';
    public string $quantity = '';
    public string $unit = 'kg';
    public string $price = '';
    public bool $is_negotiable = true;
    public string $location = '';
    public string $available_from = '';
    public array $photos = [];

    public function mount(?Listing $listing = null): void
    {
        if (! $listing?->exists) {
            $this->location = Auth::user()->location ?? '';
            return;
        }

        abort_unless(Auth::id() === $listing->user_id, 403);

        $this->listing = $listing;
        $this->type = $listing->type;
        $this->title = $listing->title;
        $this->product = $listing->product;
        $this->description = $listing->description ?? '';
        $this->category_id = (string) $listing->category_id;
        $this->quantity = (string) $listing->quantity;
        $this->unit = $listing->unit ?? 'kg';
        $this->price = (string) $listing->price;
        $this->is_negotiable = $listing->is_negotiable;
        $this->location = $listing->location;
        $this->available_from = optional($listing->available_from)->format('Y-m-d') ?? '';
    }

    public function save()
    {
        $data = $this->validate([
            'type' => ['required', 'in:offer,request'],
            'title' => ['required', 'string', 'max:160'],
            'product' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:2000'],
            'category_id' => ['required', 'exists:categories,id'],
            'quantity' => ['nullable', 'numeric', 'min:0'],
            'unit' => ['nullable', 'string', 'max:40'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'is_negotiable' => ['boolean'],
            'location' => ['required', 'string', 'max:255'],
            'available_from' => ['nullable', 'date'],
            'photos' => ['array', 'max:5'],
            'photos.*' => ['image', 'max:2048'],
        ]);

        $existingPhotoCount = $this->listing?->exists
            ? $this->listing->photos()->count()
            : 0;

        if ($existingPhotoCount + count($this->photos) > 5) {
            $this->addError('photos', 'Une annonce ne peut pas depasser 5 photos.');

            return;
        }

        $listingData = collect($data)->except('photos')->merge([
            'user_id' => Auth::id(),
            'quantity' => ($data['quantity'] ?? '') === '' ? null : $data['quantity'],
            'price' => ($data['price'] ?? '') === '' ? null : $data['price'],
            'available_from' => ($data['available_from'] ?? '') === '' ? null : $data['available_from'],
            'expires_at' => $this->listing?->expires_at ?? now()->addDays(30),
            'status' => $this->listing?->status ?? 'active',
        ])->all();

        $listing = $this->listing?->exists
            ? tap($this->listing)->update($listingData)
            : Listing::create($listingData);

        $lastSortOrder = $listing->photos()->max('sort_order');
        $nextSortOrder = $lastSortOrder === null ? 0 : (int) $lastSortOrder + 1;

        foreach ($this->photos as $index => $photo) {
            $listing->photos()->create([
                'path' => $photo->store('listings', 'public'),
                'sort_order' => $nextSortOrder + $index,
            ]);
        }

        session()->flash('status', 'Annonce enregistree.');

        return redirect()->route('listings.show', $listing);
    }

    public function render()
    {
        return $this->view([
            'categories' => Category::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }
};
?>

<div class="bg-white border border-slate-200 shadow-sm sm:rounded-lg">
    <form wire:submit.prevent="save" class="space-y-6 p-6">
        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label for="type" class="block text-sm font-medium text-slate-700">Type d'annonce</label>
                <select id="type" wire:model="type" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                    <option value="offer">Je vends</option>
                    <option value="request">Je recherche</option>
                </select>
                @error('type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="category_id" class="block text-sm font-medium text-slate-700">Categorie</label>
                <select id="category_id" wire:model="category_id" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                    <option value="">Choisir une categorie</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="title" class="block text-sm font-medium text-slate-700">Titre</label>
                <input id="title" type="text" wire:model="title" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="product" class="block text-sm font-medium text-slate-700">Produit</label>
                <input id="product" type="text" wire:model="product" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                @error('product') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="quantity" class="block text-sm font-medium text-slate-700">Quantite</label>
                <input id="quantity" type="number" min="0" step="0.01" wire:model="quantity" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                @error('quantity') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="unit" class="block text-sm font-medium text-slate-700">Unite</label>
                <input id="unit" type="text" wire:model="unit" placeholder="kg, tonne, sac, caisse" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                @error('unit') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="price" class="block text-sm font-medium text-slate-700">Prix</label>
                <input id="price" type="number" min="0" step="1" wire:model="price" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                @error('price') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="available_from" class="block text-sm font-medium text-slate-700">Disponible a partir du</label>
                <input id="available_from" type="date" wire:model="available_from" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                @error('available_from') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="location" class="block text-sm font-medium text-slate-700">Localisation</label>
                <input id="location" type="text" wire:model="location" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                @error('location') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3 pt-6">
                <input id="is_negotiable" type="checkbox" wire:model="is_negotiable" class="rounded border-slate-300 text-[#7D5A50] focus:ring-[#7D5A50]">
                <label for="is_negotiable" class="text-sm font-medium text-slate-700">Prix negociable</label>
            </div>
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-slate-700">Description</label>
            <textarea id="description" rows="5" wire:model="description" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-[#7D5A50] focus:ring-[#7D5A50]"></textarea>
            @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="photos" class="block text-sm font-medium text-slate-700">Photos</label>
            <input id="photos" type="file" wire:model="photos" multiple accept="image/*" class="mt-1 block w-full text-sm text-slate-700 file:mr-4 file:rounded-md file:border-0 file:bg-[#7D5A50]/10 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-[#7D5A50] hover:file:bg-[#7D5A50]/20">
            <p class="mt-1 text-xs text-slate-500">Jusqu'a 5 images, 2 Mo par image.</p>
            @error('photos') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            @error('photos.*') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-5">
            <a href="{{ route('listings.mine') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Annuler</a>
            <button type="submit" class="rounded-md bg-[#7D5A50] px-4 py-2 text-sm font-semibold text-white hover:bg-[#6b4d44]" wire:loading.attr="disabled">
                Enregistrer
            </button>
        </div>
    </form>
</div>
