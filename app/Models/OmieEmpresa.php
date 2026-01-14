<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OmieEmpresa extends Model
{
    use HasFactory;

    protected $table = 'omie_empresas';

    protected $fillable = [
        'empresa',
        'codigo_empresa',
        'codigo_empresa_integracao',
        'cnpj',
        'razao_social',
        'nome_fantasia',
        'logradouro',
        'endereco_numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'cep',
        'codigo_pais',
        'telefone1',
        'telefone2',
        'email',
        'website',
        'regime_tributario',
        'optante_simples_nacional',
        'gera_nfe',
        'gera_nfse',
        'inativa',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    // ==============================
    // RELACIONAMENTOS
    // ==============================
    
    public function servicos()
{
    return $this->hasMany(OmieServico::class, 'empresa', 'empresa'); 
    // Tabela servicos.empresa = tabela empresas.empresa
}

public function clientes()
{
    return $this->hasMany(OmieCliente::class, 'empresa', 'empresa');
}

public function categorias()
{
    return $this->hasMany(OmieCategoria::class, 'empresa', 'empresa');
}


    // ==============================
    // ESCOPOS
    // ==============================
    
    public function scopeEmpresa($query, $empresaId)
    {
        return $query->where('empresa', $empresaId);
    }

    public function scopeAtivas($query)
    {
        return $query->where('inativa', 'N');
    }
}
