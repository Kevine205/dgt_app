<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'nom', 'prenom', 'email', 'telephone', 'password',
        'actif', 'google2fa_secret', 'google2fa_enabled',
    ];

    protected $hidden = [
        'password', 'remember_token', 'google2fa_secret',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'actif' => 'boolean',
            'google2fa_enabled' => 'boolean',
        ];
    }

    public function getNomCompletAttribute(): string
    {
        return "{$this->prenom} {$this->nom}";
    }

    public function dossiers()
    {
        return $this->hasMany(Dossier::class);
    }

    public function dossiersInstruit()
    {
        return $this->hasMany(Dossier::class, 'agent_id');
    }

    public function dossiersValides()
    {
        return $this->hasMany(Dossier::class, 'validateur_id');
    }

    public function audits()
    {
        return $this->hasMany(JournalAudit::class);
    }

    public function estUsager(): bool
    {
        return $this->hasRole('usager');
    }

    public function estAgent(): bool
    {
        return $this->hasRole('agent');
    }

    public function estValidateur(): bool
    {
        return $this->hasRole('validateur');
    }

    public function estAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function estPersonnelDGT(): bool
    {
        return $this->hasAnyRole(['agent', 'validateur', 'admin']);
    }
}
