<?php

namespace App\Http\Controllers\Validateur;

use App\Http\Controllers\Controller;
use App\Models\JournalAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class ProfilValidateurController extends Controller
{
    public function index()
    {
        return view('validateur.profil.index', ['user' => auth()->user()]);
    }

    // Modifier nom / prénom / email
    public function updateIdentifiants(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'nom'       => 'required|string|max:100',
            'prenom'    => 'required|string|max:100',
            'email'     => 'required|email|unique:users,email,' . $user->id,
            'telephone' => 'nullable|string|max:20',
        ], [
            'email.unique' => 'Cette adresse e-mail est déjà utilisée par un autre compte.',
        ]);

        $user->update([
            'nom'       => strtoupper($request->nom),
            'prenom'    => ucfirst($request->prenom),
            'email'     => $request->email,
            'telephone' => $request->telephone,
        ]);

        JournalAudit::enregistrer('PROFIL_MODIFIE', "Identifiants mis à jour : {$user->nom_complet}");

        return back()->with('success', 'Vos identifiants ont été mis à jour avec succès.');
    }

    // Modifier le mot de passe
    public function updateMotDePasse(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'mot_de_passe_actuel'  => 'required',
            'password'             => ['required', 'confirmed', Rules\Password::min(8)->letters()->numbers()],
        ], [
            'mot_de_passe_actuel.required' => 'Le mot de passe actuel est obligatoire.',
            'password.min'                 => 'Le nouveau mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed'           => 'Les mots de passe ne correspondent pas.',
        ]);

        if (!Hash::check($request->mot_de_passe_actuel, $user->password)) {
            return back()->withErrors(['mot_de_passe_actuel' => 'Le mot de passe actuel est incorrect.']);
        }

        $user->update(['password' => Hash::make($request->password)]);
        JournalAudit::enregistrer('MOT_DE_PASSE_MODIFIE', "Mot de passe modifié : {$user->nom_complet}");

        return back()->with('success_mdp', 'Mot de passe modifié avec succès.');
    }

    // Enregistrer la signature électronique
    public function updateSignature(Request $request)
    {
        $request->validate([
            'signature' => 'required|string',
        ]);

        // Vérifier que c'est bien une image base64 valide
        if (!str_starts_with($request->signature, 'data:image/')) {
            return back()->withErrors(['signature' => 'Format de signature invalide.']);
        }

        auth()->user()->update(['signature_electronique' => $request->signature]);
        JournalAudit::enregistrer('SIGNATURE_ENREGISTREE', 'Signature électronique enregistrée');

        return back()->with('success_sig', 'Signature enregistrée avec succès.');
    }

    // Supprimer la signature
    public function supprimerSignature()
    {
        auth()->user()->update(['signature_electronique' => null]);
        JournalAudit::enregistrer('SIGNATURE_SUPPRIMEE', 'Signature électronique supprimée');

        return back()->with('success_sig', 'Signature supprimée.');
    }
}
