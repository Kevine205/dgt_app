<?php
// FichierController.php
namespace App\Http\Controllers;

use App\Models\PieceJustificative;
use Illuminate\Support\Facades\Storage;

class FichierController extends Controller
{
    public function telechargerPiece(PieceJustificative $piece)
    {
        // Vérifier droits
        $user = auth()->user();
        if (!$user->estPersonnelDGT() && $piece->dossier->user_id !== $user->id) {
            abort(403);
        }
        return Storage::disk('private')->response($piece->nom_stockage, $piece->nom_original);
    }
}
