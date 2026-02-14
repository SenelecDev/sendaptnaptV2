<?php

namespace App\Http\Controllers;

use App\Models\Demande;
use App\Models\Note;
use App\Models\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->get('q', '');
        $results = [
            'demandes' => collect(),
            'notes' => collect(),
            'users' => collect(),
        ];

        if (strlen($query) >= 2) {
            $user = auth()->user();

            // Recherche DAPT
            $demandesQuery = Demande::with(['demandeur', 'chargeTravaux'])
                ->where(function ($q) use ($query) {
                    $q->where('numero_demande', 'like', "%{$query}%")
                      ->orWhere('designation', 'like', "%{$query}%")
                      ->orWhere('lieu_execution', 'like', "%{$query}%")
                      ->orWhere('destinataire', 'like', "%{$query}%")
                      ->orWhereHas('demandeur', function ($q2) use ($query) {
                          $q2->where('name', 'like', "%{$query}%")
                             ->orWhere('matricule', 'like', "%{$query}%");
                      });
                });

            // Filtrer selon le rôle
            if ($user->hasRole('demandeur') && !$user->hasRole(['admin', 'desa'])) {
                $demandesQuery->where('demandeur_id', $user->id);
            }

            $results['demandes'] = $demandesQuery->latest()->limit(10)->get();

            // Recherche NAPT
            $notesQuery = Note::with(['demande.demandeur', 'etabliPar'])
                ->where(function ($q) use ($query) {
                    $q->where('numero_note', 'like', "%{$query}%")
                      ->orWhere('numero_semaine', 'like', "%{$query}%")
                      ->orWhere('renseignementN', 'like', "%{$query}%")
                      ->orWhereHas('demande', function ($q2) use ($query) {
                          $q2->where('numero_demande', 'like', "%{$query}%")
                             ->orWhere('designation', 'like', "%{$query}%");
                      });
                });

            $results['notes'] = $notesQuery->latest()->limit(10)->get();

            // Recherche Utilisateurs (admin seulement)
            if ($user->hasRole('admin')) {
                $results['users'] = User::where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('matricule', 'like', "%{$query}%")
                      ->orWhere('email', 'like', "%{$query}%")
                      ->orWhere('nom', 'like', "%{$query}%")
                      ->orWhere('prenom', 'like', "%{$query}%");
                })->limit(10)->get();
            }
        }

        $totalResults = $results['demandes']->count() + $results['notes']->count() + $results['users']->count();

        return view('search.index', compact('query', 'results', 'totalResults'));
    }

    /**
     * Recherche AJAX pour suggestions rapides
     */
    public function suggestions(Request $request)
    {
        $query = $request->get('q', '');
        $suggestions = [];

        if (strlen($query) >= 2) {
            // DAPT
            $demandes = Demande::where('numero_demande', 'like', "%{$query}%")
                ->orWhere('designation', 'like', "%{$query}%")
                ->limit(5)
                ->get(['id', 'numero_demande', 'designation']);

            foreach ($demandes as $d) {
                $suggestions[] = [
                    'type' => 'dapt',
                    'label' => $d->numero_demande,
                    'description' => \Illuminate\Support\Str::limit($d->designation, 50),
                    'url' => route('demandeur.demandes.show', $d->id),
                ];
            }

            // NAPT
            $notes = Note::where('numero_note', 'like', "%{$query}%")
                ->limit(5)
                ->get(['id', 'numero_note', 'numero_semaine']);

            foreach ($notes as $n) {
                $suggestions[] = [
                    'type' => 'napt',
                    'label' => $n->numero_note,
                    'description' => "Semaine {$n->numero_semaine}",
                    'url' => route('desa.notes.show', $n->id),
                ];
            }
        }

        return response()->json($suggestions);
    }
}
