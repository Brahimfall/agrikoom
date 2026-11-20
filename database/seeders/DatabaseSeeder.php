<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'fallbrahim2000@gmail.com'],
            [
                'name' => 'Brahim Fall',
                'phone' => '+221778440435',
                'location' => 'Dakar, Sénégal',
                'actor_type' => 'both',
                'description' => 'Super Administrateur de la plateforme AgriKoom.',
                'phone_verified_at' => now(),
                'email_verified_at' => now(),
                'is_admin' => true,
                'password' => Hash::make('password'),
            ],
        );

        $producer = User::query()->updateOrCreate(
            ['email' => 'producteur@agrikoom.test'],
            [
                'name' => 'Cooperative des Niayes',
                'phone' => '+221770000001',
                'location' => 'Thies',
                'actor_type' => 'producer',
                'description' => 'Producteurs locaux de legumes frais, riz et vivriers de saison.',
                'phone_verified_at' => now(),
                'password' => Hash::make('password'),
            ],
        );

        $buyer = User::query()->updateOrCreate(
            ['email' => 'acheteur@agrikoom.test'],
            [
                'name' => 'Achat Marche Sandaga',
                'phone' => '+221770000002',
                'location' => 'Dakar',
                'actor_type' => 'buyer',
                'phone_verified_at' => now(),
                'password' => Hash::make('password'),
            ],
        );

        $mangoProducer = User::query()->updateOrCreate(
            ['email' => 'mangues@agrikoom.test'],
            [
                'name' => 'Vergers de Casamance',
                'phone' => '+221770000003',
                'location' => 'Ziguinchor',
                'actor_type' => 'producer',
                'description' => 'Collecte et vente de fruits de saison en gros et demi-gros.',
                'phone_verified_at' => now(),
                'password' => Hash::make('password'),
            ],
        );

        $cooperative = User::query()->updateOrCreate(
            ['email' => 'coop@agrikoom.test'],
            [
                'name' => 'Coop Bassin Arachidier',
                'phone' => '+221770000004',
                'location' => 'Kaolack',
                'actor_type' => 'producer',
                'description' => 'Cooperative agricole specialisee en vivriers et legumes.',
                'phone_verified_at' => now(),
                'password' => Hash::make('password'),
            ],
        );

        $breeder = User::query()->updateOrCreate(
            ['email' => 'elevage@agrikoom.test'],
            [
                'name' => 'Ferme Teranga Elevage',
                'phone' => '+221770000005',
                'location' => 'Louga',
                'actor_type' => 'producer',
                'description' => 'Eleveur specialise en ovins, volailles et alimentation betail.',
                'phone_verified_at' => now(),
                'password' => Hash::make('password'),
            ],
        );

        $equipmentSeller = User::query()->updateOrCreate(
            ['email' => 'materiel@agrikoom.test'],
            [
                'name' => 'Agro Equipements Senegal',
                'phone' => '+221770000006',
                'location' => 'Dakar',
                'actor_type' => 'producer',
                'description' => 'Vente de petits materiels agricoles, pieces et accessoires.',
                'phone_verified_at' => now(),
                'password' => Hash::make('password'),
            ],
        );

        $categories = collect([
            ['name' => 'Cereales', 'description' => 'Riz, mais, mil, sorgho'],
            ['name' => 'Elevage', 'description' => 'Ovins, bovins, volailles, alimentation betail'],
            ['name' => 'Fruits', 'description' => 'Mangues, bananes, agrumes'],
            ['name' => 'Legumes', 'description' => 'Tomates, oignons, piments'],
            ['name' => 'Materiel agricole', 'description' => 'Motoculteurs, pompes, pulverisateurs, outils'],
            ['name' => 'Tubercules', 'description' => 'Igname, manioc, pomme de terre'],
            ['name' => 'Intrants', 'description' => 'Semences, engrais, materiel'],
        ])->mapWithKeys(function (array $category) {
            $model = Category::query()->firstOrCreate(
                ['slug' => Str::slug($category['name'])],
                $category + ['is_active' => true],
            );

            return [$category['name'] => $model];
        });

        Listing::query()
            ->whereIn('title', [
                'Riz local disponible en sacs de 50 kg',
                'Tomates fraiches pour restaurants et marches',
                'Mangues Kent calibre export',
                'Oignons violets sac de 25 kg',
            ])
            ->delete();

        $demoListings = [
            [
                'title' => 'Riz local de la vallee en sacs de 50 kg',
                'user' => $producer,
                'category' => 'Cereales',
                'type' => 'offer',
                'product' => 'Riz',
                'description' => 'Stock propre, trie et disponible pour livraison rapide aux grossistes, restaurants et boutiques.',
                'quantity' => 120,
                'unit' => 'sacs',
                'price' => 18500,
                'location' => 'Saint-Louis',
                'photo' => 'listings/demo/riz.png',
            ],
            [
                'title' => 'Tomates fraiches des Niayes',
                'user' => $cooperative,
                'category' => 'Legumes',
                'type' => 'offer',
                'product' => 'Tomates',
                'description' => 'Tomates fraiches recoltees le matin, disponibles en caisse pour commandes regulieres.',
                'quantity' => 450,
                'unit' => 'kg',
                'price' => 650,
                'location' => 'Thies',
                'photo' => 'listings/demo/tomates.png',
            ],
            [
                'title' => 'Mangues Kent de Casamance',
                'user' => $mangoProducer,
                'category' => 'Fruits',
                'type' => 'offer',
                'product' => 'Mangues',
                'description' => 'Mangues de saison, tri possible par calibre, livraison a discuter selon volume.',
                'quantity' => 80,
                'unit' => 'caisses',
                'price' => 9000,
                'location' => 'Ziguinchor',
                'photo' => 'listings/demo/mangues.png',
            ],
            [
                'title' => 'Manioc frais en gros',
                'user' => $producer,
                'category' => 'Tubercules',
                'type' => 'offer',
                'product' => 'Manioc',
                'description' => 'Manioc frais pour gari, farine et transformation artisanale. Chargement possible sur place.',
                'quantity' => 3,
                'unit' => 'tonnes',
                'price' => 95000,
                'location' => 'Kolda',
                'photo' => 'listings/demo/manioc.png',
            ],
            [
                'title' => 'Oignons de Podor sac de 25 kg',
                'user' => $cooperative,
                'category' => 'Legumes',
                'type' => 'offer',
                'product' => 'Oignons',
                'description' => 'Oignons secs bien conserves, vente au sac pour revendeurs et restaurants.',
                'quantity' => 75,
                'unit' => 'sacs',
                'price' => 12500,
                'location' => 'Podor',
                'photo' => 'listings/demo/oignons.png',
            ],
            [
                'title' => 'Semences et engrais pour campagne agricole',
                'user' => $mangoProducer,
                'category' => 'Intrants',
                'type' => 'offer',
                'product' => 'Intrants agricoles',
                'description' => 'Lots de semences et engrais pour producteurs. Prix negociable selon volume.',
                'quantity' => 40,
                'unit' => 'lots',
                'price' => 25000,
                'location' => 'Dakar',
                'photo' => 'listings/demo/intrants.png',
            ],
            [
                'title' => 'Moutons disponibles pour elevage et embouche',
                'user' => $breeder,
                'category' => 'Elevage',
                'type' => 'offer',
                'product' => 'Moutons',
                'description' => 'Lots de moutons suivis, vente a lunite ou par petit lot. Prix selon taille et poids.',
                'quantity' => 18,
                'unit' => 'tetes',
                'price' => 85000,
                'location' => 'Louga',
                'photo' => 'listings/demo/agri-products.png',
            ],
            [
                'title' => 'Poulets de chair prets pour grossistes',
                'user' => $breeder,
                'category' => 'Elevage',
                'type' => 'offer',
                'product' => 'Poulets de chair',
                'description' => 'Poulets disponibles pour restaurants, revendeurs et evenements. Livraison a discuter selon volume.',
                'quantity' => 250,
                'unit' => 'pieces',
                'price' => 3500,
                'location' => 'Mbour',
                'photo' => 'listings/demo/agri-products.png',
            ],
            [
                'title' => 'Motoculteur occasion bon etat',
                'user' => $equipmentSeller,
                'category' => 'Materiel agricole',
                'type' => 'offer',
                'product' => 'Motoculteur',
                'description' => 'Motoculteur revise pour maraichage et petites exploitations. Essai possible sur rendez-vous.',
                'quantity' => 1,
                'unit' => 'unite',
                'price' => 450000,
                'location' => 'Dakar',
                'photo' => 'listings/demo/intrants.png',
            ],
            [
                'title' => 'Recherche pompe solaire pour irrigation',
                'user' => $buyer,
                'category' => 'Materiel agricole',
                'type' => 'request',
                'product' => 'Pompe solaire',
                'description' => 'Recherche pompe solaire pour exploitation maraichere dans les Niayes. Budget selon puissance.',
                'quantity' => 1,
                'unit' => 'unite',
                'price' => null,
                'location' => 'Thies',
                'photo' => 'listings/demo/intrants.png',
            ],
            [
                'title' => 'Recherche tomates fraiches pour restaurant',
                'user' => $buyer,
                'category' => 'Legumes',
                'type' => 'request',
                'product' => 'Tomates',
                'description' => 'Besoin hebdomadaire avec qualite constante pour restaurant et service traiteur.',
                'quantity' => 300,
                'unit' => 'kg',
                'price' => null,
                'location' => 'Dakar',
                'photo' => 'listings/demo/tomates.png',
            ],
            [
                'title' => 'Recherche riz local pour boutique de quartier',
                'user' => $buyer,
                'category' => 'Cereales',
                'type' => 'request',
                'product' => 'Riz local',
                'description' => 'Achat regulier de sacs de riz local avec possibilite de contrat mensuel.',
                'quantity' => 50,
                'unit' => 'sacs',
                'price' => 18000,
                'location' => 'Dakar',
                'photo' => 'listings/demo/riz.png',
            ],
        ];

        foreach ($demoListings as $demoListing) {
            $photoPath = $demoListing['photo'];
            $listing = Listing::query()->updateOrCreate(
                ['title' => $demoListing['title']],
                [
                    'user_id' => $demoListing['user']->id,
                    'category_id' => $categories[$demoListing['category']]->id,
                    'type' => $demoListing['type'],
                    'product' => $demoListing['product'],
                    'description' => $demoListing['description'],
                    'quantity' => $demoListing['quantity'],
                    'unit' => $demoListing['unit'],
                    'price' => $demoListing['price'],
                    'is_negotiable' => true,
                    'location' => $demoListing['location'],
                    'available_from' => now()->toDateString(),
                    'expires_at' => now()->addDays(30),
                    'status' => 'active',
                ],
            );

            $listing->photos()->firstOrCreate(
                ['path' => $photoPath],
                ['sort_order' => 0],
            );
        }

        $this->call(AgriKoomWorkbookSeeder::class);
        $this->call(JobOfferSeeder::class);
        $this->call(MarketPriceSeeder::class);
        $this->call(ArticleSeeder::class);
    }
}
