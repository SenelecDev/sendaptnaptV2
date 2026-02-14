<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NoteHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'note_id',
        'user_id',
        'action',
        'field',
        'old_value',
        'new_value',
        'description',
    ];

    // Labels lisibles pour les champs
    const FIELD_LABELS = [
        'statut' => 'Statut',
        'numero_note' => 'Numéro',
        'numero_semaine' => 'Semaine',
        'date' => 'Date',
        'dre' => 'Date remise étude',
        'ddt' => 'Date début travaux',
        'dft' => 'Date fin travaux',
        'drex' => 'Date réelle exécution',
        'renseignementN' => 'Renseignements',
        'motif' => 'Motif de retour 1',
        'motifbis' => 'Motif de retour 2',
        'commentanul' => 'Commentaire annulation',
        'etabli_id' => 'Établi par',
        'verifie_id' => 'Vérifié par',
        'valide_id' => 'Validé par',
        'execute_id' => 'Exécuté par',
        'retourne1_id' => 'Retourné par (1)',
        'retourne2_id' => 'Retourné par (2)',
        'annule_id' => 'Annulé par',
        'en_cours_execution_id' => 'En cours d\'exécution par',
        'document' => 'Document',
        'etude' => 'Étude',
        'fiche_manoeuvre' => 'Fiche de manœuvre',
    ];

    // ==================== RELATIONS ====================

    public function note()
    {
        return $this->belongsTo(Note::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ==================== ACCESSEURS ====================

    public function getFieldLabelAttribute(): string
    {
        return self::FIELD_LABELS[$this->field] ?? $this->field;
    }

    public function getActionLabelAttribute(): string
    {
        return match($this->action) {
            'created' => 'Création',
            'updated' => 'Modification',
            'status_changed' => 'Changement de statut',
            'verified' => 'Vérification',
            'validated' => 'Validation',
            'executed' => 'Exécution',
            'returned' => 'Retour',
            'cancelled' => 'Annulation',
            default => $this->action,
        };
    }

    // ==================== HELPERS ====================

    /**
     * Enregistre un historique de création
     */
    public static function logCreation(Note $note, ?int $userId = null): void
    {
        self::create([
            'note_id' => $note->id,
            'user_id' => $userId ?? auth()->id(),
            'action' => 'created',
            'description' => 'Note créée',
        ]);
    }

    /**
     * Enregistre un changement de statut
     */
    public static function logStatusChange(Note $note, string $oldStatus, string $newStatus, ?int $userId = null, ?string $description = null): void
    {
        self::create([
            'note_id' => $note->id,
            'user_id' => $userId ?? auth()->id(),
            'action' => 'status_changed',
            'field' => 'statut',
            'old_value' => $oldStatus,
            'new_value' => $newStatus,
            'description' => $description ?? "Statut changé de \"{$oldStatus}\" à \"{$newStatus}\"",
        ]);
    }

    /**
     * Enregistre une modification de champ
     */
    public static function logFieldChange(Note $note, string $field, $oldValue, $newValue, ?int $userId = null): void
    {
        $label = self::FIELD_LABELS[$field] ?? $field;
        
        self::create([
            'note_id' => $note->id,
            'user_id' => $userId ?? auth()->id(),
            'action' => 'updated',
            'field' => $field,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'description' => "{$label} modifié",
        ]);
    }

    /**
     * Enregistre plusieurs modifications
     */
    public static function logChanges(Note $note, array $original, array $changes, ?int $userId = null): void
    {
        $trackedFields = array_keys(self::FIELD_LABELS);
        
        foreach ($changes as $field => $newValue) {
            if (!in_array($field, $trackedFields)) {
                continue;
            }
            
            $oldValue = $original[$field] ?? null;
            
            // Ne pas logger si pas de changement réel
            if ($oldValue == $newValue) {
                continue;
            }

            // Traitement spécial pour le statut
            if ($field === 'statut') {
                self::logStatusChange($note, $oldValue ?? '', $newValue, $userId);
            } else {
                self::logFieldChange($note, $field, $oldValue, $newValue, $userId);
            }
        }
    }

    /**
     * Logs spécifiques pour les actions du workflow
     */
    public static function logVerification(Note $note, ?int $userId = null): void
    {
        self::create([
            'note_id' => $note->id,
            'user_id' => $userId ?? auth()->id(),
            'action' => 'verified',
            'description' => 'Note vérifiée',
        ]);
    }

    public static function logValidation(Note $note, ?int $userId = null): void
    {
        self::create([
            'note_id' => $note->id,
            'user_id' => $userId ?? auth()->id(),
            'action' => 'validated',
            'description' => 'Note validée',
        ]);
    }

    public static function logExecution(Note $note, ?int $userId = null): void
    {
        self::create([
            'note_id' => $note->id,
            'user_id' => $userId ?? auth()->id(),
            'action' => 'executed',
            'description' => 'Note exécutée',
        ]);
    }

    public static function logReturn(Note $note, string $motif, ?int $userId = null): void
    {
        self::create([
            'note_id' => $note->id,
            'user_id' => $userId ?? auth()->id(),
            'action' => 'returned',
            'new_value' => $motif,
            'description' => 'Note retournée',
        ]);
    }

    public static function logCancellation(Note $note, string $commentaire, ?int $userId = null): void
    {
        self::create([
            'note_id' => $note->id,
            'user_id' => $userId ?? auth()->id(),
            'action' => 'cancelled',
            'new_value' => $commentaire,
            'description' => 'Note annulée',
        ]);
    }
}
