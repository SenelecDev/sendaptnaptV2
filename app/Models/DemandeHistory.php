<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemandeHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'demande_id',
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
        'designation' => 'Désignation',
        'lieu_execution' => 'Lieu d\'exécution',
        'destinataire' => 'Destinataire',
        'renseignement' => 'Renseignements',
        'ddp' => 'Date début prévue',
        'hdp' => 'Heure début prévue',
        'dfp' => 'Date fin prévue',
        'hfp' => 'Heure fin prévue',
        'dda' => 'Date début acceptée',
        'hda' => 'Heure début acceptée',
        'dfa' => 'Date fin acceptée',
        'hfa' => 'Heure fin acceptée',
        'motif_retour' => 'Motif de retour',
        'mte' => 'MTE',
        'mcce' => 'MCCE',
    ];

    // ==================== RELATIONS ====================

    public function demande()
    {
        return $this->belongsTo(Demande::class);
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
            default => $this->action,
        };
    }

    // ==================== HELPERS ====================

    /**
     * Enregistre un historique de création
     */
    public static function logCreation(Demande $demande, ?int $userId = null): void
    {
        self::create([
            'demande_id' => $demande->id,
            'user_id' => $userId ?? auth()->id(),
            'action' => 'created',
            'description' => 'Demande créée',
        ]);
    }

    /**
     * Enregistre un changement de statut
     */
    public static function logStatusChange(Demande $demande, string $oldStatus, string $newStatus, ?int $userId = null, ?string $description = null): void
    {
        self::create([
            'demande_id' => $demande->id,
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
    public static function logFieldChange(Demande $demande, string $field, $oldValue, $newValue, ?int $userId = null): void
    {
        $label = self::FIELD_LABELS[$field] ?? $field;
        
        self::create([
            'demande_id' => $demande->id,
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
    public static function logChanges(Demande $demande, array $original, array $changes, ?int $userId = null): void
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
                self::logStatusChange($demande, $oldValue ?? '', $newValue, $userId);
            } else {
                self::logFieldChange($demande, $field, $oldValue, $newValue, $userId);
            }
        }
    }
}
