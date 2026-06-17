<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalAudit extends Model
{
    protected $fillable = [
        'user_id', 'action', 'modele', 'modele_id',
        'anciennes_valeurs', 'nouvelles_valeurs',
        'adresse_ip', 'user_agent', 'description',
    ];

    protected $casts = [
        'anciennes_valeurs' => 'array',
        'nouvelles_valeurs' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function enregistrer(string $action, string $description, ?Model $modele = null, array $anciennes = [], array $nouvelles = []): void
    {
        self::create([
            'user_id'          => auth()->id(),
            'action'           => $action,
            'modele'           => $modele ? class_basename($modele) : null,
            'modele_id'        => $modele?->id,
            'anciennes_valeurs'=> $anciennes ?: null,
            'nouvelles_valeurs'=> $nouvelles ?: null,
            'adresse_ip'       => request()->ip(),
            'user_agent'       => request()->userAgent(),
            'description'      => $description,
        ]);
    }
}
