<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OmieOportunidade extends Model
{
    protected $table = 'omie_oportunidades';

    protected $fillable = [
        'codigo_oportunidade',
        'titulo',
        'etapa',
        'status',
        'codigo_cliente',
        'cliente',
        'valor_previsto',
        'data_prevista_fechamento',
        'codigo_usuario_responsavel',
        'usuario_responsavel',
        'data_criacao',
        'data_alteracao',
    ];
}
