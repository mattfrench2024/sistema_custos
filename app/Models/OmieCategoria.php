<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OmieCategoria extends Model
{
    protected $table = 'omie_categorias';

    protected $guarded = ['id'];

    protected $casts = [
        'payload' => 'array',
        'conta_receita' => 'boolean',
        'conta_despesa' => 'boolean',
        'totalizadora' => 'boolean',
        'transferencia' => 'boolean',
        'conta_inativa' => 'boolean',
        'nao_exibir' => 'boolean',
        'definida_pelo_usuario' => 'boolean',
        'dre_nivel' => 'integer',
        'dre_totaliza' => 'boolean',
        'dre_nao_exibir' => 'boolean',
    ];

    /* =====================================================
     | RELACIONAMENTOS ESTRUTURAIS
     ===================================================== */

    public function superior()
    {
        return $this->belongsTo(self::class, 'categoria_superior', 'codigo')
            ->where('empresa', $this->empresa);
    }

    public function filhas()
    {
        return $this->hasMany(self::class, 'categoria_superior', 'codigo')
            ->where('empresa', $this->empresa);
    }

    /* =====================================================
     | RELACIONAMENTOS FINANCEIROS (FATOS)
     ===================================================== */

    public function pagar(): HasMany
    {
        return $this->hasMany(OmiePagar::class, 'codigo_categoria', 'codigo')
            ->where('empresa', $this->empresa);
    }

    public function receber(): HasMany
    {
        return $this->hasMany(OmieReceber::class, 'codigo_categoria', 'codigo')
            ->where('empresa', $this->empresa);
    }

    /* =====================================================
     | HELPERS EXECUTIVOS
     ===================================================== */

    public function tipo(): string
    {
        return match (true) {
            $this->conta_receita => 'Receita',
            $this->conta_despesa => 'Despesa',
            $this->transferencia => 'Transferência',
            default => 'Outro',
        };
    }

    public function status(): string
    {
        return $this->conta_inativa ? 'Inativa' : 'Ativa';
    }

    /* =====================================================
     | MÉTRICAS FINANCEIRAS (SEGURAS)
     ===================================================== */

    public function totalReceitas(): float
    {
        return (float) $this->receber()->sum('valor_documento');
    }

    public function totalDespesas(): float
    {
        return (float) $this->pagar()->sum('valor_documento');
    }

    public function saldoFinanceiro(): float
    {
        return $this->totalReceitas() - $this->totalDespesas();
    }

    public function possuiMovimentacao(): bool
    {
        return $this->pagar()->exists() || $this->receber()->exists();
    }
    public function servicos()
{
    return $this->hasMany(
        OmieServico::class,
        'codigo_categoria',
        'codigo'
    )->whereColumn(
        'omie_servicos.empresa',
        'omie_categorias.empresa'
    );
}

}
