@extends('layouts.app')

@section('title', 'Categoria Contábil • ' . $categoria->descricao)

@section('content')

<style>
:root{
  --brand-from: #F9821A;
  --brand-to: #FC940D;
  --glass-bg: rgba(255,255,255,0.6);
  --muted: #6B7280;
  --card-radius: 1rem;
  --glass-border: rgba(0,0,0,0.04);
}
.card {
    background: var(--glass-bg);
    border: 1px solid var(--glass-border);
    border-radius: var(--card-radius);
}
.badge {
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
}
.badge-ok { background: #ECFDF5; color: #065F46; }
.badge-warn { background: #FFF7ED; color: #9A3412; }
.badge-risk { background: #FEF2F2; color: #991B1B; }
.kpi {
    font-size: 1.5rem;
    font-weight: 600;
}
.kpi-label {
    font-size: 0.75rem;
    color: var(--muted);
    text-transform: uppercase;
}
</style>

<div class="max-w-7xl mx-auto px-6 py-10 space-y-10">

    {{-- HEADER --}}
    <div>
        <a href="{{ route('omie.categorias.index', $empresa) }}"
           class="text-sm text-[var(--brand-from)] hover:underline">
            ← Voltar para categorias
        </a>

        <h1 class="mt-2 text-3xl font-semibold">
            {{ $categoria->descricao }}
        </h1>

        <p class="text-sm text-[var(--muted)]">
            {{ $empresaLabel }} • Código {{ $categoria->codigo }}
        </p>
    </div>

    {{-- RESUMO EXECUTIVO --}}
    <div class="card p-6">
        <h2 class="text-lg font-semibold mb-6">Resumo Executivo</h2>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <div class="kpi-label">Total Receitas</div>
                <div class="kpi text-green-700">
                    R$ {{ number_format($financeiro['total_receitas'], 2, ',', '.') }}
                </div>
            </div>

            <div>
                <div class="kpi-label">Total Despesas</div>
                <div class="kpi text-red-700">
                    R$ {{ number_format($financeiro['total_despesas'], 2, ',', '.') }}
                </div>
            </div>

            <div>
                <div class="kpi-label">Saldo Líquido</div>
                <div class="kpi {{ $financeiro['saldo'] >= 0 ? 'text-green-700' : 'text-red-700' }}">
                    R$ {{ number_format($financeiro['saldo'], 2, ',', '.') }}
                </div>
            </div>

            <div>
                <div class="kpi-label">Movimentação</div>
                <div class="mt-2">
                    @if($financeiro['movimentada'])
                        <span class="badge badge-ok">Com movimentação</span>
                    @else
                        <span class="badge badge-warn">Sem movimentação</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ALERTAS DE COMPLIANCE --}}
    @php
        $riscos = [];

        if ($categoria->conta_inativa && $financeiro['movimentada']) {
            $riscos[] = 'Categoria inativa possui movimentação financeira registrada.';
        }

        if ($categoria->conta_receita && $financeiro['total_despesas'] > 0) {
            $riscos[] = 'Categoria classificada como Receita possui lançamentos de despesa.';
        }

        if ($categoria->conta_despesa && $financeiro['total_receitas'] > 0) {
            $riscos[] = 'Categoria classificada como Despesa possui lançamentos de receita.';
        }
    @endphp

    @if(count($riscos))
        <div class="card p-6 border-red-200 bg-red-50">
            <h2 class="text-lg font-semibold text-red-700 mb-4">
                Indicadores de Risco & Compliance
            </h2>

            <ul class="list-disc pl-6 space-y-2 text-sm text-red-700">
                @foreach($riscos as $risco)
                    <li>{{ $risco }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- DADOS CONTÁBEIS --}}
    <div class="card p-6">
        <h2 class="text-lg font-semibold mb-6">Vínculos Contábeis & Estrutura</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">
            <div>
                <div class="text-[var(--muted)]">Tipo</div>
                <div class="font-medium">{{ $categoria->tipo() }}</div>
            </div>

            <div>
                <div class="text-[var(--muted)]">Status Operacional</div>
                <div class="font-medium">{{ $categoria->status() }}</div>
            </div>

            <div>
                <div class="text-[var(--muted)]">Categoria Superior</div>
                <div class="font-medium">
                    {{ $categoria->superior?->descricao ?? '—' }}
                </div>
            </div>
        </div>
    </div>

    {{-- IMPACTO NO DRE --}}
    <div class="card p-6">
        <h2 class="text-lg font-semibold mb-6">Impacto no DRE</h2>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 text-sm">
            <div>
                <div class="text-[var(--muted)]">Código DRE</div>
                <div class="font-medium">{{ $categoria->dre_codigo ?: '—' }}</div>
            </div>

            <div>
                <div class="text-[var(--muted)]">Descrição</div>
                <div class="font-medium">{{ $categoria->dre_descricao ?: '—' }}</div>
            </div>

            <div>
                <div class="text-[var(--muted)]">Nível</div>
                <div class="font-medium">{{ $categoria->dre_nivel ?? '—' }}</div>
            </div>

            <div>
                <div class="text-[var(--muted)]">Sinal</div>
                <div class="font-medium">{{ $categoria->dre_sinal ?? '—' }}</div>
            </div>
        </div>
    </div>

    {{-- ORIGEM DOS DADOS --}}
    <div class="card p-6 text-sm text-[var(--muted)]">
        <h2 class="text-lg font-semibold mb-4">Origem e Rastreabilidade dos Dados</h2>
        <p>
            As informações apresentadas nesta tela são consolidadas a partir das integrações
            financeiras com a Omie, envolvendo contas a pagar, contas a receber e cadastros
            mestres. Os valores refletem o histórico completo disponível no sistema e estão
            sujeitos às regras de negócio vigentes no momento do lançamento.
        </p>
    </div>

</div>
@endsection
