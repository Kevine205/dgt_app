<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Dossier;
use App\Policies\DossierPolicy;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        Dossier::class => DossierPolicy::class,
    ];

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Dossier::class, DossierPolicy::class);
    }
}
