<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Curso extends Model
{
    use HasFactory;
     protected $fillable = [
        'nome',
        'duracao',
    ];

    // Métodos usados para relacionamentos

     public function inscricoes(){
        return $this->belongsToMany(Inscricao::class, 'curso_inscricao')->withTimestamps();
    }
}
