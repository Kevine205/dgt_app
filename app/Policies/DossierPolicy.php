<?php

namespace App\Policies;

use App\Models\Dossier;
use App\Models\User;

class DossierPolicy
{
    public function view(User $user, Dossier $dossier): bool
    {
        // L'usager ne peut voir que ses propres dossiers
        if ($user->hasRole('usager')) {
            return $user->id === $dossier->user_id;
        }
        // Le personnel DGT peut tout voir
        return $user->estPersonnelDGT();
    }

    public function update(User $user, Dossier $dossier): bool
    {
        return $user->hasAnyRole(['agent', 'validateur', 'admin']);
    }
}
