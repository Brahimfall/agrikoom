<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function listings()
    {
        return $this->hasMany(Listing::class);
    }

    public function getImageUrlAttribute(): string
    {
        $val = $this->image ?? null;
        if (! empty($val)) {
            if (str_starts_with($val, 'http')) {
                return $val;
            }
            if (str_starts_with($val, 'assets/')) {
                return asset($val);
            }
            return asset('storage/' . $val);
        }

        $map = [
            'Materiel agricole'   => 'assets/home/agri_tracteur.jpg',
            'Elevage'             => 'assets/home/agri_elevage.jpg',
            'Cereales'            => 'assets/home/prod_cereales.jpg',
            'Legumes'             => 'assets/home/prod_legumes.jpg',
            'Intrants'            => 'assets/home/prod_intrants.jpg',
            'Fruits'              => 'assets/home/prod_fruits.jpg',
            'Tubercules'          => 'assets/home/prod_manioc.jpg',
            'Legumineuses'        => 'assets/home/prod_legumes.jpg',
            'Produits horticoles' => 'assets/home/prod_horticoles.jpg',
        ];

        $path = $map[$this->name] ?? 'assets/home/prod_legumes.jpg';

        return asset($path);
    }

    public function getSubtitleAttribute(): string
    {
        if (! empty($this->description)) {
            return $this->description;
        }

        $map = [
            'Materiel agricole'   => 'Tracteurs, outils du sol, récolte',
            'Elevage'             => 'Bovins, ovins, caprins, provende',
            'Cereales'            => 'Riz de la vallée, maïs, mil, sorgho',
            'Legumes'             => 'Oignons des Niayes, tomates, piments',
            'Intrants'            => 'Semences certifiées, engrais, compost',
            'Fruits'              => 'Mangues, agrumes, papayes',
            'Tubercules'          => 'Manioc, patates douces, igname',
            'Legumineuses'        => 'Arachides, niébé, sésame',
            'Produits horticoles' => 'Production maraîchère et pépinières',
        ];

        return $map[$this->name] ?? 'Productions et équipements';
    }
}
