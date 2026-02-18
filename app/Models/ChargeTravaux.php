<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\SearchableTrait;

/**
 * Chargé de Travaux externe (ne se connecte pas à l'application)
 * Pour les chargés de travaux internes, utiliser User avec rôle approprié
 */
class ChargeTravaux extends Model
{
    use HasFactory, SearchableTrait;

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
     * Recherche par nom, téléphone ou entreprise (insensible casse/accents)
     */
    public function scopeSearch($query, $search)
    {
        $this->applySimpleSearch($query, $search, ['nom', 'telephone', 'entreprise'], []);
        return $query;
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
