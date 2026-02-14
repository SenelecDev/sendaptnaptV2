<?php

namespace Database\Seeders;

use App\Models\Note;
use App\Models\NoteHistory;
use Illuminate\Database\Seeder;

class NoteHistorySeeder extends Seeder
{
    /**
     * Crée l'historique de création pour toutes les notes existantes
     */
    public function run(): void
    {
        $notes = Note::all();
        $count = 0;

        foreach ($notes as $note) {
            // Vérifier si l'historique de création existe déjà
            $exists = NoteHistory::where('note_id', $note->id)
                ->where('action', 'created')
                ->exists();

            if (!$exists) {
                NoteHistory::create([
                    'note_id' => $note->id,
                    'user_id' => $note->etabli_id,
                    'action' => 'created',
                    'description' => 'Note créée',
                    'created_at' => $note->created_at,
                    'updated_at' => $note->created_at,
                ]);
                $count++;

                // Ajouter les changements de statut selon le statut actuel
                $this->addStatusHistory($note);
            }
        }

        $this->command->info("Historique créé pour {$count} notes.");
    }

    /**
     * Ajoute les entrées d'historique selon le statut actuel de la note
     */
    private function addStatusHistory(Note $note): void
    {
        // Si vérifiée, validée ou exécutée
        if ($note->verifie_id && $note->statut !== 'brouillon' && $note->statut !== 'en étude') {
            NoteHistory::create([
                'note_id' => $note->id,
                'user_id' => $note->verifie_id,
                'action' => 'verified',
                'description' => 'Note vérifiée',
                'created_at' => $note->updated_at,
                'updated_at' => $note->updated_at,
            ]);
        }

        if ($note->valide_id && in_array($note->statut, ['validée', 'en cours d\'exécution', 'exécutée'])) {
            NoteHistory::create([
                'note_id' => $note->id,
                'user_id' => $note->valide_id,
                'action' => 'validated',
                'description' => 'Note validée',
                'created_at' => $note->updated_at,
                'updated_at' => $note->updated_at,
            ]);
        }

        if ($note->execute_id && in_array($note->statut, ['exécutée'])) {
            NoteHistory::create([
                'note_id' => $note->id,
                'user_id' => $note->execute_id,
                'action' => 'executed',
                'description' => 'Note exécutée',
                'created_at' => $note->updated_at,
                'updated_at' => $note->updated_at,
            ]);
        }

        if ($note->annule_id && $note->statut === 'annulée') {
            NoteHistory::create([
                'note_id' => $note->id,
                'user_id' => $note->annule_id,
                'action' => 'cancelled',
                'field' => 'commentanul',
                'new_value' => $note->commentanul,
                'description' => 'Note annulée',
                'created_at' => $note->updated_at,
                'updated_at' => $note->updated_at,
            ]);
        }
    }
}
