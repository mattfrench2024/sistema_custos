<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\OmieCliente;

class OmieReceber extends Model
{
    public function getStatusCalculadoAttribute(): string
{
    $statusOmie = strtolower($this->status ?? 'pendente');

    // Status finais
    if (in_array($statusOmie, ['recebido', 'cancelado'])) {
        return ucfirst($statusOmie);
    }

    if (! $this->data_vencimento) {
        return 'Pendente';
    }

    if ($this->data_vencimento->isToday()) {
        return 'Vence hoje';
    }

    if ($this->data_vencimento->isPast()) {
        return 'Vencido';
    }

    return 'A vencer';
}
    protected $table = 'omie_receber';

    protected $guarded = ['id'];

 protected $fillable = [
        'empresa',
        'codigo_lancamento_integracao',
        'id_conta_corrente',
        'valor_documento',
        'status',
        'data_vencimento',
    ];
    protected $casts = [
        'payload'         => 'array',
        'retorno_omie'    => 'array',
        'data_vencimento' => 'date',
        'data_previsao'   => 'date',
    ];

    /**
     * Route Model Binding
     */
    public function getRouteKeyName(): string
    {
        return 'codigo_lancamento_integracao';
    }

    /* =====================================================
     | RELACIONAMENTOS
     ===================================================== */
    public function movimentoFinanceiro()
    {
        return $this->belongsTo(OmieMovimentoFinanceiro::class, 'codigo_lancamento_integracao', 'codigo_lancamento_omie');
    }
    public function cliente()
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

    public function getNomeClienteAttribute(): string
    {
        if (! $this->cliente) {
            return 'Cliente #' . $this->codigo_cliente_fornecedor;
        }

        return $this->cliente->nome_fantasia
            ?: $this->cliente->razao_social
            ?: 'Cliente #' . $this->codigo_cliente_fornecedor;
    }

    /* =====================================================
     | UX
     ===================================================== */

    public function statusColor(): string
{
    return match ($this->status_calculado) {
        'Recebido'   => 'bg-green-100 text-green-700',
        'Vencido'    => 'bg-red-100 text-red-700',
        'Vence hoje' => 'bg-orange-100 text-orange-700',
        'A vencer'   => 'bg-yellow-100 text-yellow-700',
        'Cancelado'  => 'bg-gray-200 text-gray-700',
        default      => 'bg-gray-100 text-gray-700',
    };
}

}
