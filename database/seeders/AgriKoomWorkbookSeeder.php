<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AgriKoomWorkbookSeeder extends Seeder
{
    private array $usersByVendorId = [];

    public function run(): void
    {
        $path = database_path('data/agrikoom_import.json');

        if (! file_exists($path)) {
            return;
        }

        $data = json_decode((string) file_get_contents($path), true, flags: JSON_THROW_ON_ERROR);
        $categories = $this->seedCategories();
        $formalVendorIds = collect($data['offers'] ?? [])->pluck('ID Vendeur')->filter()->unique()->all();

        foreach ($data['vendors'] ?? [] as $vendor) {
            $this->usersByVendorId[$vendor['2.0']] = $this->upsertVendor($vendor);
        }

        foreach ($data['offers'] ?? [] as $offer) {
            $this->upsertStructuredOffer($offer, $categories);
        }

        foreach ($data['vendors'] ?? [] as $vendor) {
            if (in_array($vendor['2.0'], $formalVendorIds, true)) {
                continue;
            }

            $this->upsertVendorListing($vendor, $categories);
        }
    }

    private function seedCategories(): array
    {
        $defaults = [
            'Cereales' => 'Riz, mais, mil, sorgho',
            'Elevage' => 'Ovins, bovins, volailles, alimentation betail',
            'Fruits' => 'Mangues, bananes, agrumes',
            'Intrants' => 'Semences, engrais, fumier, biostimulants',
            'Legumes' => 'Tomates, oignons, piments',
            'Legumineuses' => 'Niebe, arachide, haricot',
            'Materiel agricole' => 'Motoculteurs, pompes, pulverisateurs, outils',
            'Produits horticoles' => 'Pepinieres, plants, fleurs, vergers',
            'Tubercules' => 'Igname, manioc, pomme de terre',
        ];

        return collect($defaults)->mapWithKeys(function (string $description, string $name) {
            $category = Category::query()->updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'description' => $description, 'is_active' => true],
            );

            return [$name => $category];
        })->all();
    }

    private function upsertVendor(array $vendor): User
    {
        $phone = $this->normalizePhone($vendor['Numéro de téléphone'] ?? '') ?: $this->normalizePhone($vendor['Numéro WhatsApp'] ?? '');
        $vendorId = $vendor['2.0'] ?? Str::slug($vendor['Nom / Nom commercial'] ?? Str::random(8));
        $name = trim($vendor['Nom / Nom commercial'] ?? '') ?: 'Vendeur AgriKoom '.$vendorId;
        $location = trim($vendor['Localisation (description libre)'] ?? '') ?: trim($vendor['Région'] ?? '') ?: 'Senegal';
        $verification = trim($vendor['Vérification du vendeur'] ?? '');
        $isVerified = $verification !== '' && ! Str::contains(Str::lower(Str::ascii($verification)), 'non verifie');
        $description = collect([
            trim($vendor['Activité principale'] ?? ''),
            trim($vendor['Commentaire de vérification'] ?? ''),
            'Source import: '.$vendorId,
        ])->filter()->implode("\n");

        return User::query()->updateOrCreate(
            ['phone' => $phone ?: '+221000'.str_pad((string) abs(crc32($vendorId)), 6, '0', STR_PAD_LEFT)],
            [
                'name' => $name,
                'email' => 'import+'.Str::lower(Str::slug($vendorId)).'@agrikoom.local',
                'location' => $location,
                'actor_type' => 'producer',
                'description' => $description,
                'phone_verified_at' => $isVerified ? now() : null,
                'password' => Hash::make('password'),
            ],
        );
    }

    private function upsertStructuredOffer(array $offer, array $categories): void
    {
        $user = $this->usersByVendorId[$offer['ID Vendeur'] ?? ''] ?? $this->upsertVendor([
            '2.0' => $offer['ID Vendeur'] ?? $offer['ID Offre'],
            'Nom / Nom commercial' => $offer['Nom vendeur'] ?? 'Vendeur AgriKoom',
            'Numéro de téléphone' => $offer['Téléphone'] ?? '',
            'Numéro WhatsApp' => $offer['WhatsApp'] ?? '',
            'Localisation (description libre)' => $offer['Localisation'] ?? '',
            'Région' => $offer['Région'] ?? '',
            'Activité principale' => $offer['Produit'] ?? '',
            'Vérification du vendeur' => '',
            'Commentaire de vérification' => '',
        ]);

        $categoryName = $this->normalizeCategory($offer['Catégorie'] ?? '', $offer['Produit'] ?? '');
        $category = $categories[$categoryName] ?? $categories['Materiel agricole'];
        $product = trim($offer['Produit'] ?? 'Produit agricole');
        $variant = trim($offer['Variété'] ?? '');
        $title = trim($product.' '.$variant);
        $description = collect([
            trim($offer['Description'] ?? ''),
            $variant ? 'Variete: '.$variant : null,
            trim($offer['Disponibilité'] ?? '') ? 'Disponibilite: '.trim($offer['Disponibilité']) : null,
            trim($offer['Livraison'] ?? '') ? 'Livraison: '.trim($offer['Livraison']) : null,
            'Source import: '.($offer['ID Offre'] ?? 'offre'),
        ])->filter()->implode("\n");

        $listing = Listing::query()->updateOrCreate(
            ['user_id' => $user->id, 'title' => Str::limit($title, 180, '')],
            [
                'category_id' => $category->id,
                'type' => 'offer',
                'product' => $product,
                'description' => $description,
                'quantity' => $this->toNumber($offer['Quantité disponible'] ?? ''),
                'unit' => trim($offer['Unité'] ?? '') ?: 'unite',
                'price' => $this->toPrice($offer['Prix'] ?? ''),
                'is_negotiable' => true,
                'location' => trim($offer['Localisation'] ?? '') ?: trim($offer['Région'] ?? '') ?: 'Senegal',
                'available_from' => $this->parseDate($offer['Date de mise à jour'] ?? '') ?? now()->toDateString(),
                'expires_at' => now()->addDays(90),
                'status' => $this->listingStatus($offer['Statut'] ?? ''),
            ],
        );

        $this->attachPhoto($listing, $this->photoFor($categoryName, $product));
    }

    private function upsertVendorListing(array $vendor, array $categories): void
    {
        $activity = trim($vendor['Activité principale'] ?? '');

        if ($activity === '') {
            return;
        }

        $user = $this->usersByVendorId[$vendor['2.0']] ?? $this->upsertVendor($vendor);
        $categoryName = $this->inferCategoryFromText($activity);
        $category = $categories[$categoryName] ?? $categories['Materiel agricole'];
        $title = Str::limit(Str::ucfirst($activity), 170, '');
        $priceText = trim($vendor['Prix produits'] ?? '');
        $description = collect([
            $activity,
            $priceText ? 'Prix indique: '.$priceText : null,
            trim($vendor['Commentaire de vérification'] ?? ''),
            'Source import: '.($vendor['2.0'] ?? 'vendeur'),
        ])->filter()->unique()->implode("\n");

        $listing = Listing::query()->updateOrCreate(
            ['user_id' => $user->id, 'title' => $title],
            [
                'category_id' => $category->id,
                'type' => 'offer',
                'product' => Str::limit($activity, 120, ''),
                'description' => $description,
                'quantity' => null,
                'unit' => 'unite',
                'price' => $this->toPrice($priceText),
                'is_negotiable' => true,
                'location' => trim($vendor['Localisation (description libre)'] ?? '') ?: trim($vendor['Région'] ?? '') ?: 'Senegal',
                'available_from' => now()->toDateString(),
                'expires_at' => now()->addDays(90),
                'status' => 'active',
            ],
        );

        $this->attachPhoto($listing, $this->photoFor($categoryName, $activity));
    }

    private function normalizeCategory(string $category, string $product = ''): string
    {
        $key = Str::lower(Str::ascii(trim($category)));

        return match ($key) {
            'cereales' => 'Cereales',
            'legumes' => 'Legumes',
            'legumineuses' => 'Legumineuses',
            'fruits' => 'Fruits',
            'tubercules' => 'Tubercules',
            'produits horticoles' => 'Produits horticoles',
            'produits d elevage', 'produits avicoles' => 'Elevage',
            default => $this->inferCategoryFromText($product),
        };
    }

    private function inferCategoryFromText(string $text): string
    {
        $value = Str::lower(Str::ascii($text));

        if (Str::contains($value, ['semence', 'engrais', 'fumier', 'biostimulant', 'aphasol', 'bio foliaire'])) {
            return 'Intrants';
        }

        if (Str::contains($value, ['dinde', 'pigeon', 'couveuse', 'elevage', 'poulet', 'betail'])) {
            return 'Elevage';
        }

        if (Str::contains($value, ['forage', 'pompe', 'panneau', 'reservoir', 'kit', 'gaine', 'asperceur', 'decortiseur', 'pulverisateur', 'botte', 'tracteur', 'equipement'])) {
            return 'Materiel agricole';
        }

        if (Str::contains($value, ['pepiniere', 'citronnier', 'grenadier', 'goyavier', 'verger', 'oranger', 'mandarinier', 'manguier'])) {
            return 'Produits horticoles';
        }

        if (Str::contains($value, ['arachide', 'niebe', 'haricot'])) {
            return 'Legumineuses';
        }

        if (Str::contains($value, ['oignon', 'piment', 'poivron', 'pasteque'])) {
            return 'Legumes';
        }

        return 'Materiel agricole';
    }

    private function normalizePhone(string $value): ?string
    {
        $digits = preg_replace('/\D+/', '', $value);

        if (! $digits) {
            return null;
        }

        if (preg_match('/(?:221)?(7[05678]\d{7})/', $digits, $matches)) {
            return '+221'.$matches[1];
        }

        return null;
    }

    private function toNumber(string $value): ?float
    {
        $price = $this->toPrice($value);

        return $price === null ? null : (float) $price;
    }

    private function toPrice(string $value): ?float
    {
        $value = trim($value);

        if ($value === '' || $value === '0') {
            return null;
        }

        if (! preg_match('/\d[\d\s.]*(?:,\d+)?/', $value, $matches)) {
            return null;
        }

        $raw = str_replace(' ', '', $matches[0]);

        if (preg_match('/\.\d{1,2}$/', $raw)) {
            return (float) $raw;
        }

        return (float) str_replace(['.', ','], ['', '.'], $raw);
    }

    private function parseDate(string $value): ?string
    {
        if (trim($value) === '') {
            return null;
        }

        try {
            return Carbon::parse($value)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }

    private function listingStatus(string $status): string
    {
        $value = Str::lower(Str::ascii($status));

        if (Str::contains($value, ['epuise', 'vendu'])) {
            return 'sold';
        }

        if (Str::contains($value, ['brouillon', 'expire', 'suspend'])) {
            return 'draft';
        }

        return 'active';
    }

    private function photoFor(string $category, string $product): string
    {
        $value = Str::lower(Str::ascii($category.' '.$product));

        return match (true) {
            Str::contains($value, ['oignon']) => 'listings/demo/oignons.png',
            Str::contains($value, ['tomate', 'piment', 'poivron']) => 'listings/demo/tomates.png',
            Str::contains($value, ['mangue', 'citron', 'orange', 'goyave', 'grenadier', 'verger', 'pepiniere']) => 'listings/demo/mangues.png',
            Str::contains($value, ['riz', 'mil', 'cereale']) => 'listings/demo/riz.png',
            Str::contains($value, ['manioc', 'tubercule']) => 'listings/demo/manioc.png',
            Str::contains($value, ['elevage', 'poulet', 'dinde', 'pigeon', 'couveuse']) => 'listings/demo/agri-products.png',
            default => 'listings/demo/intrants.png',
        };
    }

    private function attachPhoto(Listing $listing, string $path): void
    {
        $listing->photos()->firstOrCreate(
            ['path' => $path],
            ['sort_order' => 0],
        );
    }
}
