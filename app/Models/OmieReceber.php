<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\OmieCliente;
use App\Models\OmieCategoria;

class OmieReceber extends Model
{
    protected $table = 'omie_receber';

    protected $guarded = ['id'];

    protected $fillable = [
        'empresa',
        'codigo_lancamento_integracao',
        'codigo_cliente_fornecedor',
        'codigo_categoria',
        'id_conta_corrente',
        'valor_documento',
        'status',
        'data_vencimento',
        'data_previsao',
        'payload',           // ← agora usado para guardar resposta completa da Omie
    ];

    protected $casts = [
        'payload'         => 'array',
        'retorno_omie'    => 'array',
        'data_vencimento' => 'date',
        'data_previsao'   => 'date',
    ];

    public const STATUS = [
    'aberto'       => 'Em Aberto',          // comum na Omie para títulos não baixados
    'pendente'     => 'Pendente',
    'a_vencer'     => 'A Vencer',
    'vence_hoje'   => 'Vence Hoje',
    'atrasado'     => 'Atrasado',
    'parcial'      => 'Pagamento Parcial',   // se você salva 'parcial'
    'recebido'     => 'Recebido',
    'cancelado'    => 'Cancelado',
];
    // ────────────────────────────────────────────────
    // Accessor principal de status (UX / front)
    // ────────────────────────────────────────────────
    public function getStatusCalculadoAttribute(): string
{
    // 1. Prioridade máxima: usar o status salvo no banco (vem da Omie via import)
    if ($this->status && isset(self::STATUS[$this->status])) {
        return self::STATUS[$this->status];
    }

    // 2. Fallback apenas se o status não estiver mapeado ou for vazio
    // (quase nunca deve chegar aqui se o import estiver correto)

    // Segurança: se já recebeu tudo, força como Recebido
    if ($this->total_recebido >= $this->valor_documento && $this->valor_documento > 0) {
        return 'Recebido';
    }

    if (!$this->data_vencimento) {
        return 'Pendente';
    }

    if ($this->data_vencimento->isToday()) {
        return 'Vence Hoje';
    }

    if ($this->data_vencimento->isPast()) {
        return 'Atrasado';
    }

    return 'A Vencer';
}

    public function getTotalRecebidoAttribute(): float
    {
        return $this->movimentosFinanceiros()
            ->where('tipo_movimento', 'C')
            ->sum('valor') ?? 0;
    }

    public function getSaldoAbertoAttribute(): float
    {
        return max($this->valor_documento - $this->total_recebido, 0);
    }

    public function statusColor(): string
{
    return match ($this->status_calculado) {
        'Recebido'       => 'bg-green-100 text-green-700',
        'Atrasado'       => 'bg-red-100 text-red-700',
        'Vence hoje'     => 'bg-orange-100 text-orange-700',
        'A vencer'       => 'bg-yellow-100 text-yellow-700',
        'Cancelado'      => 'bg-gray-200 text-gray-700',
        'Pagamento Parcial' => 'bg-blue-100 text-blue-700',
        default          => 'bg-gray-100 text-gray-700',
    };
}

    public function podeEditarStatus(): bool
    {
        return ! in_array($this->status, ['recebido', 'cancelado']);
    }

    // Relacionamentos (mantidos iguais)
    public function movimentoFinanceiro()
    {
        return $this->belongsTo(OmieMovimentoFinanceiro::class, 'codigo_lancamento_integracao', 'codigo_lancamento_omie');
    }

    public function movimentosFinanceiros()
    {
        return $this->hasMany(
            OmieMovimentoFinanceiro::class,
            'codigo_lancamento_omie',
            'codigo_lancamento_integracao'
        );
    }

    public function cliente()
    {
        return $this->belongsTo(
            OmieCliente::class,
            'codigo_cliente_fornecedor',
            'codigo_cliente_omie'
        );
    }

    public function categoria()
    {
        return $this->belongsTo(OmieCategoria::class, 'codigo_categoria', 'codigo');
    }

    public function getNomeClienteAttribute(): string
    {
        if (!$this->cliente) {
            return 'Cliente #' . $this->codigo_cliente_fornecedor;
        }

        return $this->cliente->nome_fantasia
            ?: $this->cliente->razao_social
            ?: 'Cliente #' . $this->codigo_cliente_fornecedor;
    }

    protected static function booted()
    {
        static::saving(function ($model) {
            if (empty($model->codigo_lancamento_integracao)) {
                throw new \RuntimeException('codigo_lancamento_integracao não pode ser vazio');
            }
        });
    }

    // Route Model Binding
    public function getRouteKeyName(): string
    {
        return 'id';
    }
}