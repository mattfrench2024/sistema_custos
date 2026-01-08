@extends('layouts.app')

@section('content')
<style>
:root{
    --brand-from: #f97316;
    --brand-to:   #fbbf24;
    --brand-primary: #f97316;
    --soft-white: rgba(255,255,255,0.96);
    --soft-black: rgba(17,24,39,0.92);
    --glass-border: rgba(255,255,255,0.12);
    --text-primary: #111827;
    --muted: #6b7280;
    --radius-lg: 1rem;
    --radius-md: 0.75rem;
    --shadow-soft:
        0 1px 2px rgba(0,0,0,.04),
        0 12px 32px rgba(0,0,0,.08);
}

/* ===== Layout Base ===== */
.container {
    max-width: 1200px;
    padding-top: 1.5rem;
    padding-bottom: 3rem;
}

h1 {
    font-weight: 700;
    color: var(--text-primary);
    letter-spacing: -0.02em;
}

h5 {
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 1rem;
}

/* ===== Buttons ===== */
.btn-secondary {
    background: transparent;
    border: 1px solid var(--glass-border);
    color: var(--text-primary);
    border-radius: var(--radius-md);
    padding: 0.45rem 0.85rem;
    font-weight: 500;
    transition: all 0.2s ease;
}
.btn-secondary:hover {
    background: var(--soft-black);
    color: #fff;
    transform: translateY(-1px);
}

/* ===== Cards ===== */
.card {
    border: 1px solid var(--glass-border);
    border-radius: var(--radius-lg);
    background: var(--soft-white);
    box-shadow: var(--shadow-soft);
    margin-bottom: 2rem;
}

.card-body {
    padding: 1.5rem;
}

/* ===== Tables ===== */
.table {
    width: 100%;
    margin-bottom: 0;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 0.875rem;
}

.table th {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: var(--muted);
    border-bottom: 1px solid #e5e7eb;
    white-space: nowrap;
    padding: 0.5rem 0.75rem;
}

.table td {
    vertical-align: middle;
    padding: 0.5rem 0.75rem;
    color: var(--text-primary);
}

.table-striped tbody tr:nth-of-type(odd) {
    background-color: #fafafa;
}

.table-hover tbody tr:hover {
    background-color: #fff7ed;
}

/* ===== Status / Badges ===== */
.badge {
    padding: 0.35em 0.6em;
    font-weight: 600;
    border-radius: var(--radius-md);
    font-size: 0.75rem;
    display: inline-block;
    text-align: center;
}

.bg-success {
    background-color: #dcfce7 !important;
    color: #166534 !important;
}

.bg-danger {
    background-color: #fee2e2 !important;
    color: #991b1b !important;
}

/* ===== Financial Values ===== */
.text-end {
    text-align: right;
}

.text-success {
    color: #166534 !important;
    font-weight: 600;
}

.text-danger {
    color: #991b1b !important;
    font-weight: 600;
}

/* ===== Section Separation ===== */
.card + .card {
    margin-top: 2rem;
}

/* ===== Scrollable tables ===== */
.table-responsive {
    overflow-x:auto;
}

/* ===== Responsive ===== */
@media (max-width: 768px) {
    h1 {
        font-size: 1.5rem;
    }
    .card-body {
        padding: 1rem;
    }
    .table th, .table td {
        font-size: 0.8rem;
        padding: 0.4rem 0.5rem;
    }
}
</style>

<div class="container">
    <h1 class="mb-4">Conta Corrente – Detalhes Financeiros</h1>

    <a href="{{ route('omie.contas-correntes.index', ['empresa' => $empresaSlug]) }}"
       class="btn btn-secondary mb-4">
        ← Voltar
    </a>

    {{-- CARD: Informações Básicas --}}
    <div class="card">
        <div class="card-body">
            <h5>Informações da Conta</h5>
            <table class="table table-sm table-striped">
                <tr>
                    <th>Empresa</th>
                    <td>{{ $conta->empresa_nome }}</td>
                </tr>
                <tr>
                    <th>Descrição</th>
                    <td>{{ $conta->descricao }}</td>
                </tr>
                <tr>
                    <th>Banco / Agência / Conta</th>
                    <td>{{ $conta->codigo_banco }} / {{ $conta->codigo_agencia ?? '-' }} / {{ $conta->numero_conta_corrente ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Tipo de Conta</th>
                    <td>{{ $conta->tipo }}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        <span class="badge {{ $conta->inativo === 'N' ? 'bg-success' : 'bg-danger' }}">
                            {{ $conta->inativo === 'N' ? 'Ativa' : 'Inativa' }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>Importado da API</th>
                    <td>{{ $conta->importado_api === 'S' ? 'Sim' : 'Não' }}</td>
                </tr>
                <tr>
                    <th>Última Atualização</th>
                    <td>{{ $conta->data_alt ?? '-' }}</td>
                </tr>
            </table>
        </div>
    </div>

    {{-- CARD: Resumo Financeiro --}}
    <div class="card">
        <div class="card-body">
            <h5>Resumo Financeiro</h5>
            <table class="table table-sm table-bordered">
                <tr>
                    <th>Saldo Inicial</th>
                    <td class="text-end">R$ {{ number_format($conta->saldo_inicial, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>Contas a Receber</th>
                    <td class="text-end">R$ {{ number_format($saldoReceber, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>Contas a Pagar</th>
                    <td class="text-end">R$ {{ number_format($saldoPagar, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>Saldo Consolidado</th>
                    <td class="text-end {{ $saldoConsolidado < 0 ? 'text-danger' : 'text-success' }}">
                        R$ {{ number_format($saldoConsolidado, 2, ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <th>Limite da Conta</th>
                    <td class="text-end">R$ {{ number_format($conta->valor_limite, 2, ',', '.') }}</td>
                </tr>
            </table>
        </div>
    </div>

    {{-- CARD: Contas a Receber --}}
    <div class="card">
        <div class="card-body">
            <h5>Contas a Receber ({{ $conta->receber->count() }})</h5>
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Data Vencimento</th>
                            <th class="text-end">Valor</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($conta->receber as $rec)
                        <tr>
                            <td>{{ optional($rec->cliente)->razao_social ?? '-' }}</td>
                            <td>{{ $rec->data_vencimento }}</td>
                            <td class="text-end">R$ {{ number_format($rec->valor_documento, 2, ',', '.') }}</td>
                            <td>{{ ucfirst($rec->status) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">Nenhum título a receber</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- CARD: Contas a Pagar --}}
    <div class="card">
        <div class="card-body">
            <h5>Contas a Pagar ({{ $conta->pagar->count() }})</h5>
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th>Fornecedor</th>
                            <th>Data Vencimento</th>
                            <th class="text-end">Valor</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($conta->pagar as $pay)
                        <tr>
                            <td>{{ optional($pay->fornecedor)->razao_social ?? '-' }}</td>
                            <td>{{ $pay->data_vencimento }}</td>
                            <td class="text-end">R$ {{ number_format($pay->valor_documento, 2, ',', '.') }}</td>
                            <td>{{ ucfirst($pay->status_titulo ?? 'pendente') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">Nenhum título a pagar</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
