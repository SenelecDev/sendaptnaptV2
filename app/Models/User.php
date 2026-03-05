<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use LdapRecord\Laravel\Auth\LdapAuthenticatable;
use LdapRecord\Laravel\Auth\AuthenticatesWithLdap;
use App\Traits\SearchableTrait;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable implements LdapAuthenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, AuthenticatesWithLdap, SearchableTrait, LogsActivity;

    protected $fillable = [
        'name',
        'email',
        'password',
        // Identifiants
        'matricule',
        'ldap_username',
        'ldap_guid',
        // Informations personnelles
        'nom',
        'prenom',
        'poste',
        'telephone',
        'photo',
        // Organisation
        'organisation',
        'entreprise',
        'service',
        'direction',
        'departement',
        // Oracle HR
        'oracle_person_id',
        'fonction_oracle',
        'oracle_synced_at',
        // Signatures
        'signature',
        'stamp',
        // Groupe et statut
        'groupe_id',
        'is_active',
        'notifications_enabled',
        'onboarding_completed',
        // Timestamps
        'last_sync_at',
        'last_activity_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'onboarding_completed' => 'boolean',
        'oracle_synced_at' => 'datetime',
        'last_sync_at' => 'datetime',
        'last_activity_at' => 'datetime',
    ];

    // ==================== ACCESSEURS ====================

    const SUPER_ADMIN_MATRICULE = 'C01156';

    public function isSuperAdmin(): bool
    {
        return strtoupper(trim($this->matricule)) === self::SUPER_ADMIN_MATRICULE;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'matricule', 'telephone', 'appartenance', 'groupe_id', 'n1_id', 'signature'])
            ->logOnlyDirty()
            ->useLogName('users')
            ->dontSubmitEmptyLogs();
    }

    /**
     * Retourne le nom complet de l'utilisateur
     */
    public function getFullNameAttribute(): string
    {
        return trim(($this->prenom ?? '') . ' ' . ($this->nom ?? '')) ?: $this->name ?? $this->matricule;
    }

    /**
     * Service ou département (appartenance) - affiche l'un si l'autre est vide
     */
    public function getAppartenanceAttribute(): ?string
    {
        return $this->service ?? $this->departement ?? null;
    }

    /**
     * Retourne les initiales de l'utilisateur
     */
    public function getInitialsAttribute(): string
    {
        $prenom = $this->prenom ?? '';
        $nom = $this->nom ?? '';
        
        if ($prenom && $nom) {
            return strtoupper(substr($prenom, 0, 1) . substr($nom, 0, 1));
        }
        
        return strtoupper(substr($this->name ?? $this->matricule ?? 'U', 0, 2));
    }

    /**
     * URL de la signature
     */
    public function getSignatureUrlAttribute(): ?string
    {
        return $this->signature ? asset('storage/' . $this->signature) : null;
    }

    /**
     * URL du cachet
     */
    public function getStampUrlAttribute(): ?string
    {
        return $this->stamp ? asset('storage/' . $this->stamp) : null;
    }

    /**
     * URL de la photo
     * - base64 : retourné tel quel
     * - profil/xxx.jpg : fichier dans public/profil/ (URL /profil/xxx.jpg)
     * - autre chemin : asset direct
     * - si photo null : fallback profil/{matricule}.jpg (le navigateur gère le 404 via onerror)
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if ($this->photo) {
            if (str_starts_with($this->photo, 'data:image')) {
                return $this->photo;
            }
            if (str_starts_with($this->photo, 'profil/')) {
                return asset($this->photo);
            }
            return asset($this->photo);
        }

        // Fallback : photo par matricule (sans file_exists pour éviter problèmes Docker/casse)
        if ($this->matricule) {
            return asset('profil/' . trim($this->matricule) . '.jpg');
        }

        return null;
    }

    // ==================== RELATIONS ====================
    
    public function groupe()
    {
        return $this->belongsTo(Groupe::class);
    }

    public function demandes()
    {
        return $this->hasMany(Demande::class, 'demandeur_id');
    }

    public function demandesTraitees()
    {
        return $this->hasMany(Demande::class, 'traite_id');
    }

    public function notesEtablies()
    {
        return $this->hasMany(Note::class, 'etabli_id');
    }

    public function notesVerifiees()
    {
        return $this->hasMany(Note::class, 'verifie_id');
    }

    public function notesValidees()
    {
        return $this->hasMany(Note::class, 'valide_id');
    }

    public function absences()
    {
        return $this->hasMany(Absence::class, 'user_id');
    }

    public function interims()
    {
        return $this->hasMany(Absence::class, 'interim_id');
    }

    public function observations()
    {
        return $this->hasMany(Observation::class);
    }

    // ==================== INTÉRIM ====================

    /**
     * Vérifie si l'utilisateur est actuellement intérimaire pour un rôle donné
     * Si le rôle de l'absence est NULL, l'intérimaire couvre TOUS les rôles du titulaire
     */
    public function estInterimaireA(string $role, $date = null): bool
    {
        $date = $date ?? now()->toDateString();
        
        // Vérifie d'abord s'il y a une absence avec ce rôle spécifique
        $absenceSpecifique = Absence::where('interim_id', $this->id)
            ->whereDate('date_debut', '<=', $date)
            ->whereDate('date_fin', '>=', $date)
            ->where('role', $role)
            ->exists();
        
        if ($absenceSpecifique) {
            return true;
        }
        
        // Vérifie s'il y a une absence avec role = NULL (tous les rôles du titulaire)
        $absenceGlobale = Absence::where('interim_id', $this->id)
            ->whereDate('date_debut', '<=', $date)
            ->whereDate('date_fin', '>=', $date)
            ->whereNull('role')
            ->with('user')
            ->first();
        
        if ($absenceGlobale && $absenceGlobale->user) {
            // Vérifie que le titulaire a bien ce rôle
            return $absenceGlobale->user->hasRole($role);
        }
        
        return false;
    }

    /**
     * Retourne tous les rôles d'intérim actifs de l'utilisateur
     */
    public function getRolesInterimActifs($date = null): array
    {
        $date = $date ?? now()->toDateString();
        
        $absences = Absence::where('interim_id', $this->id)
            ->whereDate('date_debut', '<=', $date)
            ->whereDate('date_fin', '>=', $date)
            ->with('user')
            ->get();

        $roles = [];
        $rolesDisponibles = ['demandeur', 'desa', 'verificateur', 'valideur', 'operateur', 'operateurchef'];

        foreach ($absences as $absence) {
            if ($absence->role === null) {
                // NULL = hérite tous les rôles du titulaire
                $rolesTitulaire = $absence->user->getRoleNames()->toArray();
                foreach ($rolesTitulaire as $r) {
                    if (in_array($r, $rolesDisponibles) && !isset($roles[$r])) {
                        $roles[$r] = [
                            'role' => $r,
                            'titulaire' => $absence->user,
                            'absence' => $absence,
                        ];
                    }
                }
            } else {
                if (!isset($roles[$absence->role])) {
                    $roles[$absence->role] = [
                        'role' => $absence->role,
                        'titulaire' => $absence->user,
                        'absence' => $absence,
                    ];
                }
            }
        }

        return $roles;
    }

    /**
     * Retourne l'utilisateur absent que cet utilisateur remplace pour un rôle
     */
    public function absentRemplace(string $role, $date = null): ?User
    {
        $date = $date ?? now()->toDateString();
        
        // Cherche d'abord une absence avec ce rôle spécifique
        $absenceSpecifique = Absence::where('interim_id', $this->id)
            ->whereDate('date_debut', '<=', $date)
            ->whereDate('date_fin', '>=', $date)
            ->where('role', $role)
            ->with('user')
            ->first();
        
        if ($absenceSpecifique) {
            return $absenceSpecifique->user;
        }
        
        // Cherche une absence avec role = NULL (tous les rôles du titulaire)
        $absenceGlobale = Absence::where('interim_id', $this->id)
            ->whereDate('date_debut', '<=', $date)
            ->whereDate('date_fin', '>=', $date)
            ->whereNull('role')
            ->with('user')
            ->first();
        
        if ($absenceGlobale && $absenceGlobale->user && $absenceGlobale->user->hasRole($role)) {
            return $absenceGlobale->user;
        }
        
        return null;
    }

    /**
     * Vérifie si l'utilisateur a un rôle OU est intérimaire pour ce rôle
     */
    public function hasRoleOrInterim(string $role, $date = null): bool
    {
        return $this->hasRole($role) || $this->estInterimaireA($role, $date);
    }

    /**
     * Vérifie si l'utilisateur est actuellement absent
     */
    public function estAbsent($date = null): bool
    {
        $date = $date ?? now()->toDateString();
        
        return Absence::where('user_id', $this->id)
            ->whereDate('date_debut', '<=', $date)
            ->whereDate('date_fin', '>=', $date)
            ->exists();
    }

    /**
     * Retourne les intérimaires actuels de l'utilisateur
     */
    public function getInterimaireActuel($date = null): ?User
    {
        $date = $date ?? now()->toDateString();
        
        $absence = Absence::where('user_id', $this->id)
            ->whereDate('date_debut', '<=', $date)
            ->whereDate('date_fin', '>=', $date)
            ->first();

        return $absence?->interim;
    }

    // ==================== LDAP ====================

    public function getLdapDomainColumn(): string
    {
        return 'ldap_guid';
    }

    public function getLdapGuidColumn(): string
    {
        return 'ldap_guid';
    }

    // ==================== SCOPES ====================

    /**
     * Scope pour les utilisateurs actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour rechercher par matricule, nom ou email (insensible casse/accents)
     */
    public function scopeSearch($query, string $search)
    {
        $this->applySimpleSearch($query, $search, ['matricule', 'nom', 'prenom', 'email', 'name'], []);
        return $query;
    }
}
