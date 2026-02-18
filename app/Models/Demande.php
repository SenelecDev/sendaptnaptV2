<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Demande extends Model
{
    use HasFactory;

    // ==================== CONSTANTES DE STATUT ====================
    
    const STATUT_CREEE = 'créée';
    const STATUT_EN_COURS = 'en cours de traitement';
    const STATUT_ACCEPTEE = 'acceptée';
    const STATUT_RETOURNEE = 'retournée';
    const STATUT_BROUILLON = 'brouillon';

    const MODE_GMAO = 'gmao';
    const MODE_MANUEL = 'manuel';

    // ==================== FILLABLE ====================

    protected $fillable = [
        'numero_demande',
        'date',
        'destinataire',
        'lieu_execution',
        'lieu_code',
        'demandeur_id',
        'charge_travaux_id',
        'charge_travaux_externe_id',
        'traite_id',
        'designation',
        'renseignement',
        'ouvrage_type',
        'mte',
        'mcce',
        'etape',
        // Dates/heures prévues
        'ddp', 'hdp',
        'dfp', 'hfp',
        'dmrp', 'dmrp_restitution',
        // Dates/heures acceptées
        'dda', 'hda',
        'dfa', 'hfa',
        'dmra',
        // Fichiers
        'schema',
        'pdf_path',
        // Statut et mode
        'statut',
        'mode_saisie',
        // Mode GMAO (JSON depuis SQL Server)
        'ouvrages_consigner_gmao',
        'ouvrages_installer_gmao',
        // Mode Manuel (texte libre)
        'ouvrages_consigner_manuel',
        'ouvrages_installer_manuel',
        // Téléphones
        'telephone_demandeur',
        'telephone_charge',
        // Motif retour
        'motif_retour',
        // Date de traitement
        'date_traitement',
    ];

    protected $casts = [
        'date' => 'date',
        'ddp' => 'date',
        'dfp' => 'date',
        'dda' => 'date',
        'dfa' => 'date',
        'mte' => 'boolean',
        'mcce' => 'boolean',
        // dmrp stocke une heure (string) ou 'non_applicable', pas un boolean
        'dmrp_restitution' => 'boolean',
        'dmra' => 'boolean',
        'ouvrages_consigner_gmao' => 'array',
        'ouvrages_installer_gmao' => 'array',
        'date_traitement' => 'datetime',
    ];

    // Propriété temporaire pour l'historique (ne pas sauvegarder en BDD)
    protected $originalForHistory = null;

    // ==================== BOOT ====================

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($demande) {
            if (empty($demande->numero_demande)) {
                $demande->numero_demande = $demande->generateNumeroDemande();
            }
        });

        static::created(function ($demande) {
            DemandeHistory::logCreation($demande);
        });

        static::updating(function ($demande) {
            // Stocker les valeurs originales pour comparaison après save
            $demande->originalForHistory = $demande->getOriginal();
        });

        static::updated(function ($demande) {
            if ($demande->originalForHistory) {
                $changes = $demande->getChanges();
                DemandeHistory::logChanges($demande, $demande->originalForHistory, $changes);
                $demande->originalForHistory = null;
            }
        });
    }

    // ==================== GÉNÉRATION NUMÉRO ====================

    public function generateNumeroDemande(): string
    {
        $currentYear = date('Y');
        
        $demandeur = $this->demandeur ?? User::find($this->demandeur_id);
        $groupe = $demandeur?->groupe;
        
        $nomGroupe = $groupe ? $groupe->nom : 'DT';
        $nomGroupe = $this->formatGroupeCode($nomGroupe);
        
        // Chercher le dernier numéro existant pour ce groupe et cette année
        $pattern = $nomGroupe . '-%-%' . $currentYear;
        $pos = strlen($nomGroupe) + 2;
        // CAST AS INTEGER compatible MySQL et PostgreSQL (UNSIGNED n'existe pas en PostgreSQL)
        $lastNumero = static::where('numero_demande', 'LIKE', $pattern)
            ->orderByRaw("CAST(SUBSTRING(numero_demande FROM {$pos} FOR 5) AS INTEGER) DESC")
            ->value('numero_demande');
        
        if ($lastNumero) {
            // Extraire le compteur du dernier numéro (ex: EP-00014-2026 → 14)
            preg_match('/' . preg_quote($nomGroupe, '/') . '-(\d+)-' . $currentYear . '/', $lastNumero, $matches);
            $count = isset($matches[1]) ? (int)$matches[1] + 1 : 1;
        } else {
            $count = 1;
        }
        
        return sprintf('%s-%05d-%s', $nomGroupe, $count, $currentYear);
    }

    /**
     * Formate le nom du groupe en code court pour le numéro de demande.
     * Si le nom est trop long (>6 caractères), prend les initiales de chaque mot.
     * Ex: "Direction Technique" → "DT", "DESA" → "DESA"
     */
    private function formatGroupeCode(string $nomGroupe): string
    {
        // Nettoyer et mettre en majuscules
        $nomGroupe = strtoupper(trim($nomGroupe));
        
        // Supprimer les caractères spéciaux (garder lettres, chiffres et espaces)
        $nomClean = preg_replace('/[^A-Z0-9\s]/', '', $nomGroupe);
        
        // Si le nom nettoyé (sans espaces) fait plus de 6 caractères, prendre les initiales
        $nomSansEspaces = preg_replace('/\s+/', '', $nomClean);
        
        if (strlen($nomSansEspaces) > 6) {
            // Extraire la première lettre de chaque mot
            $mots = preg_split('/\s+/', $nomClean);
            $initiales = '';
            foreach ($mots as $mot) {
                if (!empty($mot)) {
                    $initiales .= $mot[0];
                }
            }
            return substr($initiales, 0, 10);
        }
        
        // Sinon garder le nom nettoyé (max 10 caractères)
        return substr($nomSansEspaces, 0, 10);
    }

    // ==================== RELATIONS ====================

    public function demandeur()
    {
        return $this->belongsTo(User::class, 'demandeur_id');
    }

    public function chargeTravaux()
    {
        return $this->belongsTo(User::class, 'charge_travaux_id');
    }

    /**
     * Chargé de travaux externe (pour les contacts hors système)
     */
    public function chargeTravauxExterne()
    {
        return $this->belongsTo(ChargeTravaux::class, 'charge_travaux_externe_id');
    }

    /**
     * Retourne le chargé de travaux (interne ou externe)
     */
    public function getChargeTravauxInfoAttribute(): ?object
    {
        if ($this->charge_travaux_id && $this->chargeTravaux) {
            return (object) [
                'type' => 'interne',
                'id' => $this->chargeTravaux->id,
                'nom' => $this->chargeTravaux->name,
                'telephone' => $this->telephone_charge ?? $this->chargeTravaux->telephone,
                'entreprise' => 'SENELEC',
                'matricule' => $this->chargeTravaux->matricule,
            ];
        }
        
        if ($this->charge_travaux_externe_id && $this->chargeTravauxExterne) {
            return (object) [
                'type' => 'externe',
                'id' => $this->chargeTravauxExterne->id,
                'nom' => $this->chargeTravauxExterne->nom,
                'telephone' => $this->chargeTravauxExterne->telephone,
                'entreprise' => $this->chargeTravauxExterne->entreprise,
                'matricule' => null,
            ];
        }
        
        return null;
    }

    public function traite()
    {
        return $this->belongsTo(User::class, 'traite_id');
    }

    public function note()
    {
        return $this->hasOne(Note::class);
    }

    /**
     * Alias pour compatibilité avec le code existant
     * Une demande a une seule note, mais le code utilise "notes" au pluriel
     */
    public function notes()
    {
        return $this->hasOne(Note::class);
    }

    /**
     * Historique des modifications
     */
    public function histories()
    {
        return $this->hasMany(DemandeHistory::class)->orderBy('created_at', 'desc');
    }

    // ==================== ACCESSORS ====================

    public function getSchemaUrlAttribute(): ?string
    {
        return $this->schema ? Storage::url($this->schema) : null;
    }

    public function getPdfUrlAttribute(): ?string
    {
        return $this->pdf_path ? Storage::url($this->pdf_path) : null;
    }

    public function getIsGmaoModeAttribute(): bool
    {
        return $this->mode_saisie === self::MODE_GMAO;
    }

    public function getIsManuelModeAttribute(): bool
    {
        return $this->mode_saisie === self::MODE_MANUEL;
    }

    // ==================== SCOPES ====================

    public function scopeCreees($query)
    {
        return $query->where('statut', self::STATUT_CREEE);
    }

    public function scopeEnCours($query)
    {
        return $query->where('statut', self::STATUT_EN_COURS);
    }

    public function scopeAcceptees($query)
    {
        return $query->where('statut', self::STATUT_ACCEPTEE);
    }

    public function scopeRetournees($query)
    {
        return $query->where('statut', self::STATUT_RETOURNEE);
    }

    public function scopeForDemandeur($query, User $user)
    {
        return $query->where('demandeur_id', $user->id);
    }

    public function scopeForGroupe($query, Groupe $groupe)
    {
        return $query->whereHas('demandeur', function ($q) use ($groupe) {
            $q->where('groupe_id', $groupe->id);
        });
    }

    // ==================== MÉTHODES ====================

    public function canBeEdited(): bool
    {
        return in_array($this->statut, [self::STATUT_CREEE, self::STATUT_BROUILLON, self::STATUT_RETOURNEE]);
    }

    public function canCreateNote(): bool
    {
        return $this->statut === self::STATUT_ACCEPTEE && !$this->note;
    }

    public function getStatutBadgeClass(): string
    {
        return match ($this->statut) {
            self::STATUT_CREEE => 'badge-info',
            self::STATUT_EN_COURS => 'badge-warning',
            self::STATUT_ACCEPTEE => 'badge-success',
            self::STATUT_RETOURNEE => 'badge-danger',
            self::STATUT_BROUILLON => 'badge-secondary',
            default => 'badge-secondary',
        };
    }
}
