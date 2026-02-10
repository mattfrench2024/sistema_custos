<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OmieNotaFiscal extends Model
{
    protected $table = 'omie_notas_fiscais';

    protected $fillable = [
        'empresa',
        'numero',
        'tipo',
        'valor_total',
        'data_emissao',
        'payload',
        'possui_pdf',
        'pdf_path',
    ];

    protected $casts = [
        'payload'       => 'array',
        'data_emissao'  => 'date',
        'possui_pdf'    => 'boolean',
    ];


    public function receber()
{
    return $this->hasMany(
        OmieReceber::class,
        'codigo_cliente_fornecedor',
        'payload->Cabecalho->nCodigoCliente'
    )->whereColumn('omie_receber.empresa', 'omie_notas_fiscais.empresa');
}
public function pagar()
{
    return $this->hasMany(
        OmiePagar::class,
        'codigo_cliente_fornecedor',
        'payload->Cabecalho->nCodigoFornecedor'
    )->whereColumn('omie_pagar.empresa', 'omie_notas_fiscais.empresa');
}

}