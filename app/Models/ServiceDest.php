<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceDest extends Model
{
    use HasFactory;

    protected $table = 'services_dest';

    protected $fillable = [
        'nom',
        'responsable',
        'email',
    ];

    public function notes()
    {
        return $this->belongsToMany(Note::class, 'note_service');
    }
}
