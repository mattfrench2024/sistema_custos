<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\OmieCliente;
use App\Models\OmieTipoDocumento;

class OmiePagar extends Model
{
    protected $table = 'omie_pagar';

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
        'categorias'       => 'array',
        'distribuicao'     => 'array',
        'info'             => 'array',
        'data_emissao'     => 'date',
        'data_vencimento'  => 'date',
    ];

    /**
     * Route Model Binding
     */
    public function getRouteKeyName(): string
    {
        return 'codigo_lancamento_omie';
    }
public function movimentoFinanceiro()
    {
        return $this->belongsTo(OmieMovimentoFinanceiro::class, 'codigo_lancamento_omie', 'codigo_lancamento_omie');
    }
    /* =====================================================
     | RELACIONAMENTOS
     ===================================================== */
     public function contaCorrente()
    {
        return $this->belongsTo(OmieContaCorrente::class, 'id_conta_corrente', 'omie_cc_id');
    }

    public function tipoDocumento()
    {
        return $this->belongsTo(
            OmieTipoDocumento::class,
            'codigo_tipo_documento',
            'codigo'
        );
    }

    public function fornecedor()
{
    return $this->belongsTo(
        OmieCliente::class,
        'codigo_cliente_fornecedor',
        'codigo_cliente_omie'
    );
}


    /* =====================================================
     | ACCESSORS
     ===================================================== */
public function getStatusCalculadoAttribute(): string
{
    if (in_array(strtoupper($this->status_titulo), ['PAGO', 'CANCELADO'])) {
        return strtoupper($this->status_titulo);
    }

    if ($this->data_vencimento && $this->data_vencimento->isPast()) {
        return 'VENCIDO';
    }

    return 'A VENCER';
}
    /**
     * Nome do fornecedor (mesma regra do Financeiro Analítico)
     */
    public function getNomeFornecedorAttribute(): string
    {
        if (! $this->fornecedor) {
            return 'Fornecedor #' . $this->codigo_cliente_fornecedor;
        }

        return $this->fornecedor->nome_fantasia
            ?: $this->fornecedor->razao_social
            ?: 'Fornecedor #' . $this->codigo_cliente_fornecedor;
    }
}
