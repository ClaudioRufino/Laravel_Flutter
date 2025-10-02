<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


use Illuminate\Database\Eloquent\Factories\HasFactory;

class FormacaoAnterior extends Model
{
    use HasFactory;

    
     protected $fillable = [
        'nomeEscola',
        'mediaCurso',
        'certificado',
        'anoConclusao',
        'cursoConcluido',

        'user_id', // Chave estrangeira
    ];

    // Métodos usados para relacionamento

    public function candidato(){
        return $this->belongsTo(User::class);
    }

}
