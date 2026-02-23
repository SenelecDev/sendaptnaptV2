<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();

                if ($user->hasRole('admin')) {
                    return redirect()->route('admin.dashboard');
                }
                if ($user->hasRole('desa')) {
                    return redirect()->route('desa.dashboard');
                }
                if ($user->hasRole('verificateur')) {
                    return redirect()->route('verificateur.dashboard');
                }
                if ($user->hasRole('valideur')) {
                    return redirect()->route('valideur.dashboard');
                }
                if ($user->hasRole('operateur') || $user->hasRole('operateurchef')) {
                    return redirect()->route('operateur.notes.index');
                }
                if ($user->hasRole('directeur')) {
                    return redirect()->route('directeur.dashboard');
                }
                if ($user->hasRole('demandeur')) {
                    return redirect()->route('demandeur.dashboard');
                }

                return redirect()->route('dashboard');
            }
        }

        return $next($request);
    }
}
