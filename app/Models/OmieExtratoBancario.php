<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OmieExtratoBancario extends Model
{
    protected $table = 'omie_extratos_bancarios';

    protected $fillable = [
        'empresa_codigo',
        'empresa_nome',
        'omie_cc_id',
        'codigo_interno_cc',
        'data_movimento',
        'data_compensacao',
        'valor',
        'tipo',
        'descricao',
        'documento',
        'saldo_pos',
        'payload',
        'importado_em',
    ];

    protected $casts = [
        'data_movimento'    => 'date',
        'data_compensacao'  => 'date',
        'valor'             => 'decimal:2',
        'saldo_pos'         => 'decimal:2',
        'payload'           => 'array',
        'importado_em'      => 'datetime',
    ];
}
