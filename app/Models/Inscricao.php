<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Inscricao extends Model
{
    use HasFactory;
     protected $fillable = [
        'data',
        'turno',

        'user_id',
    ];


    // Métodos usados para relacionamentos

    public function cursos(): BelongsToMany
    {
        return $this->belongsToMany(Curso::class, 'curso_inscricao')->withTimestamps();
    }

    public function pagamento(): HasOne
    {
        return $this->hasOne(Pagamento::class);
    }

    public function candidato(): HasOne
    {
        return $this->hasOne(User::class);
    }

}
