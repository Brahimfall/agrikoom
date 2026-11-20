<?php

namespace Database\Seeders;

use App\Models\JobOffer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class JobOfferSeeder extends Seeder
{
    public function run(): void
    {
        $offers = [
            [
                'title' => 'Technicien agricole',
                'company' => 'AGROPONIC SARL',
                'location' => 'Mermoz Pyrotechnie, Dakar',
                'contract_type' => 'Disponibilite immediate souhaitee',
                'summary' => 'AGROPONIC SARL recrute un profil experimente pour accompagner la production agricole, avec une expertise particuliere en hydroponie.',
                'details' => 'Maitrise des techniques de production agricole, travail terrain, gestion d\'exploitations agricoles, organisation, rigueur et esprit d\'initiative.',
                'profile' => 'Minimum 5 ans d\'experience dans le domaine agricole, avec une tres bonne connaissance de l\'hydroponie.',
                'apply_email' => 'contact@agroponic-sn.com',
                'is_featured' => true,
            ],
            [
                'title' => 'Directeur d\'abattoir',
                'company' => 'Olam Agri / Avisen SARL',
                'location' => 'Dakar, Senegal',
                'contract_type' => 'Direction industrielle',
                'summary' => 'Pilotage global des activites d\'abattoir pour garantir production, qualite, hygiene, securite alimentaire et conformite reglementaire.',
                'details' => 'Supervision de l\'abattage et de la transformation, objectifs de rendement, ressources, flux operationnels, audits, conformite sanitaire et encadrement des equipes.',
                'profile' => '10 a 15 ans d\'experience dans l\'agroalimentaire, l\'abattage ou les produits carnes. Bac+4/5 en genie agroalimentaire, production animale, genie industriel ou equivalent. Anglais professionnel confirme.',
                'apply_url' => 'https://careers.olamagri.com/job/Directeur-Abattoir/59498344/',
                'is_featured' => true,
            ],
            [
                'title' => 'Charge(e) Marketing Agroalimentaire',
                'company' => 'Lateef Entreprise',
                'location' => 'Dakar, Senegal',
                'contract_type' => 'CDD',
                'summary' => 'Developper et executer la strategie marketing pour promouvoir des produits agroalimentaires sur les marches locaux et internationaux.',
                'details' => 'Etudes de marche, plans marketing, communication, campagnes media, digital, innovation produit, support commercial et reporting de performance.',
                'profile' => 'Formation marketing, communication ou commerce. 2 a 5 ans d\'experience en marketing produit ou digital, idealement dans l\'agroalimentaire.',
                'apply_url' => 'https://www.emploidakar.com/offre-demploi/lateef-entreprise-dakar-cdd-charge-e-marketing-agroalimentaire/',
                'is_featured' => true,
            ],
            [
                'title' => 'Chef de secteur junior',
                'company' => 'SOBOA',
                'location' => 'Dakar et regions',
                'contract_type' => 'Commercial terrain',
                'deadline' => '2026-10-01',
                'summary' => 'La SOBOA recherche des chefs de secteur junior motives pour rejoindre sa force de vente a Dakar et dans les regions.',
                'details' => 'Developpement commercial terrain, suivi de secteur et appui a la distribution.',
                'profile' => 'Formation en commerce ou marketing, 6 mois a 1 an d\'experience commerciale, permis obligatoire, conduite moto ou tricycle.',
                'apply_email' => 'sobo.recruit@castel-afrique.com',
                'is_featured' => true,
            ],
            [
                'title' => 'Vendeuse',
                'company' => 'Activite bouillie de mil et thiakry',
                'location' => 'Keur Massar, Dakar',
                'contract_type' => 'Phase pilote',
                'summary' => 'Recherche d\'une vendeuse dynamique pour commercialiser bouillie de mil, thiakry, galaxx et produits conditionnes en pots individuels.',
                'details' => 'Commercialisation, presentation et conservation des produits, developpement clientele, prise de commandes, encaissement, suivi quotidien des ventes et retours clients.',
                'profile' => 'Personne serieuse, ponctuelle, honnete, autonome et a l\'aise avec les clients. Experience vente ou commerce appreciee. Forfait annonce: 3000 FCFA/jour.',
                'apply_email' => 'campanybusiness1000@gmail.com',
            ],
            [
                'title' => '(03) Operateurs Ensacheuses',
                'company' => 'SOSEDINE / Chef Aida',
                'location' => 'Senegal',
                'contract_type' => 'CDD/CDI',
                'summary' => 'SOSEDINE recherche trois operateurs ensacheuses pour son usine de production.',
                'details' => 'Conduite de machines et operations de conditionnement en environnement agroalimentaire.',
                'profile' => 'CAP, BEP ou BT en agroalimentaire, electromecanique, electrotechnique, mecanique ou conduite de machines, avec 1 a 2 ans d\'experience.',
                'apply_email' => 'elolo22.dossou@gmail.com',
            ],
            [
                'title' => '(02) Operateurs Process',
                'company' => 'SOSEDINE / Chef Aida',
                'location' => 'Senegal',
                'contract_type' => 'CDD/CDI',
                'summary' => 'SOSEDINE recherche deux operateurs process pour son usine de production.',
                'details' => 'Fabrication et suivi process dans la production agroalimentaire.',
                'profile' => 'Niveau BT en agroalimentaire, avec experience en fabrication moutarde et mayonnaise.',
                'apply_email' => 'elolo22.dossou@gmail.com',
            ],
            [
                'title' => 'Commerciaux terrain',
                'company' => 'SIAD Tampico',
                'location' => 'Senegal',
                'contract_type' => 'Commercial terrain',
                'summary' => 'SIAD TAMPICO recrute des commerciaux terrain pour vendre et distribuer ses produits aupres des grossistes.',
                'details' => 'Developpement du portefeuille, distribution terrain, negociation et animation du circuit grossiste.',
                'profile' => 'Bac+2 en marketing, commerce ou communication. Minimum 2 ans d\'experience, portefeuille clients actif, bonne connaissance terrain et orientation resultats.',
                'apply_email' => 'rh@siad.sn',
            ],
            [
                'title' => 'Agents de terrain (facilitateurs communautaires/agents de vulgarisation)',
                'company' => 'Societe internationale de developpement',
                'location' => 'Niger',
                'contract_type' => 'Projet agro-transformation',
                'summary' => 'Recrutement d\'agents de terrain pour soutenir un projet d\'agro-transformation solaire, inclusion economique et nutrition au Niger.',
                'details' => 'Mise en service d\'unites agro-transformation solaires, formation femmes et jeunes, appui aux relais communautaires, collecte de donnees et documentation des resultats.',
                'profile' => 'BEPC+2/3 a Bac+2 avec au moins 2 ans d\'experience en mobilisation communautaire, formation, suivi technique, collecte de donnees ou diffusion des connaissances.',
                'apply_email' => 'recrutement@sdi-qc.org',
            ],
            [
                'title' => 'Jeune Professionnel (Nutrition et systemes alimentaires)',
                'company' => 'FAO',
                'location' => 'Dakar, Senegal',
                'contract_type' => 'Contrat a duree determinee de deux ans',
                'deadline' => '2026-10-07',
                'summary' => 'Poste P-1 au Bureau sous-regional de la FAO pour l\'Afrique de l\'Ouest, centre sur nutrition et transformation des systemes agroalimentaires.',
                'details' => 'Appui aux programmes nutritionnels, systemes alimentaires durables, politiques publiques, suivi de projets et collaboration avec les equipes techniques regionales.',
                'profile' => 'Profil jeune professionnel avec expertise en nutrition, agriculture, developpement rural ou systemes alimentaires. Reference poste: 2601931.',
            ],
            [
                'title' => 'Pointeur produits finis',
                'company' => 'SOLIBRA',
                'location' => 'Abidjan Treichville, Cote d\'Ivoire',
                'contract_type' => 'Production / stock',
                'summary' => 'Suivi, controle et verification des produits finis en bout de lignes de production.',
                'details' => 'Gestion de stock, controle des sorties et expeditions de produits finis vers d\'autres sites.',
                'profile' => 'La connaissance de l\'outil informatique est un atout.',
                'apply_url' => 'https://www.novojob.com/cote-d-ivoire/entreprise/solibra-societe-de-limonaderies-et-de-brasseries-d-afrique/offres-d-emploi/offre-d-emploi/136856-pointeur-produits-finis-f-h-abidjan-cote-d-ivoire',
            ],
            [
                'title' => 'Coordonnateur de la thematique Jeunes, Femmes, Strategies entrepreneuriales et de mobilite',
                'company' => 'IPAR Think Tank',
                'location' => 'Senegal',
                'contract_type' => 'Coordination recherche',
                'summary' => 'IPAR recherche un profil senior pour piloter la thematique jeunes, femmes, strategies entrepreneuriales et mobilite.',
                'details' => 'Pilotage scientifique, coordination de projets, mobilisation de financements, partenariats, production de connaissances et influence des politiques publiques.',
                'profile' => 'Doctorat souhaite ou master avec experience substantielle en economie du developpement, economie agricole, genre, sociologie, politiques publiques, migrations ou domaines connexes.',
                'apply_url' => 'https://ipar.sn/avis-de-recrutement-dune-coordonnatrice-coordonnateur-de-la-thematique-jeunes-femmes-strategies-entrepreneuriales-et-de-mobilite/',
            ],
            [
                'title' => 'Charge(e) de programme (Technologue alimentaire) NOB',
                'company' => 'PAM / WFP',
                'location' => 'Afrique de l\'Ouest et centrale',
                'contract_type' => 'NOB',
                'summary' => 'Le PAM recherche un technologue alimentaire/nutritionniste pour renforcer les chaines de valeur nutritionnelles et l\'enrichissement alimentaire.',
                'details' => 'Soutien technique aux producteurs locaux, transformation alimentaire, formulation produits, BPF, BPH, assurance qualite, fortification alimentaire et gestion des connaissances.',
                'profile' => 'Diplome universitaire superieur en technologie alimentaire, genie alimentaire, agroalimentaire, sciences de la sante, nutrition ou equivalent. Au moins 3 ans d\'experience professionnelle post-universitaire.',
            ],
            [
                'title' => 'Superviseur de Quart',
                'company' => 'Laiterie du Berger',
                'location' => 'Sandiara / Senegal',
                'contract_type' => 'CDI',
                'summary' => 'Supervision de ligne de conditionnement pour atteindre les objectifs de production en quantite, qualite, delai et securite.',
                'details' => 'Gestion d\'equipe, controle qualite, suivi production, maintenance, reporting, respect HACCP, hygiene, securite alimentaire et amelioration continue.',
                'profile' => 'Ingenieur electromecanique ou diplome equivalent, minimum 1 an d\'experience en conditionnement, connaissance des process agroalimentaires et capacite d\'encadrement.',
                'apply_url' => 'https://www.emploidakar.com/offre-demploi/la-laiterie-du-berger-sandiara-cdi-superviseur-de-quart/',
            ],
            [
                'title' => 'Directeur des cultures en zones arides',
                'company' => 'CIMMYT',
                'location' => 'Dakar, Senegal',
                'contract_type' => 'Contrat international de 3 ans',
                'deadline' => '2026-09-30',
                'summary' => 'Direction du programme mondial sur les cultures en zones arides pour renforcer la resilience, la securite alimentaire et l\'impact scientifique.',
                'details' => 'Leadership scientifique, adoption de l\'innovation, partenariats strategiques, mobilisation de ressources, gestion d\'equipes internationales et transformation des systemes agricoles en zones arides.',
                'profile' => '10 a 15 ans et plus d\'experience en leadership scientifique ou programmatique, doctorat en agronomie, sciences vegetales, phytotechnie, developpement rural ou domaine connexe. Anglais excellent, francais fortement apprecie.',
                'apply_url' => 'https://www.emploidakar.com/offre-demploi/dryland-crops-director/',
            ],
        ];

        foreach ($offers as $offer) {
            JobOffer::query()->updateOrCreate(
                ['slug' => Str::slug($offer['title'])],
                $offer + ['status' => 'active'],
            );
        }
    }
}
