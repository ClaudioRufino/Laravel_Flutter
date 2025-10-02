<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pagamento extends Model
{
    use HasFactory;
     protected $fillable = [
        'data',
        'valor',
        'estado',
        'comprovativo',
        'prazoPagamento',

        'inscricao_id',
    ];

    // Métodos de relacionamento
    public function inscricao(): BelongsTo
    {
        return $this->belongsTo(Inscricao::class);
    }
}
