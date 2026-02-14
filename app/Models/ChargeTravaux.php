<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Chargé de Travaux externe (ne se connecte pas à l'application)
 * Pour les chargés de travaux internes, utiliser User avec rôle approprié
 */
class ChargeTravaux extends Model
{
    use HasFactory;

    protected $table = 'charges_travaux';

    protected $fillable = [
        'nom',
        'telephone',
        'entreprise',
        'service',
        'actif',
    ];

    protected $casts = [
        'actif' => 'boolean',
    ];

    /**
     * Les demandes associées à ce chargé de travaux externe
     */
    public function demandes()
    {
        return $this->hasMany(Demande::class, 'charge_travaux_externe_id');
    }

    /**
     * Recherche par nom ou téléphone
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('nom', 'like', "%{$search}%")
              ->orWhere('telephone', 'like', "%{$search}%")
              ->orWhere('entreprise', 'like', "%{$search}%");
        });
    }

    /**
     * Seulement les actifs
     */
    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }

    /**
     * Affichage formaté (nom + entreprise)
     */
    public function getNomCompletAttribute(): string
    {
        $display = $this->nom;
        if ($this->entreprise) {
            $display .= ' (' . $this->entreprise . ')';
        }
        return $display;
    }
}
