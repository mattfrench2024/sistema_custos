<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OmieReceber extends Model
{
    protected $table = 'omie_receber';

    protected $guarded = ['id'];

    protected $casts = [
        'payload' => 'array',
        'retorno_omie' => 'array',
        'data_vencimento' => 'date',
        'data_previsao' => 'date',
    ];

    // Define que a chave de rota é codigo_lancamento_omie
    public function getRouteKeyName()
    {
        return 'codigo_lancamento_integracao';
    }

    // Status color para UX
    public function statusColor(): string
    {
        return match(strtolower($this->status)) {
            'recebido' => 'bg-green-100 text-green-700',
            'pendente' => 'bg-yellow-100 text-yellow-700',
            'atrasado' => 'bg-red-100 text-red-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }
    
}
