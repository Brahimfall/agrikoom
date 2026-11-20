<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirige l'utilisateur vers la mire de consentement Google OAuth
     */
    public function redirect(): RedirectResponse
    {
        $clientId = config('services.google.client_id');
        $clientSecret = config('services.google.client_secret');

        if (empty($clientId) || empty($clientSecret)) {
            // Mode informatif convivial si les clés Google ne sont pas encore configurées dans .env
            return redirect()->route('register')->with('warning', 'La connexion avec Google sera activée dès que GOOGLE_CLIENT_ID et GOOGLE_CLIENT_SECRET seront ajoutés dans le fichier .env. Utilisez l\'e-mail avec code de vérification ci-dessous pour vous inscrire immédiatement.');
        }

        try {
            return Socialite::driver('google')->redirect();
        } catch (\Throwable $e) {
            logger()->error('Google Auth Redirect error: ' . $e->getMessage());
            return redirect()->route('register')->with('error', 'Impossible de se connecter à Google pour le moment. Veuillez utiliser votre adresse e-mail.');
        }
    }

    /**
     * Reçoit le retour de Google après consentement
     */
    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            logger()->error('Google Auth Callback error: ' . $e->getMessage());
            return redirect()->route('register')->with('error', 'Authentification Google annulée ou échouée.');
        }

        $email = strtolower(trim((string) $googleUser->getEmail()));
        $googleId = (string) $googleUser->getId();
        $name = (string) ($googleUser->getName() ?: explode('@', $email)[0]);
        $avatar = $googleUser->getAvatar();

        // 1. Chercher par google_id
        $user = User::where('google_id', $googleId)->first();

        // 2. Si pas trouvé, chercher par email
        if (! $user && ! empty($email)) {
            $user = User::where('email', $email)->first();
        }

        if ($user) {
            // Mettre à jour google_id et avatar s'ils manquent
            $user->google_id = $googleId;
            if (empty($user->avatar_path) && ! empty($avatar)) {
                $user->avatar_path = $avatar;
            }
            if (empty($user->email_verified_at)) {
                $user->email_verified_at = now();
            }
            $user->save();
        } else {
            // Nouvel utilisateur
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'google_id' => $googleId,
                'avatar_path' => $avatar,
                'actor_type' => 'producer',
                'email_verified_at' => now(),
            ]);
        }

        // Connexion
        Auth::login($user, remember: true);
        if (request()->hasSession()) {
            request()->session()->regenerate();
        }

        // Étape suivante demandée par l'utilisateur :
        // Si le profil n'a pas encore son numéro ou sa localisation, on le redirige vers l'onboarding
        if (empty($user->phone) || empty($user->location)) {
            return redirect()->route('profile.complete')->with('info', 'Connexion réussie avec Google ! Renseignez maintenant votre numéro et votre localisation pour finaliser votre profil.');
        }

        if ($user->is_admin) {
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->intended(route('dashboard'));
    }
}
