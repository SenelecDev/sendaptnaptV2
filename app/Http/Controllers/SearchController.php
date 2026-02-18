<?php

namespace App\Http\Controllers;

use App\Models\Demande;
use App\Models\Note;
use App\Models\User;
use App\Traits\SearchableTrait;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    use SearchableTrait;
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
            $demandesQuery = Demande::with(['demandeur', 'chargeTravaux']);
            $this->applySimpleSearch($demandesQuery, $query,
                ['numero_demande', 'designation', 'lieu_execution', 'destinataire'],
                ['demandeur' => ['name', 'matricule']]);

            // Filtrer selon le rôle
            if ($user->hasRole('demandeur') && !$user->hasRole(['admin', 'desa'])) {
                $demandesQuery->where('demandeur_id', $user->id);
            }

            $results['demandes'] = $demandesQuery->latest()->limit(10)->get();

            // Recherche NAPT
            $notesQuery = Note::with(['demande.demandeur', 'etabliPar']);
            $this->applySimpleSearch($notesQuery, $query,
                ['numero_note', 'numero_semaine', 'renseignementN'],
                ['demande' => ['numero_demande', 'designation']]);

            $results['notes'] = $notesQuery->latest()->limit(10)->get();

            // Recherche Utilisateurs (admin seulement)
            if ($user->hasRole('admin')) {
                $usersQuery = User::query();
                $this->applySimpleSearch($usersQuery, $query,
                    ['name', 'matricule', 'email', 'nom', 'prenom'], []);
                $results['users'] = $usersQuery->limit(10)->get();
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
            $demandesQuery = Demande::query();
            $this->applySimpleSearch($demandesQuery, $query, ['numero_demande', 'designation'], []);
            $demandes = $demandesQuery->limit(5)->get(['id', 'numero_demande', 'designation']);

            foreach ($demandes as $d) {
                $suggestions[] = [
                    'type' => 'dapt',
                    'label' => $d->numero_demande,
                    'description' => \Illuminate\Support\Str::limit($d->designation, 50),
                    'url' => route('demandeur.demandes.show', $d->id),
                ];
            }

            // NAPT
            $notesQuery = Note::query();
            $this->applySimpleSearch($notesQuery, $query, ['numero_note'], []);
            $notes = $notesQuery->limit(5)->get(['id', 'numero_note', 'numero_semaine']);

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
