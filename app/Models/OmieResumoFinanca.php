<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OmieResumoFinanca extends Model
{
    protected $table = 'omie_resumo_financas';

    protected $fillable = [
        'empresa',
        'data_referencia',
        'saldo_contas',
        'limite_credito',
        'qtd_pagar',
        'total_pagar',
        'total_pagar_atraso',
        'qtd_receber',
        'total_receber',
        'total_receber_atraso',
        'fluxo_pagar',
        'fluxo_receber',
        'fluxo_saldo',
        'icone',
        'cor',
        'payload',
    ];

    protected $casts = [
        'data_referencia' => 'date',
        'payload' => 'array',
    ];
}

