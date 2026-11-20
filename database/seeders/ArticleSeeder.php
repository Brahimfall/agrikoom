<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->where('is_admin', true)->first();

        $articles = [
            [
                'title' => "Guide pratique : Réussir la culture de l'oignon dans la zone des Niayes",
                'category' => 'Conseils & Pratiques',
                'excerpt' => "Du choix des semences de qualité jusqu'à la phase de séchage et de conservation en claies, découvrez les clés indispensables pour maximiser le rendement de vos parcelles maraîchères.",
                'image_path' => 'assets/home/prod_legumes.jpg',
                'reading_time' => 6,
                'is_featured' => true,
                'status' => 'published',
                'published_at' => now()->subDays(2),
                'content' => <<<MARKDOWN
La zone des Niayes concentre la majorité de la production maraîchère du Sénégal. La culture de l'oignon y représente une activité économique majeure, permettant de faire vivre des milliers d'exploitations familiales et de coopératives.

### 1. Choix variétal et qualité des semences
Pour obtenir des bulbes fermes, bien colorés et aptes au stockage, le choix de la semence est déterminant. Les variétés telles que *Violet de Galmi*, *Safari* ou *Texas Early Grano* sont particulièrement adaptées au climat sahélien. Veillez à vous approvisionner auprès de distributeurs agréés pour garantir un taux de germination supérieur à 85 %.

### 2. Conduite de la pépinière et repiquage
- **Durée de pépinière :** 40 à 45 jours après levée.
- **Taille optimale des plants :** diamètre d'un crayon (environ 6 à 8 mm).
- **Densité au repiquage :** espacement de 15 cm entre les rangs et 10 cm entre les plants sur planches surélevées.

Un habillage soigné des racines et des feuilles lors de la transplantation facilite une reprise vigoureuse sans stress hydrique.

### 3. Gestion de la fertilisation et des apports hydriques
L'oignon est exigeant en phosphore au démarrage pour le développement racinaire, puis en potassium pendant la bulbification. Un apport organique bien décomposé (fumier de bœuf ou de volaille) combiné à un engrais minéral équilibré (type 10-10-20 ou NPK adapté) garantit un calibre homogène. 

> **Astuce d'expert :** Stoppez les arrosages 10 à 15 jours avant la récolte pour favoriser la maturité des tuniques externes et éviter les pourritures bactériennes au stockage.

### 4. Récolte et conservation
La récolte intervient lorsque 60 à 70 % des tiges sont tombées au sol. L'étape de ressuyage à l'ombre (ou sous hangar aéré) pendant 7 à 10 jours est cruciale pour former une pelure protectrice sèche.
MARKDOWN
            ],
            [
                'title' => "Comment bien choisir son tracteur agricole d'occasion au Sénégal ?",
                'category' => 'Machinisme & Équipement',
                'excerpt' => "Puissance moteur, prise de force, disponibilité des filtres et vérification de la pompe hydraulique : la checklist complète avant tout achat de matériel agricole.",
                'image_path' => 'assets/home/agri_tracteur.jpg',
                'reading_time' => 5,
                'is_featured' => true,
                'status' => 'published',
                'published_at' => now()->subDays(5),
                'content' => <<<MARKDOWN
L'acquisition d'un tracteur est souvent l'investissement le plus important pour une exploitation agricole ou un groupement d'intérêt économique (GIE). Sur le marché sénégalais, le matériel d'occasion importé (Europe, Asie) représente une excellente alternative au neuf, à condition de vérifier rigoureusement l'état mécanique.

### 1. Quelle puissance pour quels travaux ?
- **45 à 65 CV :** Parfait pour le maraîchage, le transport de récoltes avec remorque 3 tonnes et les petits labours sur sols légers (Dior).
- **75 à 95 CV :** Le cœur du marché pour les exploitations polyvalentes (labours profonds, disquage, semis mécanique).
- **100 CV et plus :** Indispensable dans la vallée du fleuve pour le travail lourd des terres argileuses (Hollaldé) et les grands chantiers de nivellement.

### 2. Les points d'inspection incontournables
1. **Le moteur :** Vérifiez l'absence de fumée noire persistante ou blanche à chaud. Une surpression dans le carter d'huile (vapeurs abondantes au reniflard) indique souvent une segmentation usée.
2. **Le système hydraulique :** Testez le relevage 3 points avec une charge lourde (charrue ou cover-crop). Le relevage ne doit ni brouter, ni redescendre lentement à l'arrêt.
3. **La transmission et l'embrayage :** Passez toutes les vitesses et gammes (lentes, rapides, marche arrière) sous charge pour détecter d'éventuels bruits de pignonnerie.
4. **La disponibilité des pièces :** Privilégiez des marques largement implantées au Sénégal (Massey Ferguson, New Holland, John Deere, Fiat) où les filtres, injecteurs et courroies se trouvent facilement à Dakar, Thiès ou Saint-Louis.

### 3. Contact direct et essai sur parcelle
N'achetez jamais un tracteur uniquement sur photo. Exigez un essai de labour réel sur au moins 30 minutes de fonctionnement continu afin de contrôler la température d'eau et la pression d'huile.
MARKDOWN
            ],
            [
                'title' => "Élevage de moutons Ladoum : sélection génétique et alimentation à forte valeur",
                'category' => 'Élevage & Bétail',
                'excerpt' => "Véritable fleuron de l'élevage sénégalais, le bélier Ladoum demande un programme nutritionnel précis et une hygiène rigoureuse pour exprimer son plein potentiel génétique.",
                'image_path' => 'assets/home/agri_elevage.jpg',
                'reading_time' => 7,
                'is_featured' => false,
                'status' => 'published',
                'published_at' => now()->subDays(8),
                'content' => <<<MARKDOWN
Le mouton Ladoum n'est plus seulement une passion d'éleveur : c'est devenu une filière agro-pastorale de premier plan au Sénégal. Sa silhouette imposante, son chanfrein busqué et son gabarit hors norme nécessitent une conduite d'élevage spécialisée.

### 1. Critères de sélection d'un futur reproducteur
Lors de l'achat d'un jeune bélier ou d'une agnelle :
- **Hauteur au garrot :** Visez au minimum 85 cm à l'âge adulte pour un mâle d'élite.
- **Longueur du corps :** Le dos doit être droit, large et bien musclé.
- **Aplombs :** Des membres solides et d'aplomb évitent les boiteries lors de la prise de poids.
- **Généalogie :** Privilégiez des animaux dont les ascendants sont répertoriés dans les grands élevages connus.

### 2. Alimentation équilibrée et rationnement
Un Ladoum ne se nourrit pas uniquement de foin ordinaire. Sa masse corporelle exige une ration dense en énergie et en protéines :
- **Fourrages grossiers :** Foin d'arachide de première coupe, luzerne séchée, résidus de maïs broyés.
- **Concentrés :** Mélange de son de blé, tourteau de coton ou de soja, maïs concassé et mélasse.
- **Compléments minéraux et vitaminés (CMV) :** Pierre à lécher enrichie en oligo-éléments et sel indispensable pour prévenir les carences.

### 3. Suivi sanitaire et biosécurité
Un calendrier prophylactique strict doit être mis en place avec un vétérinaire :
- Déparasitage interne tous les 3 mois.
- Vaccination contre la peste des petits ruminants (PPR) et la clavelée.
- Bains désinfectants pour les sabots et litière toujours sèche.
MARKDOWN
            ],
            [
                'title' => "Maîtriser l'irrigation goutte-à-goutte : économiser 40% d'eau et doubler son rendement",
                'category' => 'Irrigation & Eau',
                'excerpt' => "Face aux coûts du carburant et au stress hydrique, l'irrigation localisée couplée à un pompage solaire offre un retour sur investissement rapide pour les exploitants maraîchers.",
                'image_path' => 'assets/home/market-hero.png',
                'reading_time' => 5,
                'is_featured' => false,
                'status' => 'published',
                'published_at' => now()->subDays(12),
                'content' => <<<MARKDOWN
Dans un contexte où les charges d'exploitation augmentent, la gestion rationnelle de l'eau est devenue la clé de voûte de la rentabilité agricole au Sénégal. Le système de goutte-à-goutte (micro-irrigation) révolutionne la production horticole.

### 1. Pourquoi abandonner l'aspersion et le gravitaire ?
L'irrigation par submersion entraîne d'importantes pertes par évaporation et asphyxie souvent les racines dans les sols lourds. Le goutte-à-goutte apporte l'eau et les engrais solubles (fertigation) directement au niveau du système racinaire, sans mouiller le feuillage, ce qui réduit drastiquement les maladies cryptogamiques (mildiou, oïdium).

### 2. Composition d'une installation type (1 hectare)
- **Tête de contrôle :** Filtre à disque ou à tamis (incontournable pour éviter le colmatage des goutteurs), manomètre et injecteur venturi pour les engrais.
- **Canalisations principales et secondaires :** Tuyaux PEHD résistant aux UV.
- **Gaines goutte-à-goutte :** Espacement de 20 à 30 cm entre goutteurs selon la culture (tomate, piment, pastèque).

### 3. L'atout du pompage solaire
Coupler le goutte-à-goutte avec des panneaux solaires photovoltaïques et une pompe immergée élimine définitivement les factures de gasoil ou d'électricité, rendant l'exploitation autonome et rentable dès la deuxième année.
MARKDOWN
            ],
            [
                'title' => "Tendances et perspectives du riz de la vallée du fleuve Sénégal en 2026",
                'category' => 'Marchés & Économie',
                'excerpt' => "Analyse de la campagne de contre-saison, évolution des coûts de transformation et débouchés directs du paddy vers les centres urbains.",
                'image_path' => 'assets/home/prod_cereales.jpg',
                'reading_time' => 4,
                'is_featured' => false,
                'status' => 'published',
                'published_at' => now()->subDays(15),
                'content' => <<<MARKDOWN
Le riz local sénégalais confirme sa place incontournable dans l'approvisionnement des ménages et des marchés de gros à Dakar, Touba et Saint-Louis. La qualité de l'usinage moderne a permis au riz de la vallée d'égaler, voire de surpasser les riz importés en termes de saveur et de fraîcheur.

### 1. Dynamique de la contre-saison chaude
Les rendements enregistrés dans les périmètres irrigués de Dagana, Podor et Matam atteignent en moyenne 6 à 7,5 tonnes par hectare grâce à l'ensoleillement optimal et à la maîtrise de l'eau. Les variétés *Sahel 108* et *Sahel 201* restent plébiscitées pour leur cycle court et leur excellent rendement à l'usinage.

### 2. Valorisation et commercialisation directe
Les producteurs qui s'organisent en coopératives ou qui utilisent les plateformes numériques comme AgriKoom pour vendre directement sans intermédiaires obtiennent une marge nette supérieure de 15 à 25 %. L'emballage en sacs de 25 kg et 50 kg étiquetés renforce la confiance du consommateur final.
MARKDOWN
            ],
        ];

        foreach ($articles as $item) {
            Article::query()->firstOrCreate(
                ['slug' => Str::slug($item['title'])],
                [
                    'title' => $item['title'],
                    'slug' => Str::slug($item['title']),
                    'category' => $item['category'],
                    'excerpt' => $item['excerpt'],
                    'content' => $item['content'],
                    'image_path' => $item['image_path'],
                    'author_name' => 'Rédaction AgriKoom',
                    'user_id' => $admin?->id,
                    'status' => $item['status'],
                    'is_featured' => $item['is_featured'],
                    'reading_time' => $item['reading_time'],
                    'views_count' => rand(85, 450),
                    'published_at' => $item['published_at'],
                ]
            );
        }
    }
}

