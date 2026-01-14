<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OmieContaCorrenteLancamento extends Model
{
    protected $table = 'omie_contas_correntes_lancamentos';

    protected $fillable = [
        'empresa_codigo',
        'empresa_nome',
        'nCodLanc',
        'cCodIntLanc',
        'omie_cc_id',
        'data_lancamento',
        'valor',
        'tipo',
        'descricao',
        'codigo_titulo',
        'origem',
        'payload',
        'importado_em',
    ];

    protected $casts = [
        'data_lancamento' => 'date',
        'valor'           => 'decimal:2',
        'payload'         => 'array',
        'importado_em'    => 'datetime',
    ];
}
