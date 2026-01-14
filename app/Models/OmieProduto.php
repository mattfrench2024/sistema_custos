<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OmieProduto extends Model
{
    protected $table = 'omie_produtos';

    protected $fillable = [
        'empresa',
        'codigo_produto',
        'codigo_produto_integracao',
        'codigo',
        'descricao',
        'unidade',
        'ncm',
        'tipo',
        'importado_api',
        'caracteristicas',
        'componentes_kit',
        'imagens',
        'dados_ibpt',
        'info',
        'payload',
    ];

    protected $casts = [
        'caracteristicas' => 'array',
        'componentes_kit' => 'array',
        'imagens' => 'array',
        'dados_ibpt' => 'array',
        'info' => 'array',
        'payload' => 'array',
    ];
}
