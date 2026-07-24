<?php

namespace App\Services\Assistant;

use App\Models\Demande;
use App\Models\Note;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class AssistantToolService
{
    private int $maxRows;

    public function __construct(?int $maxRows = null)
    {
        $this->maxRows = $maxRows ?? (int) config('assistant.max_tool_rows', 15);
    }

    /**
     * @param  array<string, mixed>  $args
     * @return array{ok: bool, message?: string, data?: mixed}
     */
    public function execute(string $name, array $args, User $user, KnowledgeBaseService $knowledge): array
    {
        try {
            return match ($name) {
                'get_my_demandes' => $this->getMyDemandes($user, $args),
                'get_my_notes' => $this->getMyNotes($user, $args),
                'get_pending_queue' => $this->getPendingQueue($user, $args),
                'get_napt_stats' => $this->getNaptStats($user),
                'search_help' => [
                    'ok' => true,
                    'data' => ['excerpt' => $knowledge->search((string) ($args['query'] ?? ''))],
                ],
                default => ['ok' => false, 'message' => "Outil inconnu : {$name}"],
            };
        } catch (\Throwable $e) {
            Log::warning('Assistant tool error', ['tool' => $name, 'error' => $e->getMessage()]);

            return ['ok' => false, 'message' => 'Erreur lors de la récupération des données.'];
        }
    }

    /**
     * @param  array<string, mixed>  $args
     * @return array{ok: bool, data?: mixed, message?: string}
     */
    private function getMyDemandes(User $user, array $args): array
    {
        $limit = $this->limit($args);
        $query = Demande::query()->orderByDesc('updated_at');

        if ($user->hasAnyRole(['admin', 'desa', 'directeur'])) {
            // Périmètre large pour supervision / traitement
        } elseif ($user->groupe_id) {
            $query->where(function ($q) use ($user) {
                $q->where('demandeur_id', $user->id)
                    ->orWhereHas('demandeur', fn ($u) => $u->where('groupe_id', $user->groupe_id));
            });
        } else {
            $query->where('demandeur_id', $user->id);
        }

        $rows = $query->limit($limit)
            ->get(['id', 'numero_demande', 'designation', 'statut', 'ddp', 'dfp', 'updated_at'])
            ->map(fn (Demande $d) => [
                'id' => $d->id,
                'numero' => $d->numero_demande,
                'designation' => mb_substr((string) $d->designation, 0, 80),
                'statut' => $d->statut,
                'debut_prevu' => optional($d->ddp)?->format('d/m/Y'),
                'fin_prevue' => optional($d->dfp)?->format('d/m/Y'),
            ])
            ->all();

        return ['ok' => true, 'data' => ['count' => count($rows), 'demandes' => $rows]];
    }

    /**
     * @param  array<string, mixed>  $args
     * @return array{ok: bool, data?: mixed, message?: string}
     */
    private function getMyNotes(User $user, array $args): array
    {
        $limit = $this->limit($args);
        $query = Note::query()
            ->with(['demande:id,numero_demande,demandeur_id'])
            ->orderByDesc('updated_at');

        if ($user->hasAnyRole(['admin', 'desa', 'directeur'])) {
            // Vue large
        } elseif ($user->hasRole('verificateur')) {
            $query->where(function ($q) use ($user) {
                $q->where('statut', Note::STATUT_EN_ATTENTE_VERIFICATION)
                    ->orWhere('verifie_id', $user->id);
            });
        } elseif ($user->hasRole('valideur')) {
            $query->where(function ($q) use ($user) {
                $q->whereIn('statut', [Note::STATUT_VERIFIEE, Note::STATUT_EN_ATTENTE_VALIDATION])
                    ->orWhere('valide_id', $user->id);
            });
        } elseif ($user->hasAnyRole(['operateur', 'operateurchef'])) {
            $query->whereIn('statut', [
                Note::STATUT_VALIDEE,
                Note::STATUT_EN_COURS_EXECUTION,
                Note::STATUT_EXECUTEE,
            ]);
        } else {
            // Demandeur : NAPT liées à ses DAPT / groupe
            $query->whereHas('demande', function ($q) use ($user) {
                if ($user->groupe_id) {
                    $q->where(function ($inner) use ($user) {
                        $inner->where('demandeur_id', $user->id)
                            ->orWhereHas('demandeur', fn ($u) => $u->where('groupe_id', $user->groupe_id));
                    });
                } else {
                    $q->where('demandeur_id', $user->id);
                }
            });
        }

        $rows = $query->limit($limit)
            ->get(['id', 'numero_note', 'statut', 'demande_id', 'numero_semaine', 'fiche_manoeuvre', 'updated_at'])
            ->map(fn (Note $n) => [
                'id' => $n->id,
                'numero' => $n->numero_note,
                'dapt' => $n->demande?->numero_demande,
                'statut' => $n->statut,
                'semaine' => $n->numero_semaine,
                'fiche_manoeuvre' => filled($n->fiche_manoeuvre),
            ])
            ->all();

        return ['ok' => true, 'data' => ['count' => count($rows), 'notes' => $rows]];
    }

    /**
     * File d'attente selon le rôle (ce qui attend une action de l'utilisateur).
     *
     * @param  array<string, mixed>  $args
     * @return array{ok: bool, data?: mixed, message?: string}
     */
    private function getPendingQueue(User $user, array $args): array
    {
        $limit = $this->limit($args);
        $items = [];

        if ($user->hasAnyRole(['desa', 'admin'])) {
            $demandes = Demande::query()
                ->whereIn('statut', [Demande::STATUT_CREEE, Demande::STATUT_EN_COURS])
                ->orderByDesc('updated_at')
                ->limit($limit)
                ->get(['numero_demande', 'statut', 'designation']);
            foreach ($demandes as $d) {
                $items[] = [
                    'type' => 'DAPT',
                    'numero' => $d->numero_demande,
                    'statut' => $d->statut,
                    'libelle' => mb_substr((string) $d->designation, 0, 60),
                ];
            }

            $notes = Note::query()
                ->whereIn('statut', [Note::STATUT_BROUILLON, Note::STATUT_EN_ETUDE, Note::STATUT_RETOURNEE])
                ->orderByDesc('updated_at')
                ->limit($limit)
                ->get(['numero_note', 'statut']);
            foreach ($notes as $n) {
                $items[] = [
                    'type' => 'NAPT',
                    'numero' => $n->numero_note,
                    'statut' => $n->statut,
                    'libelle' => 'À traiter / corriger',
                ];
            }
        }

        if ($user->hasRole('verificateur') || $user->hasRole('admin')) {
            $notes = Note::query()
                ->where('statut', Note::STATUT_EN_ATTENTE_VERIFICATION)
                ->orderByDesc('updated_at')
                ->limit($limit)
                ->get(['numero_note', 'statut']);
            foreach ($notes as $n) {
                $items[] = [
                    'type' => 'NAPT',
                    'numero' => $n->numero_note,
                    'statut' => $n->statut,
                    'libelle' => 'En attente de vérification',
                ];
            }
        }

        if ($user->hasRole('valideur') || $user->hasRole('admin')) {
            $notes = Note::query()
                ->whereIn('statut', [Note::STATUT_VERIFIEE, Note::STATUT_EN_ATTENTE_VALIDATION])
                ->orderByDesc('updated_at')
                ->limit($limit)
                ->get(['numero_note', 'statut']);
            foreach ($notes as $n) {
                $items[] = [
                    'type' => 'NAPT',
                    'numero' => $n->numero_note,
                    'statut' => $n->statut,
                    'libelle' => 'En attente de validation',
                ];
            }
        }

        if ($user->hasRole('operateurchef') || $user->hasRole('admin')) {
            $notes = Note::query()
                ->where('statut', Note::STATUT_VALIDEE)
                ->where(function ($q) {
                    $q->whereNull('fiche_manoeuvre')->orWhere('fiche_manoeuvre', '');
                })
                ->orderByDesc('updated_at')
                ->limit($limit)
                ->get(['numero_note', 'statut']);
            foreach ($notes as $n) {
                $items[] = [
                    'type' => 'NAPT',
                    'numero' => $n->numero_note,
                    'statut' => $n->statut,
                    'libelle' => 'Fiche manœuvre manquante',
                ];
            }
        }

        if ($user->hasRole('operateur') || $user->hasRole('admin')) {
            $notes = Note::query()
                ->whereIn('statut', [Note::STATUT_VALIDEE, Note::STATUT_EN_COURS_EXECUTION])
                ->whereNotNull('fiche_manoeuvre')
                ->where('fiche_manoeuvre', '!=', '')
                ->orderByDesc('updated_at')
                ->limit($limit)
                ->get(['numero_note', 'statut']);
            foreach ($notes as $n) {
                $items[] = [
                    'type' => 'NAPT',
                    'numero' => $n->numero_note,
                    'statut' => $n->statut,
                    'libelle' => 'À exécuter',
                ];
            }
        }

        if ($user->hasRole('demandeur') && ! $user->hasAnyRole(['desa', 'admin'])) {
            $demandes = Demande::query()
                ->where('demandeur_id', $user->id)
                ->where('statut', Demande::STATUT_RETOURNEE)
                ->orderByDesc('updated_at')
                ->limit($limit)
                ->get(['numero_demande', 'statut', 'motif_retour']);
            foreach ($demandes as $d) {
                $items[] = [
                    'type' => 'DAPT',
                    'numero' => $d->numero_demande,
                    'statut' => $d->statut,
                    'libelle' => 'À corriger : '.mb_substr((string) $d->motif_retour, 0, 50),
                ];
            }
        }

        $items = array_slice($items, 0, $limit);

        return ['ok' => true, 'data' => ['count' => count($items), 'file' => $items]];
    }

    /**
     * @return array{ok: bool, data?: mixed, message?: string}
     */
    private function getNaptStats(User $user): array
    {
        if (! $user->hasAnyRole(['admin', 'desa', 'directeur', 'verificateur', 'valideur', 'operateurchef', 'operateur'])) {
            return ['ok' => false, 'message' => 'Statistiques NAPT réservées aux rôles de traitement.'];
        }

        $counts = Note::query()
            ->selectRaw('statut, COUNT(*) as total')
            ->groupBy('statut')
            ->pluck('total', 'statut')
            ->all();

        return [
            'ok' => true,
            'data' => [
                'par_statut' => $counts,
                'total' => array_sum(array_map('intval', $counts)),
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $args
     */
    private function limit(array $args): int
    {
        $limit = (int) ($args['limit'] ?? 10);

        return max(1, min($limit, $this->maxRows));
    }
}
