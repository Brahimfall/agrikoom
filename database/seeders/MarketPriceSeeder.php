<?php

namespace Database\Seeders;

use App\Models\MarketPrice;
use Illuminate\Database\Seeder;

class MarketPriceSeeder extends Seeder
{
    public function run(): void
    {
        $prices = [
            [
                'product_name'   => 'Riz paddy / blanc',
                'ticker_code'    => 'SEN-RIZ',
                'category'       => 'Céréales',
                'price_range'    => '17 500 – 19 000 FCFA',
                'previous_price' => '18 200 – 19 800 FCFA',
                'change_percent' => -3.50,
                'unit'           => 'Sac de 50 kg',
                'location'       => 'Vallée du fleuve (Saint-Louis, Dagana, Podor)',
                'trend'          => 'down',
                'trend_label'    => 'Moins cher',
                'source'         => 'Marchés régionaux & coopératives',
                'notes'          => 'Riz blanc brisé et paddy bord-champ',
                'is_active'      => true,
                'sort_order'     => 1,
            ],
            [
                'product_name'   => 'Oignon local',
                'ticker_code'    => 'SEN-OIGN',
                'category'       => 'Légumes & Maraîchage',
                'price_range'    => '12 000 – 13 500 FCFA',
                'previous_price' => '11 000 – 12 200 FCFA',
                'change_percent' => 8.20,
                'unit'           => 'Sac de 25 kg',
                'location'       => 'Zone des Niayes (Thiès, Potou, Mboro)',
                'trend'          => 'up',
                'trend_label'    => 'Plus cher',
                'source'         => 'Marchés de gros & producteurs',
                'notes'          => 'Oignon violet et jaune bien séché',
                'is_active'      => true,
                'sort_order'     => 2,
            ],
            [
                'product_name'   => 'Prestation de labour',
                'ticker_code'    => 'SEN-LABR',
                'category'       => 'Matériel & Services',
                'price_range'    => '25 000 – 30 000 FCFA',
                'previous_price' => '25 000 – 30 000 FCFA',
                'change_percent' => 0.00,
                'unit'           => 'Tarif par hectare',
                'location'       => 'Toutes zones agricoles',
                'trend'          => 'stable',
                'trend_label'    => 'Stable',
                'source'         => 'GIE & prestataires machinisme',
                'notes'          => 'Tracteur + chauffeur + équipement',
                'is_active'      => true,
                'sort_order'     => 3,
            ],
            [
                'product_name'   => 'Bélier de race',
                'ticker_code'    => 'SEN-BLIR',
                'category'       => 'Élevage & Bétail',
                'price_range'    => '160 000 – 780 000 FCFA',
                'previous_price' => '150 000 – 740 000 FCFA',
                'change_percent' => 5.40,
                'unit'           => 'Par tête',
                'location'       => 'Dakar, Thiès, Louga, Kaolack',
                'trend'          => 'up',
                'trend_label'    => 'Plus cher',
                'source'         => 'Éleveurs & loumas hebdomadaires',
                'notes'          => 'Ladoum / Touabire selon gabarit et âge',
                'is_active'      => true,
                'sort_order'     => 4,
            ],
            [
                'product_name'   => 'Maïs local grain',
                'ticker_code'    => 'SEN-MAIS',
                'category'       => 'Céréales',
                'price_range'    => '13 500 – 15 500 FCFA',
                'previous_price' => '13 500 – 15 500 FCFA',
                'change_percent' => 0.00,
                'unit'           => 'Sac de 50 kg',
                'location'       => 'Bassin arachidier & Saloum (Kaolack, Nioro)',
                'trend'          => 'stable',
                'trend_label'    => 'Stable',
                'source'         => 'Coopératives céréalières',
                'notes'          => 'Maïs jaune de qualité supérieure',
                'is_active'      => true,
                'sort_order'     => 5,
            ],
            [
                'product_name'   => 'Pomme de terre locale',
                'ticker_code'    => 'SEN-PDTR',
                'category'       => 'Légumes & Maraîchage',
                'price_range'    => '11 500 – 13 000 FCFA',
                'previous_price' => '12 500 – 14 000 FCFA',
                'change_percent' => -6.50,
                'unit'           => 'Sac de 25 kg',
                'location'       => 'Zone des Niayes (Mboro, Kayar)',
                'trend'          => 'down',
                'trend_label'    => 'Moins cher',
                'source'         => 'Marchés régionaux',
                'notes'          => 'Calibre moyen et gros, récolte fraîche',
                'is_active'      => true,
                'sort_order'     => 6,
            ],
            [
                'product_name'   => 'Arachide décortiquée',
                'ticker_code'    => 'SEN-ARAC',
                'category'       => 'Céréales & Oléagineux',
                'price_range'    => '27 500 – 30 000 FCFA',
                'previous_price' => '28 800 – 31 500 FCFA',
                'change_percent' => -4.20,
                'unit'           => 'Sac de 50 kg',
                'location'       => 'Bassin arachidier (Kaolack, Touba)',
                'trend'          => 'down',
                'trend_label'    => 'Moins cher',
                'source'         => 'Loumas ruraux & huileries',
                'notes'          => 'Graines saines et triées',
                'is_active'      => true,
                'sort_order'     => 7,
            ],
            [
                'product_name'   => 'Tomate fraîche de table',
                'ticker_code'    => 'SEN-TOMA',
                'category'       => 'Légumes & Maraîchage',
                'price_range'    => '8 500 – 10 500 FCFA',
                'previous_price' => '7 500 – 9 200 FCFA',
                'change_percent' => 12.50,
                'unit'           => 'Caisse de 20 kg',
                'location'       => 'Niayes & Vallée du fleuve',
                'trend'          => 'up',
                'trend_label'    => 'Plus cher',
                'source'         => 'Marchés de gros Diaobé & Castors',
                'notes'          => 'Forte demande marché urbain',
                'is_active'      => true,
                'sort_order'     => 8,
            ],
        ];

        foreach ($prices as $priceData) {
            MarketPrice::query()->updateOrCreate(
                ['product_name' => $priceData['product_name']],
                $priceData
            );
        }
    }
}

