<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OmieDfeDoc extends Model
{
    protected $table = 'omie_dfe_docs';

    protected $fillable = [
        'empresa',
        'tipo_documento',
        'id_documento_omie',
        'numero',
        'chave_acesso',
        'data_emissao',
        'xml',
        'pdf_url',
        'portal_url',
        'status_codigo',
        'status_descricao',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
        'data_emissao' => 'date',
    ];
}
