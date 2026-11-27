<?php

use App\Models\Category;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
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
    public string $availability_type = 'immediate';
    public string $available_from = '';
    public array $photos = [];

    // Super Admin
    public ?int $user_id = null;
    public string $vendor_mode = 'existing';
    public string $vendor_new_name = '';
    public string $vendor_new_phone = '';
    public string $vendor_new_email = '';
    public string $vendor_new_location = '';

    public function mount(?Listing $listing = null): void
    {
        if (request()->query('vendor_mode') === 'new') {
            $this->vendor_mode = 'new';
        }

        if (! $listing?->exists) {
            $this->location = Auth::user()->location ?? '';
            $this->user_id  = $this->vendor_mode === 'new' ? null : Auth::id();
            return;
        }

        abort_unless(Auth::id() === $listing->user_id || Auth::user()?->is_admin, 403);

        $this->listing          = $listing;
        $this->user_id          = $listing->user_id;
        $this->type             = $listing->type;
        $this->title            = $listing->title;
        $this->product          = $listing->product;
        $this->description      = $listing->description ?? '';
        $this->category_id      = (string) $listing->category_id;
        $this->quantity         = (string) $listing->quantity;
        $this->unit             = $listing->unit ?? 'kg';
        $this->price            = (string) $listing->price;
        $this->is_negotiable    = $listing->is_negotiable;
        $this->location         = $listing->location;

        if ($listing->available_from && $listing->available_from->gt(today())) {
            $this->availability_type = 'later';
            $this->available_from    = $listing->available_from->format('Y-m-d');
        } else {
            $this->availability_type = 'immediate';
            $this->available_from    = '';
        }
    }

    public function setVendorMode(string $mode): void
    {
        $this->vendor_mode = $mode;
        if ($mode === 'new') {
            $this->user_id = null;
        } else {
            $this->user_id = Auth::id();
            if (empty($this->location)) {
                $this->location = Auth::user()->location ?? '';
            }
        }
    }

    public function updatedUserId($val): void
    {
        if ($val == -1 || $val === '-1') {
            $this->setVendorMode('new');
            return;
        }
        if ($val && (int) $val > 0) {
            $this->vendor_mode = 'existing';
            $vendor = User::find($val);
            if ($vendor && ! empty($vendor->location) && empty($this->location)) {
                $this->location = $vendor->location;
            }
        }
    }

    public function save()
    {
        // Auto-fill title/product
        if (empty($this->title) && ! empty($this->product)) {
            $this->title = $this->product;
        } elseif (empty($this->product) && ! empty($this->title)) {
            $this->product = $this->title;
        }

        // Auto-fill location
        if (empty($this->location)) {
            $this->location = ! empty($this->vendor_new_location)
                ? trim($this->vendor_new_location)
                : (Auth::user()?->location ?: 'Sénégal');
        }

        // Si un nom/téléphone de nouveau client est présent → forcer mode 'new'
        if (Auth::user()?->is_admin) {
            if (! empty(trim($this->vendor_new_name)) || ! empty(trim($this->vendor_new_phone))) {
                $this->vendor_mode = 'new';
                $this->user_id     = null;
            }
        }

        if (Auth::user()?->is_admin && $this->vendor_mode === 'new') {
            if (empty($this->location) && ! empty($this->vendor_new_location)) {
                $this->location = trim($this->vendor_new_location);
            } elseif (empty($this->vendor_new_location) && ! empty($this->location)) {
                $this->vendor_new_location = trim($this->location);
            }
        }

        $validationRules = [
            'type'              => ['required', 'in:offer,request'],
            'title'             => ['required', 'string', 'max:160'],
            'product'           => ['required', 'string', 'max:120'],
            'description'       => ['nullable', 'string', 'max:2000'],
            'category_id'       => ['required', 'exists:categories,id'],
            'quantity'          => ['nullable', 'numeric', 'min:0'],
            'unit'              => ['nullable', 'string', 'max:40'],
            'price'             => ['nullable', 'numeric', 'min:0'],
            'is_negotiable'     => ['boolean'],
            'location'          => ['required', 'string', 'max:255'],
            'availability_type' => ['required', 'in:immediate,later'],
            'photos'            => ['array', 'max:5'],
            'photos.*'          => ['image', 'max:2048'],
        ];

        if ($this->availability_type === 'later') {
            $validationRules['available_from'] = ['required', 'date'];
        }

        $customMessages = [];
        if (Auth::user()?->is_admin && $this->vendor_mode === 'new') {
            $validationRules['vendor_new_name']  = ['required', 'string', 'max:120'];
            $validationRules['vendor_new_phone'] = ['required', 'string', 'max:30'];
            $validationRules['vendor_new_email'] = ['nullable', 'email', 'max:120'];
            $validationRules['vendor_new_location'] = ['nullable', 'string', 'max:255'];
            $customMessages['vendor_new_name.required']  = 'Le nom du nouveau client est obligatoire.';
            $customMessages['vendor_new_phone.required'] = 'Le téléphone du client est obligatoire.';
        }

        $data = $this->validate($validationRules, $customMessages);

        $existingPhotoCount = $this->listing?->exists ? $this->listing->photos()->count() : 0;
        if ($existingPhotoCount + count($this->photos) > 5) {
            $this->addError('photos', 'Une annonce ne peut pas dépasser 5 photos.');
            return;
        }

        $targetUserId = Auth::id();

        if (Auth::user()?->is_admin) {
            if ($this->vendor_mode === 'new') {
                $rawPhone = trim($this->vendor_new_phone);
                $digits   = preg_replace('/\D+/', '', $rawPhone);

                $newVendor = User::where('phone', $rawPhone)->where('id', '!=', Auth::id())->first();
                if (! $newVendor && strlen($digits) >= 9) {
                    $newVendor = User::where('phone', 'LIKE', '%' . substr($digits, -9))
                        ->where('id', '!=', Auth::id())
                        ->first();
                }

                if (! $newVendor) {
                    $emailVal = ! empty($this->vendor_new_email) ? trim($this->vendor_new_email) : null;
                    if ($emailVal && User::where('email', $emailVal)->exists()) {
                        $emailVal = null;
                    }
                    $newVendor = User::create([
                        'name'              => trim($this->vendor_new_name),
                        'phone'             => $rawPhone,
                        'email'             => $emailVal,
                        'password'          => Hash::make(Str::random(32)),
                        'location'          => trim($this->vendor_new_location) ?: $data['location'],
                        'actor_type'        => 'producer',
                        'is_admin'          => false,
                        'phone_verified_at' => now(),
                    ]);
                } else {
                    $upd = [];
                    if (! empty($this->vendor_new_name))     { $upd['name']     = trim($this->vendor_new_name); }
                    if (! empty($this->vendor_new_location)) { $upd['location'] = trim($this->vendor_new_location); }
                    if (! empty($upd)) { $newVendor->update($upd); }
                }
                $targetUserId = $newVendor->id;

            } elseif ($this->user_id && (int) $this->user_id > 0) {
                $targetUserId = (int) $this->user_id;
            }
        } elseif ($this->listing?->exists) {
            $targetUserId = $this->listing->user_id;
        }

        // Filet de sécurité : jamais liée à l'admin si mode 'new'
        if (Auth::user()?->is_admin && $this->vendor_mode === 'new' && $targetUserId === Auth::id()) {
            $emailVal = ! empty($this->vendor_new_email) ? trim($this->vendor_new_email) : null;
            $fallback = User::create([
                'name'              => trim($this->vendor_new_name),
                'phone'             => trim($this->vendor_new_phone),
                'email'             => $emailVal,
                'password'          => Hash::make(Str::random(32)),
                'location'          => trim($this->vendor_new_location) ?: $data['location'],
                'actor_type'        => 'producer',
                'is_admin'          => false,
                'phone_verified_at' => now(),
            ]);
            $targetUserId = $fallback->id;
        }

        $finalAvailableFrom = $this->availability_type === 'immediate'
            ? now()->toDateString()
            : ($this->available_from ?: now()->toDateString());

        $listingData = collect($data)->except([
            'photos', 'availability_type',
            'vendor_new_name', 'vendor_new_phone', 'vendor_new_email', 'vendor_new_location',
        ])->merge([
            'user_id'       => $targetUserId,
            'quantity'      => ($data['quantity'] ?? '') === '' ? null : $data['quantity'],
            'price'         => ($data['price'] ?? '') === '' ? null : $data['price'],
            'available_from'=> $finalAvailableFrom,
            'expires_at'    => $this->listing?->expires_at ?? now()->addDays(30),
            'status'        => $this->listing?->status ?? 'active',
        ])->all();

        try {
            $listing = $this->listing?->exists
                ? tap($this->listing)->update($listingData)
                : Listing::create($listingData);

            $lastSortOrder = $listing->photos()->max('sort_order');
            $nextSortOrder = $lastSortOrder === null ? 0 : (int) $lastSortOrder + 1;

            foreach ($this->photos as $index => $photo) {
                $listing->photos()->create([
                    'path'       => $photo->store('listings', 'public'),
                    'sort_order' => $nextSortOrder + $index,
                ]);
            }

            session()->flash('status', 'Annonce enregistrée avec succès.');
            return $this->redirect(route('listings.show', $listing), navigate: false);

        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Erreur enregistrement annonce: ' . $e->getMessage(), ['exception' => $e]);
            $this->addError('general', 'Erreur lors de l\'enregistrement : ' . $e->getMessage());
            return null;
        }
    }

    public function render()
    {
        return $this->view([
            'categories'     => Category::query()->where('is_active', true)->orderBy('name')->get(),
            'vendors'        => Auth::user()?->is_admin
                ? User::query()->orderBy('name')->get(['id', 'name', 'phone', 'email', 'location'])
                : collect(),
            'selectedVendor' => Auth::user()?->is_admin && $this->user_id
                ? User::find($this->user_id)
                : null,
        ]);
    }
};
?>

