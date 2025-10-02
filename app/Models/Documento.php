<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Documento extends Model
{
    use HasFactory;
     protected $fillable = [
        'nome',
        'path',

        'user_id',
    ];

    // Métodos usados para relacionamentos

     public function candidato(){
        return $this->belongsTo(User::class);
    }
}
