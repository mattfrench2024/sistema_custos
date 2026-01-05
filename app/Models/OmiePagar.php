<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OmiePagar extends Model
{
    protected $table = 'omie_pagar';

    // ✅ CHAVE DE BINDING
    public function getRouteKeyName()
    {
        return 'codigo_lancamento_omie';
    }

    protected $fillable = [
        'empresa',
        'codigo_cliente_fornecedor',
        'codigo_lancamento_omie',
        'codigo_categoria',
        'codigo_tipo_documento',
        'status_titulo',
        'data_emissao',
        'data_vencimento',
        'valor_documento',
        'categorias',
        'distribuicao',
        'info',
        'id_conta_corrente',
    ];

    protected $casts = [
        'categorias' => 'array',
        'distribuicao' => 'array',
        'info' => 'array',
        'data_emissao' => 'date',
        'data_vencimento' => 'date',
    ];
}
