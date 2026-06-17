<?php

namespace App\Http\Controllers\Usager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TableauBordUsagerController extends Controller
{
    public function index()
    {
        return view('usager.dashboard');
    }

    public function profil()
    {
        return view('usager.profil', ['user' => auth()->user()]);
    }

    public function updateProfil(Request $request)
    {
        $request->validate([
            'nom'       => 'required|string|max:100',
            'prenom'    => 'required|string|max:100',
            'telephone' => 'nullable|string|max:20',
        ]);

        auth()->user()->update([
            'nom'       => strtoupper($request->nom),
            'prenom'    => ucfirst($request->prenom),
            'telephone' => $request->telephone,
        ]);

        return back()->with('success', 'Profil mis à jour avec succès.');
    }
}
