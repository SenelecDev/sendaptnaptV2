<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Observation extends Model
{
    use HasFactory;

    const TYPE_BUG = 'bug';
    const TYPE_SUGGESTION = 'suggestion';
    const TYPE_QUESTION = 'question';
    const TYPE_AUTRE = 'autre';

    const PRIORITE_BASSE = 'basse';
    const PRIORITE_NORMALE = 'normale';
    const PRIORITE_HAUTE = 'haute';
    const PRIORITE_URGENTE = 'urgente';

    const STATUT_OUVERT = 'ouvert';
    const STATUT_EN_COURS = 'en cours';
    const STATUT_RESOLU = 'résolu';
    const STATUT_FERME = 'fermé';

    protected $fillable = [
        'user_id',
        'sujet',
        'description',
        'type',
        'priorite',
        'statut',
        'lu',
        'reponse_admin',
        'date_reponse',
        'traite_par',
    ];

    protected $casts = [
        'date_reponse' => 'datetime',
        'lu' => 'boolean',
    ];

    // ==================== RELATIONS ====================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function traitePar()
    {
        return $this->belongsTo(User::class, 'traite_par');
    }

    // ==================== SCOPES ====================

    public function scopeOuverts($query)
    {
        return $query->where('statut', self::STATUT_OUVERT);
    }

    public function scopeEnCours($query)
    {
        return $query->where('statut', self::STATUT_EN_COURS);
    }

    public function scopeResolus($query)
    {
        return $query->where('statut', self::STATUT_RESOLU);
    }

    public function scopeUrgents($query)
    {
        return $query->where('priorite', self::PRIORITE_URGENTE);
    }

    // ==================== MÉTHODES ====================

    public function getTypeBadgeClass(): string
    {
        return match ($this->type) {
            self::TYPE_BUG => 'badge-danger',
            self::TYPE_SUGGESTION => 'badge-info',
            self::TYPE_QUESTION => 'badge-warning',
            default => 'badge-secondary',
        };
    }

    public function getPrioriteBadgeClass(): string
    {
        return match ($this->priorite) {
            self::PRIORITE_URGENTE => 'badge-danger',
            self::PRIORITE_HAUTE => 'badge-warning',
            self::PRIORITE_NORMALE => 'badge-info',
            default => 'badge-secondary',
        };
    }

    public function getStatutBadgeClass(): string
    {
        return match ($this->statut) {
            self::STATUT_OUVERT => 'badge-info',
            self::STATUT_EN_COURS => 'badge-warning',
            self::STATUT_RESOLU => 'badge-success',
            self::STATUT_FERME => 'badge-secondary',
            default => 'badge-secondary',
        };
    }
}
