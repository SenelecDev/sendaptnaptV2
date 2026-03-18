<?php

namespace App\Console\Commands;

use App\Models\Note;
use App\Models\NoteHistory;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RestoreNaptAcceptedDates extends Command
{
    protected $signature = 'napt:restore-accepted-dates
        {--id=* : IDs des notes à corriger}
        {--all-executed : Corriger toutes les notes exécutées/en cours}
        {--only-if-real-null : Ne corriger que si les champs *_reel sont vides}
        {--dry-run : Afficher sans modifier}';

    protected $description = 'Restaure les dates acceptées NAPT (dre/ddt/dft/drex) et stocke les dates réelles dans *_reel.';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $onlyIfRealNull = (bool) $this->option('only-if-real-null');
        $ids = (array) $this->option('id');
        $allExecuted = (bool) $this->option('all-executed');

        if (!$allExecuted && empty($ids)) {
            $this->error('Utilise --all-executed ou --id=...');
            return self::FAILURE;
        }

        $query = Note::query()->with('demande');

        if (!empty($ids)) {
            $query->whereIn('id', $ids);
        } else {
            $query->whereIn('statut', [Note::STATUT_EN_COURS_EXECUTION, Note::STATUT_EXECUTEE]);
        }

        if ($onlyIfRealNull) {
            $query->where(function ($q) {
                $q->whereNull('dre_reel')
                    ->orWhereNull('ddt_reel')
                    ->orWhereNull('dft_reel')
                    ->orWhereNull('drex_reel');
            });
        }

        $notes = $query->get();
        if ($notes->isEmpty()) {
            $this->info('Aucune note à corriger.');
            return self::SUCCESS;
        }

        $fixed = 0;
        foreach ($notes as $note) {
            $changes = [];

            // 1) Conserver les valeurs actuelles comme "réelles" si pas déjà stockées
            if (!$note->dre_reel && $note->dre) {
                $changes['dre_reel'] = $note->dre;
            }
            if (!$note->ddt_reel && $note->ddt) {
                $changes['ddt_reel'] = $note->ddt;
            }
            if (!$note->dft_reel && $note->dft) {
                $changes['dft_reel'] = $note->dft;
            }
            if (!$note->drex_reel && $note->drex) {
                $changes['drex_reel'] = $note->drex;
            }

            // 2) Restaurer les dates acceptées (ddt/dft) depuis la DAPT si disponibles
            $demande = $note->demande;
            if ($demande && $demande->dda && $demande->dfa) {
                $hda = $demande->hda ?: '00:00';
                $hfa = $demande->hfa ?: '00:00';

                try {
                    $acceptedDdt = Carbon::parse($demande->dda->format('Y-m-d') . ' ' . $hda);
                    $acceptedDft = Carbon::parse($demande->dfa->format('Y-m-d') . ' ' . $hfa);
                    $changes['ddt'] = $acceptedDdt;
                    $changes['dft'] = $acceptedDft;
                } catch (\Throwable $e) {
                    // ignore parsing issues
                }
            }

            // 3) Restaurer dre/drex acceptées depuis l'historique si possible
            $dreOld = NoteHistory::where('note_id', $note->id)->where('field', 'dre')->orderByDesc('created_at')->value('old_value');
            if ($dreOld) {
                $changes['dre'] = $dreOld;
            }
            $drexOld = NoteHistory::where('note_id', $note->id)->where('field', 'drex')->orderByDesc('created_at')->value('old_value');
            if ($drexOld) {
                $changes['drex'] = $drexOld;
            }

            if (empty($changes)) {
                continue;
            }

            $fixed++;
            if ($dryRun) {
                $this->line("Note #{$note->id} {$note->numero_note}: " . implode(', ', array_keys($changes)));
                continue;
            }

            $note->fill($changes);
            $note->save();
        }

        $this->info($dryRun ? "Dry-run: {$fixed} note(s) ciblée(s)." : "{$fixed} note(s) corrigée(s).");
        return self::SUCCESS;
    }
}

