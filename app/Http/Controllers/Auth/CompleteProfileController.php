<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CompleteProfileController extends Controller
{
    /**
     * Liste officielle des 14 régions du Sénégal
     */
    public const REGIONS = [
        'Dakar',
        'Thiès',
        'Saint-Louis',
        'Kaolack',
        'Diourbel',
        'Fatick',
        'Kaffrine',
        'Kolda',
        'Louga',
        'Matam',
        'Sédhiou',
        'Tambacounda',
        'Kédougou',
        'Ziguinchor',
    ];

    /**
     * Affiche le formulaire de finalisation du profil
     */
    public function show(): View|RedirectResponse
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        return view('auth.complete-profile', [
            'user' => $user,
            'regions' => self::REGIONS,
        ]);
    }

    /**
     * Enregistre le numéro et la localisation
     */
    public function store(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $request->merge([
            'phone' => preg_replace('/[^\d+]/', '', (string) $request->phone),
        ]);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'min:9', 'max:20', 'unique:users,phone,' . $user->id],
            'region' => ['required', 'string', 'in:' . implode(',', self::REGIONS)],
            'city_commune' => ['nullable', 'string', 'max:100'],
            'actor_type' => ['required', 'string', 'in:producer,buyer,both'],
        ], [
            'name.required' => 'Votre nom ou nom d\'exploitation est obligatoire.',
            'phone.required' => 'Le numéro de téléphone ou WhatsApp est obligatoire pour que les acheteurs puissent vous joindre.',
            'phone.unique' => 'Ce numéro de téléphone est déjà associé à un autre compte.',
            'region.required' => 'Veuillez sélectionner votre région.',
            'region.in' => 'Veuillez sélectionner une région valide du Sénégal.',
        ]);

        // Construire la localisation complète : Région (+ commune/louma si précisé)
        $location = $request->region;
        if ($request->filled('city_commune')) {
            $location = trim($request->city_commune) . ', ' . $request->region;
        }

        $user->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'location' => $location,
            'actor_type' => $request->actor_type,
            'phone_verified_at' => $user->phone_verified_at ?: now(),
        ]);

        return redirect()->route('listings.create')->with('status', 'Votre profil est validé ! Vous pouvez maintenant publier votre première annonce ou produit sur AgriKoom.');
    }
}

