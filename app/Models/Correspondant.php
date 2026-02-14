<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Correspondant extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'fonction',
        'adresse',
        'matricule',
        'telephone',
    ];

    public function notes()
    {
        return $this->belongsToMany(Note::class, 'note_correspondant');
    }
}
