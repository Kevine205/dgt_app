<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TwoFactorAuth
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user) return $next($request);

        // Si personnel DGT avec 2FA activé mais pas encore vérifié cette session
        if ($user->estPersonnelDGT() && $user->google2fa_enabled && !session('2fa_verified')) {
            session(['2fa_required' => true]);
            return redirect()->route('2fa.show');
        }

        // Si personnel DGT sans 2FA configuré → forcer la configuration
        if ($user->estPersonnelDGT() && !$user->google2fa_enabled) {
            if (!$request->routeIs('2fa.*') && !$request->routeIs('logout')) {
                return redirect()->route('2fa.setup')
                    ->with('warning', 'Vous devez configurer l\'authentification à deux facteurs avant de continuer.');
            }
        }

        return $next($request);
    }
}
