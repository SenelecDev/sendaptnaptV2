<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ImpersonateController extends Controller
{
    /**
     * Démarrer l'impersonation d'un utilisateur
     */
    public function start(User $user)
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Seul le super administrateur peut simuler un utilisateur.');
        }

        // Sauvegarder l'ID de l'admin original
        Session::put('impersonate_admin_id', Auth::id());
        Session::put('impersonating', true);

        // Se connecter en tant que l'utilisateur cible
        Auth::login($user);

        return redirect()->route('dashboard')->with('success', "Vous êtes maintenant connecté en tant que {$user->full_name}");
    }

    /**
     * Arrêter l'impersonation et revenir à l'admin
     */
    public function stop()
    {
        if (!Session::has('impersonate_admin_id')) {
            return redirect()->route('dashboard');
        }

        // Récupérer l'admin original
        $adminId = Session::get('impersonate_admin_id');
        $admin = User::find($adminId);

        // Nettoyer la session
        Session::forget('impersonate_admin_id');
        Session::forget('impersonating');

        // Reconnecter l'admin
        if ($admin) {
            Auth::login($admin);
            return redirect()->route('admin.users.index')->with('success', 'Vous êtes de retour en tant qu\'administrateur.');
        }

        return redirect()->route('login');
    }

    /**
     * Vérifier si on est en mode impersonation
     */
    public static function isImpersonating(): bool
    {
        return Session::has('impersonating') && Session::get('impersonating') === true;
    }

    /**
     * Récupérer l'ID de l'admin qui impersonate
     */
    public static function getAdminId(): ?int
    {
        return Session::get('impersonate_admin_id');
    }
}
