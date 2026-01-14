<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OmieDocumentoFiscal extends Model
{
    protected $table = 'omie_documentos_fiscais';

    protected $fillable = [
        'empresa',
        'modelo',
        'numero',
        'serie',
        'chave',
        'data_emissao',
        'hora_emissao',
        'valor',
        'status',
        'omie_id_nf',
        'omie_id_pedido',
        'omie_id_os',
        'omie_id_ct',
        'omie_id_receb',
        'omie_id_cupom',
        'xml',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
        'data_emissao' => 'date',
    ];
}
