<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OmieOrcamento extends Model
{
    protected $table = 'omie_orcamentos';

    protected $fillable = [
        'empresa',
        'ano',
        'mes',
        'codigo_categoria',
        'descricao_categoria',
        'valor_previsto',
        'valor_realizado',
        'payload',
    ];

    protected $casts = [
        'valor_previsto' => 'decimal:2',
        'valor_realizado' => 'decimal:2',
        'payload' => 'array',
    ];
}
