<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'interim_id',
        'date_debut',
        'date_fin',
        'motif',
        'role',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];

    // ==================== RELATIONS ====================

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function interim()
    {
        return $this->belongsTo(User::class, 'interim_id');
    }

    // ==================== SCOPES ====================

    public function scopeActive($query)
    {
        $today = now()->startOfDay();
        return $query->where('date_debut', '<=', $today)
                     ->where('date_fin', '>=', $today);
    }

    public function scopeAtDate($query, $date = null)
    {
        $date = $date ?? now()->startOfDay();
        return $query->where('date_debut', '<=', $date)
                     ->where('date_fin', '>=', $date);
    }

    public function scopeFuture($query)
    {
        $today = now()->startOfDay();
        return $query->where('date_debut', '>', $today);
    }

    public function scopePast($query)
    {
        $today = now()->startOfDay();
        return $query->where('date_fin', '<', $today);
    }

    public function scopeForRole($query, string $role)
    {
        return $query->where(function ($q) use ($role) {
            $q->where('role', $role)
              ->orWhereNull('role'); // NULL = tous les rôles
        });
    }

    public function scopeForUser($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }

    public function scopeForInterim($query, User $user)
    {
        return $query->where('interim_id', $user->id);
    }

    // ==================== MÉTHODES ====================

    public function isActive(): bool
    {
        $today = now()->startOfDay();
        return $this->date_debut <= $today && $this->date_fin >= $today;
    }

    public function isFuture(): bool
    {
        $today = now()->startOfDay();
        return $this->date_debut > $today;
    }

    public function isPast(): bool
    {
        $today = now()->startOfDay();
        return $this->date_fin < $today;
    }

    /**
     * Vérifie si cette absence couvre un rôle spécifique
     * Si role est NULL, elle couvre tous les rôles
     */
    public function coversRole(string $role): bool
    {
        return $this->role === null || $this->role === $role;
    }

    /**
     * Retourne les rôles couverts par cette absence
     */
    public function getCoveredRoles(): array
    {
        if ($this->role === null) {
            // Tous les rôles du titulaire
            return $this->user?->getRoleNames()->toArray() ?? [];
        }
        return [$this->role];
    }
}