{{-- ============================================================
     UN SEUL x-data au niveau du <div> racine.
     Toutes les variables Alpine (vendorMode, vendorName…)
     sont accessibles partout dans le template, y compris
     depuis le bouton Submit qui appelle beforeSubmit().
     ============================================================ --}}
<div class="bg-white border border-slate-200 shadow-sm sm:rounded-lg"
     x-data="{
         vendorMode: '{{ $vendor_mode }}',
         vendorName: '{{ addslashes($vendor_new_name) }}',
         vendorPhone: '{{ addslashes($vendor_new_phone) }}',
         vendorLocation: '{{ addslashes($vendor_new_location) }}',
         setMode(val) {
             this.vendorMode = val;
             $wire.set('vendor_mode', val);
             if (val === 'new') {
                 $wire.set('user_id', null);
             } else {
                 $wire.set('user_id', {{ (int) Auth::id() }});
             }
         },
         beforeSubmit() {
             if (this.vendorMode === 'new') {
                 $wire.set('vendor_new_name',     this.vendorName     || '');
                 $wire.set('vendor_new_phone',    this.vendorPhone    || '');
                 $wire.set('vendor_new_location', this.vendorLocation || '');
                 $wire.set('vendor_mode', 'new');
                 $wire.set('user_id', null);
             }
         }
     }">

    <form wire:submit.prevent="save" class="space-y-6 p-6">

        {{-- Bannière erreurs --}}
        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800 shadow-sm"
                 x-data x-init="$el.scrollIntoView({ behavior: 'smooth' })">
                <div class="flex items-center gap-2 font-bold mb-1.5 text-red-900">
                    <svg class="h-5 w-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732
                                 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span>Veuillez vérifier les points suivants avant d'enregistrer :</span>
                </div>
                <ul class="list-disc list-inside space-y-1 text-xs text-red-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ===== SECTION SUPER ADMIN ===== --}}
        @if (Auth::user()?->is_admin)
            <div class="rounded-2xl border-2 border-dashed border-[#7D5A50]/40 bg-[#7D5A50]/5 p-5">
                <div class="flex items-center justify-between gap-3 mb-2">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-[#7D5A50] text-white text-xs font-bold">★</span>
                        <h3 class="text-sm font-bold text-[#7D5A50]">Options Super Admin : Attribution de l'annonce</h3>
                    </div>
                    <span class="rounded-full bg-[#7D5A50] px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider text-white">Super Admin</span>
                </div>
                <p class="text-xs text-slate-600 mb-4">Vous pouvez publier ce produit au nom d'un vendeur ou producteur tiers.</p>

                {{-- Boutons bascule --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-4">
                    <button type="button"
                            @click="setMode('existing')"
                            :class="vendorMode === 'existing'
                                ? 'bg-[#7D5A50] text-white border-[#7D5A50] shadow-sm'
                                : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50'"
                            class="flex items-center justify-center gap-2 rounded-xl p-3 text-xs font-bold border transition cursor-pointer">
                        <span>👤 Vendeur existant</span>
                        <span class="text-[10px] opacity-80">(Choisir dans la liste)</span>
                    </button>
                    <button type="button"
                            @click="setMode('new')"
                            :class="vendorMode === 'new'
                                ? 'bg-emerald-700 text-white border-emerald-700 shadow-sm'
                                : 'bg-emerald-50 text-emerald-800 border-emerald-300 hover:bg-emerald-100'"
                            class="flex items-center justify-center gap-2 rounded-xl p-3 text-xs font-bold border transition cursor-pointer">
                        <span>✨ Nouveau client</span>
                        <span class="text-[10px] font-semibold opacity-90">(Qui n'existe pas encore)</span>
                    </button>
                </div>

                {{-- Bloc Vendeur Existant --}}
                <div x-show="vendorMode === 'existing'" class="grid grid-cols-1 sm:grid-cols-2 gap-3 items-start">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Sélectionnez le vendeur propriétaire :</label>
                        <select wire:model.live="user_id"
                                @change="if ($event.target.value == -1) { setMode('new'); }"
                                class="w-full rounded-lg border-slate-300 text-xs focus:border-[#7D5A50] focus:ring-[#7D5A50]">
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
                        <div class="rounded-lg bg-white p-3 border border-slate-200/90 text-xs shadow-sm">
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

                {{-- Bloc Nouveau Client --}}
                {{-- Alpine x-model préserve les valeurs, @blur sync vers Livewire au fur et à mesure. --}}
                {{-- beforeSubmit() force la sync finale juste avant save(). --}}
                <div x-show="vendorMode === 'new'"
                     style="{{ $vendor_mode === 'new' ? '' : 'display:none;' }}"
                     class="rounded-xl bg-white p-4 border-2 border-emerald-500/30 space-y-3 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div class="text-xs font-extrabold text-emerald-950 flex items-center gap-1.5">
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                            <span>Coordonnées du nouveau client / vendeur :</span>
                        </div>
                        <span class="text-[10px] text-emerald-700 font-bold bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full">
                            Compte créé automatiquement
                        </span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 mb-1">Nom complet du client *</label>
                            <input type="text"
                                   x-model="vendorName"
                                   @blur="$wire.set('vendor_new_name', vendorName)"
                                   placeholder="Ex: Samba Diallo"
                                   class="w-full rounded-lg border-slate-300 text-xs focus:border-emerald-600 focus:ring-emerald-600">
                            @error('vendor_new_name') <span class="text-[10px] text-red-600 font-bold">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 mb-1">Téléphone / WhatsApp *</label>
                            <input type="tel"
                                   x-model="vendorPhone"
                                   @blur="$wire.set('vendor_new_phone', vendorPhone)"
                                   placeholder="Ex: +221 77 123 45 67"
                                   class="w-full rounded-lg border-slate-300 text-xs focus:border-emerald-600 focus:ring-emerald-600">
                            @error('vendor_new_phone') <span class="text-[10px] text-red-600 font-bold">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 mb-1">Localisation / Région</label>
                            <input type="text"
                                   x-model="vendorLocation"
                                   @blur="$wire.set('vendor_new_location', vendorLocation)"
                                   placeholder="Ex: Thiès, Niayes"
                                   class="w-full rounded-lg border-slate-300 text-xs focus:border-emerald-600 focus:ring-emerald-600">
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-500 italic">
                        💡 Dès validation, ce client sera créé et les acheteurs pourront le contacter directement via WhatsApp / Téléphone.
                    </p>
                </div>
            </div>
        @endif

        {{-- ===== CHAMPS DE L'ANNONCE ===== --}}
        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label for="type" class="block text-sm font-medium text-slate-700">Type d'annonce</label>
                <select id="type" wire:model="type"
                        class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                    <option value="offer">Je vends</option>
                    <option value="request">Je recherche</option>
                </select>
                @error('type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="category_id" class="block text-sm font-medium text-slate-700">Categorie</label>
                <select id="category_id" wire:model="category_id"
                        class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                    <option value="">Choisir une categorie</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="title" class="block text-sm font-medium text-slate-700">Titre</label>
                <input id="title" type="text" wire:model="title"
                       class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="product" class="block text-sm font-medium text-slate-700">Produit</label>
                <input id="product" type="text" wire:model="product"
                       class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                @error('product') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="quantity" class="block text-sm font-medium text-slate-700">Quantite</label>
                <input id="quantity" type="number" min="0" step="0.01" wire:model="quantity"
                       class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                @error('quantity') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="unit" class="block text-sm font-medium text-slate-700">Unite</label>
                <input id="unit" type="text" wire:model="unit" placeholder="kg, tonne, sac, caisse"
                       class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                @error('unit') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="price" class="block text-sm font-medium text-slate-700">Prix</label>
                <input id="price" type="number" min="0" step="1" wire:model="price"
                       class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                @error('price') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Disponibilité</label>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button"
                            wire:click="$set('availability_type', 'immediate')"
                            class="px-3 py-2 rounded-lg border text-sm font-medium flex items-center justify-center gap-1.5 transition
                                {{ $availability_type === 'immediate'
                                    ? 'border-emerald-600 bg-emerald-50 text-emerald-800 ring-2 ring-emerald-500/20'
                                    : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-50' }}">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>Immédiat</span>
                    </button>
                    <button type="button"
                            wire:click="$set('availability_type', 'later')"
                            class="px-3 py-2 rounded-lg border text-sm font-medium flex items-center justify-center gap-1.5 transition
                                {{ $availability_type === 'later'
                                    ? 'border-[#7D5A50] bg-[#7D5A50]/10 text-[#7D5A50] ring-2 ring-[#7D5A50]/20'
                                    : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-50' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>Date ultérieure</span>
                    </button>
                </div>
                @if ($availability_type === 'later')
                    <div class="mt-2">
                        <label for="available_from" class="block text-xs text-slate-600 mb-1">Date prévue :</label>
                        <input id="available_from" type="date" wire:model="available_from"
                               min="{{ now()->toDateString() }}"
                               class="block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                        @error('available_from') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                @else
                    <p class="mt-1.5 text-[11px] text-emerald-700 flex items-center gap-1">✓ En stock / disponible dès aujourd'hui</p>
                @endif
            </div>

            <div>
                <label for="location" class="block text-sm font-medium text-slate-700">Localisation</label>
                <input id="location" type="text" wire:model="location"
                       class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-[#7D5A50] focus:ring-[#7D5A50]">
                @error('location') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3 pt-6">
                <input id="is_negotiable" type="checkbox" wire:model="is_negotiable"
                       class="rounded border-slate-300 text-[#7D5A50] focus:ring-[#7D5A50]">
                <label for="is_negotiable" class="text-sm font-medium text-slate-700">Prix negociable</label>
            </div>
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-slate-700">Description</label>
            <textarea id="description" rows="5" wire:model="description"
                      class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-[#7D5A50] focus:ring-[#7D5A50]"></textarea>
            @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="photos" class="block text-sm font-medium text-slate-700">Photos</label>
            <input id="photos" type="file" wire:model="photos" multiple accept="image/*"
                   class="mt-1 block w-full text-sm text-slate-700
                          file:mr-4 file:rounded-md file:border-0 file:bg-[#7D5A50]/10
                          file:px-4 file:py-2 file:text-sm file:font-semibold file:text-[#7D5A50]
                          hover:file:bg-[#7D5A50]/20">
            <p class="mt-1 text-xs text-slate-500">Jusqu'a 5 images, 2 Mo par image.</p>
            @error('photos')   <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            @error('photos.*') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- ===== BOUTON SUBMIT =====
             @click.prevent → appelle beforeSubmit() pour sync Alpine → Livewire
             $nextTick      → attend que Livewire reçoive les valeurs
             $wire.save()   → déclenche la méthode save() côté PHP
        --}}
        <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-5">
            <a href="{{ route('listings.mine') }}"
               class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Annuler
            </a>
            <button type="button"
                    @click="beforeSubmit(); $nextTick(() => { $wire.save(); })"
                    class="inline-flex items-center justify-center gap-2 rounded-md bg-[#7D5A50] px-5 py-2 text-sm font-semibold text-white hover:bg-[#6b4d44] disabled:opacity-50 transition cursor-pointer"
                    wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="save">Enregistrer l'annonce</span>
                <span wire:loading wire:target="save" class="inline-flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span>Enregistrement en cours...</span>
                </span>
            </button>
        </div>
    </form>
</div>
