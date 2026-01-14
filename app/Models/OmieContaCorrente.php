<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OmieContaCorrente extends Model
{
    protected $table = 'omie_contas_correntes';

    protected $fillable = [
        'empresa_codigo',
        'empresa_nome',
        'omie_cc_id',
        'codigo_interno',
        'tipo_conta',
        'codigo_banco',
        'codigo_agencia',
        'numero_conta_corrente',
        'descricao',
        'tipo',
        'saldo_inicial',
        'saldo_atual',
        'valor_limite',
        'inativo',
        'importado_api',
        'data_inc',
        'data_alt',
    ];

    protected $casts = [
        'saldo_inicial' => 'decimal:2',
        'saldo_atual'   => 'decimal:2',
        'valor_limite'  => 'decimal:2',
    ];

    /* ===== Scopes (muito importantes) ===== */

    public function scopeAtivas($query)
    {
        return $query->where('inativo', 'N');
    }

    public function scopeEmpresa($query, $codigo)
    {
        return $query->where('empresa_codigo', $codigo);
    }
    // app/Models/OmieContaCorrente.php

public function pagar()
{
    return $this->hasMany(OmiePagar::class, 'id_conta_corrente', 'omie_cc_id');
}

public function receber()
{
    return $this->hasMany(OmieReceber::class, 'id_conta_corrente', 'omie_cc_id');
}
 public function lancamentos()
    {
        return $this->hasMany(OmieContaCorrenteLancamento::class, 'omie_cc_id', 'omie_cc_id');
    }

    public function movimentosFinanceiros()
    {
        return $this->hasMany(OmieMovimentoFinanceiro::class, 'codigo_conta_corrente', 'omie_cc_id');
    }
}
