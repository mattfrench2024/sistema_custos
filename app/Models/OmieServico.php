<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OmieServico extends Model
{
    protected $table = 'omie_servicos';

    protected $fillable = [
        'empresa',
        'codigo_servico',
        'codigo_integracao',
        'codigo',
        'descricao',
        'preco_unitario',
        'codigo_categoria',
        'importado_api',
        'inativo',
        'cabecalho',
        'descricao_completa',
        'impostos',
        'info',
        'produtos_utilizados',
        'payload',
    ];

    protected $casts = [
        'cabecalho'           => 'array',
        'descricao_completa'  => 'array',
        'impostos'            => 'array',
        'info'                => 'array',
        'produtos_utilizados' => 'array',
        'payload'             => 'array',
        'importado_api'       => 'boolean',
        'inativo'             => 'boolean',
        'preco_unitario'      => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONAMENTOS
    |--------------------------------------------------------------------------
    */

    public function categoria()
{
    return $this->belongsTo(
        OmieCategoria::class,
        'codigo_categoria',
        'codigo'
    );
}


    /*
    |--------------------------------------------------------------------------
    | SCOPES ÚTEIS
    |--------------------------------------------------------------------------
    */

    public function scopeAtivos($query)
    {
        return $query->where('inativo', false);
    }

    public function scopeEmpresa($query, string $empresa)
    {
        return $query->where('empresa', $empresa);
    }
    public function getValorTotalAttribute(): float
{
    // 1️⃣ Preço unitário explícito
    if (!empty($this->preco_unitario) && $this->preco_unitario > 0) {
        return (float) $this->preco_unitario;
    }

    // 2️⃣ Texto da descrição completa
    $descricao = $this->descricao_completa['cDescrCompleta'] ?? '';

    if (!$descricao) {
        return 0.0;
    }

    /**
     * Captura QUALQUER valor monetário no formato:
     * R$ 1.234,56
     * R$43.713,30
     * R$ 43.713,30 (NBSP)
     */
    if (preg_match(
        '/R\$\s*([\d\.]+,\d{2})/u',
        $descricao,
        $matches
    )) {
        return (float) str_replace(
            ['.', ','],
            ['', '.'],
            $matches[1]
        );
    }

    return 0.0;
}



}
