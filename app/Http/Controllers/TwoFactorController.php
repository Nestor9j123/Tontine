<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Str;

class TwoFactorController extends Controller
{
    protected $google2fa;

    public function __construct()
    {
        $this->middleware('auth');
        $this->google2fa = new Google2FA();
    }

    /**
     * Afficher la page de configuration 2FA
     */
    public function index()
    {
        $user = Auth::user();
        
        return view('auth.two-factor', [
            'user' => $user,
            'qrCodeUrl' => $user->google2fa_enabled ? null : $this->generateQRCode($user),
            'secret' => $user->google2fa_secret,
        ]);
    }

    /**
     * Activer la 2FA
     */
    public function enable(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
            'one_time_password' => 'required|string|size:6',
        ]);

        $user = Auth::user();

        // Vérifier le mot de passe
        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Mot de passe incorrect.']);
        }

        // Générer un secret si pas déjà fait
        if (!$user->google2fa_secret) {
            $user->google2fa_secret = $this->google2fa->generateSecretKey();
            $user->save();
        }

        // Vérifier le code OTP
        $valid = $this->google2fa->verifyKey($user->google2fa_secret, $request->one_time_password);

        if (!$valid) {
            return back()->withErrors(['one_time_password' => 'Code de vérification invalide.']);
        }

        // Activer la 2FA et générer des codes de récupération
        $user->google2fa_enabled = true;
        $user->google2fa_enabled_at = now();
        $user->backup_codes = $this->generateBackupCodes();
        $user->save();

        return redirect()->route('two-factor.index')->with('success', 'Authentification à deux facteurs activée avec succès !');
    }

    /**
     * Désactiver la 2FA
     */
    public function disable(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Mot de passe incorrect.']);
        }

        $user->google2fa_enabled = false;
        $user->google2fa_secret = null;
        $user->google2fa_enabled_at = null;
        $user->backup_codes = null;
        $user->save();

        return redirect()->route('two-factor.index')->with('success', 'Authentification à deux facteurs désactivée.');
    }

    /**
     * Régénérer les codes de récupération
     */
    public function regenerateBackupCodes(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Mot de passe incorrect.']);
        }

        if (!$user->google2fa_enabled) {
            return back()->withErrors(['error' => 'La 2FA doit être activée pour générer des codes de récupération.']);
        }

        $user->backup_codes = $this->generateBackupCodes();
        $user->save();

        return redirect()->route('two-factor.index')->with('success', 'Nouveaux codes de récupération générés !');
    }

    /**
     * Générer le QR Code pour la configuration
     */
    private function generateQRCode($user)
    {
        if (!$user->google2fa_secret) {
            $user->google2fa_secret = $this->google2fa->generateSecretKey();
            $user->save();
        }

        $companyName = config('app.name', 'Tontine App');
        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            $companyName,
            $user->email,
            $user->google2fa_secret
        );

        return $qrCodeUrl;
    }

    /**
     * Générer des codes de récupération
     */
    private function generateBackupCodes()
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = Str::random(10);
        }
        return $codes;
    }

    /**
     * Vérifier un code 2FA
     */
    public function verify(Request $request)
    {
        $request->validate([
            'one_time_password' => 'required|string',
        ]);

        $user = Auth::user();

        if (!$user->google2fa_enabled) {
            return response()->json(['valid' => false, 'message' => '2FA non activée']);
        }

        // Vérifier le code OTP
        $valid = $this->google2fa->verifyKey($user->google2fa_secret, $request->one_time_password);

        // Si le code OTP n'est pas valide, vérifier les codes de récupération
        if (!$valid && $user->backup_codes) {
            $backupCodes = $user->backup_codes;
            $codeIndex = array_search($request->one_time_password, $backupCodes);
            
            if ($codeIndex !== false) {
                // Supprimer le code de récupération utilisé
                unset($backupCodes[$codeIndex]);
                $user->backup_codes = array_values($backupCodes);
                $user->save();
                $valid = true;
            }
        }

        return response()->json([
            'valid' => $valid,
            'message' => $valid ? 'Code valide' : 'Code invalide'
        ]);
    }

    /**
     * Afficher la page de challenge 2FA
     */
    public function challenge()
    {
        $user = Auth::user();

        if (!$user->google2fa_enabled) {
            return redirect()->route('dashboard');
        }

        return view('auth.two-factor-challenge', compact('user'));
    }

    /**
     * Traiter le challenge 2FA
     */
    public function processChallenge(Request $request)
    {
        $request->validate([
            'one_time_password' => 'required|string',
        ]);

        $user = Auth::user();

        if (!$user->google2fa_enabled) {
            return redirect()->route('dashboard');
        }

        // Vérifier le code OTP
        $valid = $this->google2fa->verifyKey($user->google2fa_secret, $request->one_time_password);

        // Si le code OTP n'est pas valide, vérifier les codes de récupération
        if (!$valid && $user->backup_codes) {
            $backupCodes = $user->backup_codes;
            $codeIndex = array_search($request->one_time_password, $backupCodes);
            
            if ($codeIndex !== false) {
                // Supprimer le code de récupération utilisé
                unset($backupCodes[$codeIndex]);
                $user->backup_codes = array_values($backupCodes);
                $user->save();
                $valid = true;
            }
        }

        if (!$valid) {
            return back()->withErrors(['one_time_password' => 'Code de vérification invalide.']);
        }

        // Marquer la 2FA comme vérifiée pour cette session
        session(['2fa_verified_' . $user->id => true]);

        return redirect()->intended(route('dashboard'));
    }
}
