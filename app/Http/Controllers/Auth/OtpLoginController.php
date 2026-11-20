<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LoginCode;
use App\Models\User;
use App\Notifications\SendLoginOtpNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OtpLoginController extends Controller
{
    /**
     * Envoie le code OTP à l'adresse e-mail
     */
    public function sendOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
        ]);

        $email = strtolower(trim($request->email));
        $throttleKey = 'otp-send:' . $request->ip() . '|' . $email;

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'email' => "Trop de tentatives. Veuillez patienter {$seconds} secondes avant de demander un nouveau code.",
            ]);
        }

        RateLimiter::hit($throttleKey, 60);

        // Générer un code à 6 chiffres
        $code = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        // Supprimer les anciens codes pour cet e-mail
        LoginCode::where('email', $email)->delete();

        // Enregistrer le nouveau code (valable 15 minutes)
        LoginCode::create([
            'email' => $email,
            'code' => $code,
            'expires_at' => now()->addMinutes(15),
        ]);

        // Envoyer la notification e-mail
        try {
            Notification::route('mail', $email)->notify(new SendLoginOtpNotification($code));
        } catch (\Throwable $e) {
            // En cas d'erreur de transport mail, on logue et on continue si local
            logger()->error('Erreur envoi OTP mail: ' . $e->getMessage());
        }

        // Mettre en session l'email pour l'étape de vérification
        session(['otp_email' => $email]);

        // En local, on fournit aussi le code pour faciliter les tests
        $statusMsg = 'Un code de vérification à 6 chiffres a été envoyé à ' . $email . '.';
        if (app()->isLocal()) {
            session()->flash('demo_code', $code);
        }

        return redirect()->route('otp.verify')->with('status', $statusMsg);
    }

    /**
     * Affiche l'écran de saisie du code OTP
     */
    public function showVerifyForm(Request $request): View|RedirectResponse
    {
        $email = session('otp_email');

        if (! $email) {
            return redirect()->route('register')->with('warning', 'Veuillez d\'abord saisir votre adresse e-mail.');
        }

        return view('auth.verify-code', [
            'email' => $email,
            'demoCode' => session('demo_code'),
        ]);
    }

    /**
     * Valide le code OTP et connecte l'utilisateur
     */
    public function verifyOtp(Request $request): RedirectResponse
    {
        $email = session('otp_email');

        if (! $email) {
            return redirect()->route('register')->with('warning', 'Session expirée. Veuillez recommencer.');
        }

        $request->validate([
            'code' => ['required', 'string', 'min:4', 'max:10'],
        ]);

        $code = preg_replace('/\s+/', '', (string) $request->code);

        // Recherche du code valide
        $validCode = LoginCode::where('email', $email)
            ->where('code', $code)
            ->where('expires_at', '>', now())
            ->first();

        if (! $validCode) {
            throw ValidationException::withMessages([
                'code' => 'Ce code est incorrect ou a expiré. Veuillez vérifier ou en demander un nouveau.',
            ]);
        }

        // Code valide : on le consomme
        $validCode->delete();

        // Récupérer ou créer l'utilisateur
        $user = User::where('email', $email)->first();

        if (! $user) {
            $defaultName = explode('@', $email)[0];
            $user = User::create([
                'name' => ucfirst($defaultName),
                'email' => $email,
                'actor_type' => 'producer',
                'email_verified_at' => now(),
            ]);
        } else {
            if (! $user->email_verified_at) {
                $user->email_verified_at = now();
                $user->save();
            }
        }

        // Connexion
        Auth::login($user, remember: true);
        if ($request->hasSession()) {
            $request->session()->regenerate();
        }
        session()->forget('otp_email');

        // Étape suivante demandée par l'utilisateur :
        // Si le profil n'a pas encore son numéro ou sa localisation, on le redirige vers l'onboarding
        if (empty($user->phone) || empty($user->location)) {
            return redirect()->route('profile.complete')->with('info', 'Bienvenue ! Finalisez vos coordonnées pour être contacté par les acheteurs.');
        }

        if ($user->is_admin) {
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Renvoie un code OTP
     */
    public function resendOtp(Request $request): RedirectResponse
    {
        $email = session('otp_email');

        if (! $email) {
            return redirect()->route('register');
        }

        $request->merge(['email' => $email]);
        return $this->sendOtp($request);
    }
}
