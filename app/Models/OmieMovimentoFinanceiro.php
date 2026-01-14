<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OmieMovimentoFinanceiro extends Model
{
    protected $table = 'omie_movimentos_financeiros';
    protected $fillable = [
        'empresa', 'codigo_movimento', 'codigo_lancamento_omie', 'codigo_titulo',
        'tipo_movimento', 'origem', 'data_movimento', 'data_competencia',
        'valor', 'codigo_conta_corrente', 'categorias', 'departamentos', 'info',
    ];
    protected $casts = [
        'data_movimento' => 'date',
        'data_competencia' => 'date',
        'valor' => 'decimal:2',
        'categorias' => 'array',
        'departamentos' => 'array',
        'info' => 'array',
    ];

    // 🔹 Relações
    public function contaCorrente()
    {
        return $this->belongsTo(OmieContaCorrente::class, 'codigo_conta_corrente', 'omie_cc_id');
    }

    public function pagar()
    {
        return $this->hasOne(OmiePagar::class, 'codigo_lancamento_omie', 'codigo_lancamento_omie');
    }

    public function receber()
    {
        return $this->hasOne(OmieReceber::class, 'codigo_lancamento_integracao', 'codigo_lancamento_omie');
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
}

