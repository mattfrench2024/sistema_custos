<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OmieCliente extends Model
{
    protected $table = 'omie_clientes';

    protected $fillable = [
    'empresa',
    'codigo_cliente_omie',
    'codigo_integracao',
    'razao_social',
    'nome_fantasia',
    'cnpj_cpf',
    'email',
    'telefone',
    'cidade',
    'estado',
    'tags',
    'payload',
];


    protected $casts = [
        'tags' => 'array',
        'payload' => 'array',
    ];
}
