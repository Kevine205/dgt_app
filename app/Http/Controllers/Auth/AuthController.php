<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\JournalAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ], [
            'email.required'    => 'L\'adresse e-mail est obligatoire.',
            'password.required' => 'Le mot de passe est obligatoire.',
        ]);

        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember');

        if (!Auth::attempt($credentials, $remember)) {
            return back()->withErrors(['email' => 'Identifiants incorrects.'])->withInput();
        }

        $user = Auth::user();

        if (!$user->actif) {
            Auth::logout();
            return back()->withErrors(['email' => 'Votre compte a été suspendu. Contactez l\'administrateur.']);
        }

        // Si personnel DGT avec 2FA activé → vérifier OTP
        if ($user->estPersonnelDGT() && $user->google2fa_enabled) {
            session(['2fa_required' => true]);
            return redirect()->route('2fa.show');
        }

        JournalAudit::enregistrer('CONNEXION', "Connexion de {$user->nom_complet}");
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nom'      => 'required|string|max:100',
            'prenom'   => 'required|string|max:100',
            'email'    => 'required|email|unique:users',
            'telephone'=> 'nullable|string|max:20',
            'password' => ['required', 'confirmed', Rules\Password::min(8)->letters()->numbers()],
        ], [
            'nom.required'      => 'Le nom est obligatoire.',
            'prenom.required'   => 'Le prénom est obligatoire.',
            'email.required'    => 'L\'adresse e-mail est obligatoire.',
            'email.unique'      => 'Cette adresse e-mail est déjà utilisée.',
            'password.min'      => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed'=> 'Les mots de passe ne correspondent pas.',
        ]);

        $user = User::create([
            'nom'       => strtoupper($request->nom),
            'prenom'    => ucfirst($request->prenom),
            'email'     => $request->email,
            'telephone' => $request->telephone,
            'password'  => Hash::make($request->password),
        ]);

        $user->assignRole('usager');

        Auth::login($user);
        JournalAudit::enregistrer('INSCRIPTION', "Nouveau compte usager créé : {$user->nom_complet}");

        return redirect()->route('usager.dashboard')->with('success', 'Bienvenue ! Votre compte a été créé avec succès.');
    }

    public function logout(Request $request)
    {
        JournalAudit::enregistrer('DECONNEXION', 'Déconnexion de l\'utilisateur');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home')->with('success', 'Vous avez été déconnecté avec succès.');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $status = Password::sendResetLink($request->only('email'));
        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetForm(Request $request, string $token)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => ['required', 'confirmed', Rules\Password::min(8)->letters()->numbers()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', 'Mot de passe réinitialisé avec succès.')
            : back()->withErrors(['email' => __($status)]);
    }
}
