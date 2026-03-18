<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Note extends Model
{
    use HasFactory, LogsActivity;

    // ==================== CONSTANTES DE STATUT ====================
    
    const STATUT_BROUILLON = 'brouillon';
    const STATUT_EN_ETUDE = 'en étude';
    const STATUT_EN_ATTENTE_VERIFICATION = 'en attente de vérification';
    const STATUT_VERIFIEE = 'vérifiée';
    const STATUT_EN_ATTENTE_VALIDATION = 'en attente de validation';
    const STATUT_VALIDEE = 'validée';
    const STATUT_EN_COURS_EXECUTION = 'en cours d\'exécution';
    const STATUT_EXECUTEE = 'executée';
    const STATUT_RETOURNEE = 'retournée';
    const STATUT_ANNULEE = 'annulée';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['statut', 'verificateur_id', 'validateur_id'])
            ->logOnlyDirty()
            ->useLogName('notes')
            ->dontSubmitEmptyLogs();
    }

    // ==================== FILLABLE ====================

    protected $fillable = [
        'numero_note',
        'numero_semaine',
        'date',
        'demande_id',
        // Acteurs avec signatures
        'etabli_id',
        'verifie_id',
        'valide_id',
        'retourne1_id',
        'retourne2_id',
        'execute_id',
        'en_cours_execution_id',
        'annule_id',
        // Dates
        'dre',
        'ddt', 'dft',
        'drex',
        // Dates réelles d'exécution (ne pas écraser les dates acceptées)
        'dre_reel',
        'ddt_reel', 'dft_reel',
        'drex_reel',
        // Destinataires
        'chargecon_id',
        'correspondant_id',
        'servicedest_id',
        'adresse_charges_consignation',
        'adresse_correspondants',
        // Documents
        'document',
        'etude',
        'fiche_manoeuvre',
        // Informations
        'renseignementN',
        'motif',
        'motifbis',
        'commentanul',
        // Statut
        'statut',
    ];

    protected $casts = [
        'date' => 'date',
        'dre' => 'datetime',
        'ddt' => 'datetime',
        'dft' => 'datetime',
        'drex' => 'datetime',
        'dre_reel' => 'datetime',
        'ddt_reel' => 'datetime',
        'dft_reel' => 'datetime',
        'drex_reel' => 'datetime',
    ];

    // Propriété temporaire pour l'historique (ne pas sauvegarder en BDD)
    protected $originalForHistory = null;

    // ==================== BOOT ====================

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($note) {
            if (empty($note->numero_note)) {
                $note->numero_note = $note->generateNumeroNote();
            }
            if (empty($note->numero_semaine)) {
                $note->numero_semaine = date('W');
            }
        });

        // Historique: log création
        static::created(function ($note) {
            NoteHistory::logCreation($note);
        });

        // Historique: sauvegarde état avant modification
        static::updating(function ($note) {
            $note->originalForHistory = $note->getOriginal();
        });

        // Historique: log modifications
        static::updated(function ($note) {
            if ($note->originalForHistory) {
                NoteHistory::logChanges($note, $note->originalForHistory, $note->getChanges());
                $note->originalForHistory = null;
            }
        });
    }

    // ==================== RELATIONS HISTORIQUE ====================

    public function histories()
    {
        return $this->hasMany(NoteHistory::class)->orderByDesc('created_at');
    }

    // ==================== GÉNÉRATION NUMÉRO ====================

    public function generateNumeroNote(): string
    {
        $currentYear = date('Y');
        $count = static::whereYear('created_at', $currentYear)->count() + 1;
        return sprintf('%05d-%s', $count, $currentYear);
    }

    // ==================== RELATIONS ====================

    public function demande()
    {
        return $this->belongsTo(Demande::class);
    }

    public function etabli()
    {
        return $this->belongsTo(User::class, 'etabli_id');
    }

    // Alias pour etabli
    public function etabliPar()
    {
        return $this->etabli();
    }

    public function verifie()
    {
        return $this->belongsTo(User::class, 'verifie_id');
    }

    // Alias pour verifie
    public function verifiePar()
    {
        return $this->verifie();
    }

    public function valide()
    {
        return $this->belongsTo(User::class, 'valide_id');
    }

    // Alias pour valide
    public function validePar()
    {
        return $this->valide();
    }

    public function retourne1()
    {
        return $this->belongsTo(User::class, 'retourne1_id');
    }

    public function retourne2()
    {
        return $this->belongsTo(User::class, 'retourne2_id');
    }

    public function execute()
    {
        return $this->belongsTo(User::class, 'execute_id');
    }

    public function enCoursExecution()
    {
        return $this->belongsTo(User::class, 'en_cours_execution_id');
    }

    public function annule()
    {
        return $this->belongsTo(User::class, 'annule_id');
    }

    // Relations Many-to-Many
    public function chargesCons()
    {
        return $this->belongsToMany(ChargeCons::class, 'note_charge_consignation');
    }

    // Alias pour chargesCons
    public function chargecons()
    {
        return $this->chargesCons();
    }

    // Alias pour compatibilité avec la vue PDF
    public function chargesConsignation()
    {
        return $this->chargesCons();
    }

    public function correspondants()
    {
        return $this->belongsToMany(Correspondant::class, 'note_correspondant');
    }

    public function servicesDest()
    {
        return $this->belongsToMany(ServiceDest::class, 'note_service');
    }

    // Alias pour servicesDest
    public function services()
    {
        return $this->servicesDest();
    }

    // ==================== ACCESSORS ====================

    public function getDocumentUrlAttribute(): ?string
    {
        return $this->document ? Storage::url($this->document) : null;
    }

    public function getEtudeUrlAttribute(): ?string
    {
        return $this->etude ? Storage::url($this->etude) : null;
    }

    public function getFicheManoeuvreUrlAttribute(): ?string
    {
        return $this->fiche_manoeuvre ? Storage::url($this->fiche_manoeuvre) : null;
    }

    // ==================== SCOPES ====================

    public function scopeEnAttenteVerification($query)
    {
        return $query->where('statut', self::STATUT_EN_ATTENTE_VERIFICATION);
    }

    public function scopeVerifiees($query)
    {
        return $query->where('statut', self::STATUT_VERIFIEE);
    }

    public function scopeEnAttenteValidation($query)
    {
        return $query->where('statut', self::STATUT_EN_ATTENTE_VALIDATION);
    }

    public function scopeValidees($query)
    {
        return $query->where('statut', self::STATUT_VALIDEE);
    }

    public function scopeEnCoursExecution($query)
    {
        return $query->where('statut', self::STATUT_EN_COURS_EXECUTION);
    }

    public function scopeExecutees($query)
    {
        return $query->where('statut', self::STATUT_EXECUTEE);
    }

    public function scopeSemaine($query, int $semaine)
    {
        return $query->where('numero_semaine', $semaine);
    }

    public function scopeForEtabli($query, User $user)
    {
        return $query->where('etabli_id', $user->id);
    }

    // ==================== MÉTHODES ====================

    public function getStatutBadgeClass(): string
    {
        return match ($this->statut) {
            self::STATUT_BROUILLON => 'badge-secondary',
            self::STATUT_EN_ETUDE => 'badge-info',
            self::STATUT_EN_ATTENTE_VERIFICATION => 'badge-warning',
            self::STATUT_VERIFIEE => 'badge-info',
            self::STATUT_EN_ATTENTE_VALIDATION => 'badge-warning',
            self::STATUT_VALIDEE => 'badge-success',
            self::STATUT_EN_COURS_EXECUTION => 'badge-warning',
            self::STATUT_EXECUTEE => 'badge-success',
            self::STATUT_RETOURNEE => 'badge-danger',
            self::STATUT_ANNULEE => 'badge-danger',
            default => 'badge-secondary',
        };
    }

    public function canBeVerified(): bool
    {
        return $this->statut === self::STATUT_EN_ATTENTE_VERIFICATION;
    }

    public function canBeValidated(): bool
    {
        return in_array($this->statut, [self::STATUT_VERIFIEE, self::STATUT_EN_ATTENTE_VALIDATION]);
    }

    public function canBeExecuted(): bool
    {
        return $this->statut === self::STATUT_VALIDEE;
    }

    public function canBeCancelled(): bool
    {
        return !in_array($this->statut, [self::STATUT_EXECUTEE, self::STATUT_ANNULEE]);
    }
}
