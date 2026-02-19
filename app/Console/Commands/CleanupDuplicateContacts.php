<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupDuplicateContacts extends Command
{
    protected $signature = 'cleanup:duplicate-contacts {--dry-run : Afficher sans supprimer}';
    protected $description = 'Supprime les doublons dans charges_cons, correspondants et services_dest en mettant à jour les tables pivot';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('Mode dry-run : aucune modification ne sera effectuée.');
        }

        $this->dedup('correspondants', 'note_correspondant', 'correspondant_id', $dryRun);
        $this->dedup('charges_cons', 'note_charge_consignation', 'charge_cons_id', $dryRun);
        $this->dedup('services_dest', 'note_service', 'service_dest_id', $dryRun);

        $this->newLine();
        $this->info('Terminé.');
        return 0;
    }

    private function dedup(string $table, string $pivotTable, string $pivotFk, bool $dryRun): void
    {
        $this->newLine();
        $this->info("=== {$table} ===");

        $duplicates = DB::table($table)
            ->select('nom', DB::raw('COUNT(*) as cnt'), DB::raw('MIN(id) as keep_id'))
            ->groupBy('nom')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        if ($duplicates->isEmpty()) {
            $this->line("  Aucun doublon trouvé.");
            return;
        }

        $this->warn("  {$duplicates->count()} noms en doublon trouvés.");
        $totalRemoved = 0;

        foreach ($duplicates as $dup) {
            $keepId = $dup->keep_id;
            $dupeIds = DB::table($table)
                ->where('nom', $dup->nom)
                ->where('id', '!=', $keepId)
                ->pluck('id')
                ->toArray();

            $this->line("  \"{$dup->nom}\" : garder #{$keepId}, supprimer " . implode(', #', $dupeIds));

            if (!$dryRun) {
                foreach ($dupeIds as $dupeId) {
                    $pivotRows = DB::table($pivotTable)->where($pivotFk, $dupeId)->get();
                    foreach ($pivotRows as $pivot) {
                        $noteId = $pivot->note_id;
                        $exists = DB::table($pivotTable)
                            ->where('note_id', $noteId)
                            ->where($pivotFk, $keepId)
                            ->exists();

                        if ($exists) {
                            DB::table($pivotTable)
                                ->where('note_id', $noteId)
                                ->where($pivotFk, $dupeId)
                                ->delete();
                        } else {
                            DB::table($pivotTable)
                                ->where('note_id', $noteId)
                                ->where($pivotFk, $dupeId)
                                ->update([$pivotFk => $keepId]);
                        }
                    }

                    DB::table($table)->where('id', $dupeId)->delete();
                }
            }

            $totalRemoved += count($dupeIds);
        }

        $action = $dryRun ? 'à supprimer' : 'supprimés';
        $this->info("  ✓ {$totalRemoved} doublons {$action}.");
    }
}
