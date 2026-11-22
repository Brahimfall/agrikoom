<?php

use App\Models\Category;
use App\Models\Listing;
use App\Models\User;
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

    // Super Admin options
    public ?int $user_id = null;
    public string $vendor_mode = 'existing'; // 'existing' | 'new'
    public string $vendor_new_name = '';
    public string $vendor_new_phone = '';
    public string $vendor_new_email = '';
    public string $vendor_new_location = '';

    public function mount(?Listing $listing = null): void
    {
        if (request('vendor_mode') === 'new') {
            $this->vendor_mode = 'new';
        }

        if (! $listing?->exists) {
            $this->location = Auth::user()->location ?? '';
            $this->user_id = Auth::id();
            return;
        }

        abort_unless(Auth::id() === $listing->user_id || Auth::user()?->is_admin, 403);

        $this->listing = $listing;
        $this->user_id = $listing->user_id;
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

    public function updatedUserId($val): void
    {
        if ($val === -1) {
            $this->vendor_mode = 'new';
            $this->user_id = null;
            return;
        }

        if ($val) {
            $vendor = User::find($val);
            if ($vendor && ! empty($vendor->location) && empty($this->location)) {
                $this->location = $vendor->location;
            }
        }
    }

    public function updatedVendorNewLocation($val): void
    {
        if ($val && empty($this->location)) {
            $this->location = $val;
        }
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

        $targetUserId = Auth::id();

        if (Auth::user()?->is_admin) {
            if ($this->vendor_mode === 'new') {
                $this->validate([
                    'vendor_new_name' => ['required', 'string', 'max:120'],
                    'vendor_new_phone' => ['required', 'string', 'max:30'],
                    'vendor_new_email' => ['nullable', 'email', 'max:120'],
                    'vendor_new_location' => ['nullable', 'string', 'max:255'],
                ], [
                    'vendor_new_name.required' => 'Le nom du nouveau client/vendeur est obligatoire.',
                    'vendor_new_phone.required' => 'Le numéro de téléphone du client est obligatoire pour les contacts acheteurs.',
                ]);

                $phone = trim($this->vendor_new_phone);
                $newVendor = User::where('phone', $phone)->first();

                if (! $newVendor) {
                    $newVendor = User::create([
                        'name' => trim($this->vendor_new_name),
                        'phone' => $phone,
                        'email' => ! empty($this->vendor_new_email) ? trim($this->vendor_new_email) : null,
                        'location' => trim($this->vendor_new_location) ?: $data['location'],
                        'actor_type' => 'producer',
                        'phone_verified_at' => now(),
                    ]);
                } else {
                    $newVendor->update([
                        'name' => trim($this->vendor_new_name) ?: $newVendor->name,
                        'location' => trim($this->vendor_new_location) ?: $newVendor->location,
                    ]);
                }

                $targetUserId = $newVendor->id;
            } elseif ($this->user_id) {
                $targetUserId = $this->user_id;
            }
        } elseif ($this->listing?->exists) {
            $targetUserId = $this->listing->user_id;
        }

        $listingData = collect($data)->except('photos')->merge([
            'user_id' => $targetUserId,
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

        session()->flash('status', 'Annonce enregistrée avec succès.');

        return redirect()->route('listings.show', $listing);
    }

    public function render()
    {
        return $this->view([
            'categories' => Category::query()->where('is_active', true)->orderBy('name')->get(),
            'vendors' => Auth::user()?->is_admin ? User::query()->orderBy('name')->get(['id', 'name', 'phone', 'email', 'location']) : collect(),
            'selectedVendor' => Auth::user()?->is_admin && $this->user_id ? User::find($this->user_id) : null,
        ]);
    }
};
?>

<div class="bg-white border border-slate-200 shadow-sm sm:rounded-lg">
    <form wire:submit.prevent="save" class="space-y-6 p-6">
        @if (Auth::user()?->is_admin)
            <div class="rounded-2xl border-2 border-dashed border-[#7D5A50]/40 bg-[#7D5A50]/5 p-5">
                <div class="flex items-center justify-between gap-3 mb-2">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-[#7D5A50] text-white text-xs font-bold">★</span>
                        <h3 class="text-sm font-bold text-[#7D5A50]">Options Super Admin : Attribution de l'annonce</h3>
                    </div>
                    <span class="rounded-full bg-[#7D5A50] px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider text-white">Super Admin</span>
                </div>
                <p class="text-xs text-slate-600 mb-4">
                    Vous pouvez publier ce produit au nom d'un vendeur ou producteur tiers.
                </p>

                <!-- Bascule Mode Existant vs Nouveau Client / Vendeur -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-4">
                    <button type="button"
                            wire:click="$set('vendor_mode', 'existing')"
                            class="flex items-center justify-center gap-2 rounded-xl p-3 text-xs font-bold border transition text-left {{ $vendor_mode === 'existing' ? 'bg-[#7D5A50] text-white border-[#7D5A50] shadow-xs' : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50' }}">
                        <span>👤 Vendeur existant</span>
                        <span class="text-[10px] opacity-80">(Choisir dans la liste)</span>
                    </button>
                    <button type="button"
                            wire:click="$set('vendor_mode', 'new')"
                            class="flex items-center justify-center gap-2 rounded-xl p-3 text-xs font-bold border transition text-left {{ $vendor_mode === 'new' ? 'bg-emerald-700 text-white border-emerald-700 shadow-xs' : 'bg-emerald-50 text-emerald-800 border-emerald-300 hover:bg-emerald-100' }}">
                        <span>✨ Nouveau client</span>
                        <span class="text-[10px] font-semibold opacity-90">(Qui n'existe pas encore)</span>
                    </button>
                </div>

                @if ($vendor_mode === 'existing')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 items-start">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Sélectionnez le vendeur propriétaire :</label>
                            <select wire:model.live="user_id" class="w-full rounded-lg border-slate-300 text-xs focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                                <option value="{{ Auth::id() }}">★ Moi-même (Super Admin - {{ Auth::user()->name }})</option>
                                <option value="-1">➕ Créer un nouveau client...</option>
                                <optgroup label="── Vendeurs enregistrés ──">
                                    @foreach ($vendors as $v)
                                        @if ($v->id !== Auth::id())
                                            <option value="{{ $v->id }}">
                                                {{ $v->name }} — {{ $v->phone ?: $v->email }} ({{ $v->location ?: 'Sénégal' }})
                                            </option>
                                        @endif
                                    @endforeach
                                </optgroup>
                            </select>
                        </div>
                        @if ($selectedVendor)
                            <div class="rounded-lg bg-white p-3 border border-slate-200/90 text-xs shadow-2xs">
                                <div class="font-bold text-slate-900">{{ $selectedVendor->name }}</div>
                                <div class="text-slate-600 text-[11px] mt-0.5">
                                    <span>📞 {{ $selectedVendor->phone ?: 'Sans numéro' }}</span>
                                    @if ($selectedVendor->location)
                                        <span>&bull; 📍 {{ $selectedVendor->location }}</span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="rounded-xl bg-white p-4 border-2 border-emerald-500/30 space-y-3 shadow-xs">
                        <div class="flex items-center justify-between">
                            <div class="text-xs font-extrabold text-emerald-950 flex items-center gap-1.5">
                                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                <span>Coordonnées du nouveau client / vendeur :</span>
                            </div>
                            <span class="text-[10px] text-emerald-700 font-bold bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full">
                                Compte client créé automatiquement
                            </span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Nom complet du client *</label>
                                <input type="text" wire:model="vendor_new_name" placeholder="Ex: Samba Diallo" class="w-full rounded-lg border-slate-300 text-xs focus:border-emerald-600 focus:ring-emerald-600">
                                @error('vendor_new_name') <span class="text-[10px] text-red-600 font-bold">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Téléphone / WhatsApp *</label>
                                <input type="tel" wire:model="vendor_new_phone" placeholder="Ex: +221 77 123 45 67" class="w-full rounded-lg border-slate-300 text-xs focus:border-emerald-600 focus:ring-emerald-600">
                                @error('vendor_new_phone') <span class="text-[10px] text-red-600 font-bold">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Localisation / Région</label>
                                <input type="text" wire:model.live="vendor_new_location" placeholder="Ex: Thiès, Niayes" class="w-full rounded-lg border-slate-300 text-xs focus:border-emerald-600 focus:ring-emerald-600">
                            </div>
                        </div>
                        <p class="text-[11px] text-slate-500 italic">
                            💡 Dès validation, ce client sera créé sur la plateforme et les acheteurs pourront le contacter directement sur son numéro WhatsApp / Téléphone.
                        </p>
                    </div>
                @endif
            </div>
        @endif

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
