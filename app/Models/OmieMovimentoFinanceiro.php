<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OmieMovimentoFinanceiro extends Model
{
    protected $table = 'omie_movimentos_financeiros';

    protected $fillable = [
        'empresa',
        'omie_uid', // ✅ ESSENCIAL
        'codigo_movimento',
        'codigo_lancamento_omie',
        'codigo_titulo',
        'codigo_conta_corrente',
        'tipo_movimento',
        'origem',
        'data_movimento',
        'data_competencia',
        'data_inclusao',
        'valor',
        'categorias',
        'departamentos',
        'info'
    ];

    protected $casts = [
        'categorias'    => 'array',
        'departamentos' => 'array',
        'info'          => 'array',
        'valor'         => 'float',
        'data_movimento'   => 'date',
        'data_competencia' => 'date',
    ];



    // Accessors úteis para seu dashboard
    public function getNaturezaDescricaoAttribute()
    {
        return match($this->tipo_movimento) {
            'R' => 'Receita',
            'P' => 'Despesa',
            default => 'Outro'
        };
    }




    // 🔹 Relações
    public function pagar()
    {
        return $this->belongsTo(
            OmiePagar::class,
            'codigo_lancamento_omie',
            'codigo_lancamento_omie'
        );
    }

    public function receber()
    {
        return $this->belongsTo(
            OmieReceber::class,
            'codigo_lancamento_omie',
            'codigo_lancamento_integracao'
        );
    }

    public function contaCorrente()
    {
        return $this->belongsTo(
            OmieContaCorrente::class,
            'codigo_conta_corrente',
            'omie_cc_id'
        );
    }

    public function categoriasRelacionadas()
    {
        return $this->belongsTo(OmieCategoria::class, 'categorias', 'codigo');
    }


    // Helpers
    public function getGrupo(): ?string
{
    return data_get($this->info, 'detalhes.cGrupo');
}

public function isEntradaGerencial(): bool
{
    return $this->getGrupo() === 'CONTA_A_RECEBER';
}

public function isSaidaGerencial(): bool
{
    return $this->getGrupo() === 'CONTA_A_PAGAR';
}

public function isContaCorrente(): bool
{
    return in_array(
        $this->getGrupo(),
        ['CONTA_CORRENTE_REC', 'CONTA_CORRENTE_PAG']
    );
}


    public function getTipoLabelAttribute(): string
    {
        return match($this->tipo_movimento){
            'R'=>'Receber',
            'P'=>'Pagar',
            'C'=>'Crédito CC',
            'D'=>'Débito CC',
            default=>'Outro',
        };
    }
    public function getGrupoLabelAttribute(): string
{
    $grupo = data_get($this->info, 'detalhes.cGrupo');

    return match ($grupo) {
        'CONTA_A_RECEBER'    => 'Conta a Receber',
        'CONTA_A_PAGAR'      => 'Conta a Pagar',
        'CONTA_CORRENTE_REC' => 'Conta Corrente (Entrada)',
        'CONTA_CORRENTE_PAG' => 'Conta Corrente (Saída)',
        default              => 'Outro',
    };
}

public function scopeEmpresa($query, string $empresa)
{
    return $query->where('empresa', $empresa);
}

public function scopePeriodo($query, $inicio = null, $fim = null)
{
    if ($inicio) {
        $query->whereDate('data_movimento', '>=', $inicio);
    }

    if ($fim) {
        $query->whereDate('data_movimento', '<=', $fim);
    }

    return $query;
}

public function scopePagar($query)
{
    return $query->where('tipo_movimento', 'P');
}

public function scopeReceber($query)
{
    return $query->where('tipo_movimento', 'R');
}
public function getValorFormatadoAttribute(): string
{
    return number_format($this->valor, 2, ',', '.');
}

public function getDataMovimentoFormatadaAttribute(): ?string
{
    return $this->data_movimento?->format('d/m/Y');
}
public function scopeContaAPagarGerencial($query)
{
    return $query->whereJsonContains(
        'info->detalhes->cGrupo',
        'CONTA_A_PAGAR'
    );
}

public function scopeContaAReceberGerencial($query)
{
    return $query->whereJsonContains(
        'info->detalhes->cGrupo',
        'CONTA_A_RECEBER'
    );
}
public function getNomeEnvolvidoAttribute(): string
{
    // 1️⃣ Tenta relação direta via Conta a Pagar (se houver link no banco)
    if ($this->isSaidaGerencial() && $this->pagar) {
        return $this->pagar->fornecedor?->nome_fantasia
            ?: $this->pagar->fornecedor?->razao_social
            ?: 'Fornecedor #' . $this->pagar->codigo_cliente_fornecedor;
    }

    // 2️⃣ Tenta relação direta via Conta a Receber (se houver link no banco)
    if ($this->isEntradaGerencial() && $this->receber) {
        return $this->receber->cliente?->nome_fantasia
            ?: $this->receber->cliente?->razao_social
            ?: 'Cliente #' . $this->receber->codigo_cliente_fornecedor;
    }

    // 3️⃣ BUSCA PELO ID NO JSON (A correção principal)
    // O movimento quase sempre tem o ID do cliente no JSON, mesmo sem link no banco.
    $codClienteOmie = data_get($this->info, 'detalhes.nCodCliente');
    
    if ($codClienteOmie) {
        $cliente = OmieCliente::where('codigo_cliente_omie', $codClienteOmie)
            ->where('empresa', $this->empresa) // Importante para multitenancy
            ->first();

        if ($cliente) {
            return $cliente->nome_fantasia 
                ?: $cliente->razao_social 
                ?: 'Cliente #' . $codClienteOmie;
        }
    }

    // 4️⃣ Fallback via CNPJ/CPF no JSON (Último recurso)
    $cnpjCpf = data_get($this->info, 'detalhes.cCPFCNPJCliente');

    if ($cnpjCpf) {
        // Remove pontuação para garantir o match se o banco estiver limpo
        // Se o seu banco salva com pontuação, pode remover o preg_replace
        $cliente = OmieCliente::where(function($q) use ($cnpjCpf) {
                $q->where('cnpj_cpf', $cnpjCpf)
                  ->orWhere('cnpj_cpf', preg_replace('/[^0-9]/', '', $cnpjCpf));
            })
            ->where('empresa', $this->empresa)
            ->first();

        if ($cliente) {
            return $cliente->nome_fantasia
                ?: $cliente->razao_social
                ?: 'Cliente ' . $cnpjCpf;
        }
        
        // Se não achou cliente no banco, retorna o CNPJ formatado para não ficar vazio
        return $cnpjCpf; 
    }

    return 'Não identificado';
}
}