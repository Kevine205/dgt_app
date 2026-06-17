<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UserActif
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && !auth()->user()->actif) {
            auth()->logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Votre compte a été suspendu. Contactez l\'administrateur.']);
        }
        return $next($request);
    }
}
