<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OmieContrato extends Model
{
    protected $table = 'omie_contratos';

    protected $fillable = [
        'empresa',
        'nCodCtr',
        'cNumCtr',
        'nCodCli',
        'cCodCateg',
        'nValTotMes',
        'dVigInicial',
        'dVigFinal',
        'cCodSit',
        'itens',
        'payload',
    ];

    protected $casts = [
        'itens'        => 'array',
        'payload'      => 'array',
        'dVigInicial'  => 'date',
        'dVigFinal'    => 'date',
        'nValTotMes'   => 'decimal:2',
    ];

    /* ================= RELACIONAMENTOS ================= */

    // Cliente do contrato
    public function cliente()
    {
        return $this->belongsTo(
            OmieCliente::class,
            'nCodCli',
            'codigo_cliente_omie'
        );
    }

    public function categoria()
    {
        return $this->belongsTo(
            OmieCategoria::class,
            'cCodCateg',
            'codigo'
        );
    }

    public function receber()
    {
        return $this->hasMany(
            OmieReceber::class,
            'codigo_cliente_fornecedor',
            'nCodCli'
        );
    }
}
