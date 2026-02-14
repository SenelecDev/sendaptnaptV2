<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleOrInterimMiddleware
{
    /**
     * Handle an incoming request.
     * Permet l'accès si l'utilisateur a le rôle OU est intérimaire pour ce rôle.
     * Supporte plusieurs rôles séparés par | (pipe) - l'un d'entre eux suffit.
     *
     * Exemples:
     * - middleware('roleOrInterim:desa') - vérifie desa uniquement
     * - middleware('roleOrInterim:desa|admin') - vérifie desa OU admin
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403, 'Accès non autorisé.');
        }

        // Aplatir les rôles si séparés par |
        $allRoles = [];
        foreach ($roles as $role) {
            $allRoles = array_merge($allRoles, explode('|', $role));
        }

        // Vérifier si l'utilisateur a au moins un des rôles ou est intérimaire
        foreach ($allRoles as $role) {
            if ($user->hasRole($role) || $user->estInterimaireA($role)) {
                return $next($request);
            }
        }

        abort(403, 'Vous n\'avez pas les permissions nécessaires pour accéder à cette page.');
    }
}
