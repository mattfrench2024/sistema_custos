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

/* Container geral */
.container {
    max-width: 1280px;
    margin: 2rem auto;
    padding: 0 1rem;
    font-family: 'Inter', sans-serif;
    color: var(--text-primary);
}

/* Título */
h1 {
    font-size: 1.875rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: var(--soft-black);
}

/* Alert de saldo */
.alert-info {
    background: linear-gradient(90deg, var(--brand-from), var(--brand-to));
    color: #fff;
    padding: 1rem 1.25rem;
    border-radius: var(--radius-md);
    margin-bottom: 2rem;
    font-weight: 500;
    box-shadow: var(--shadow-soft);
}

/* Tabela refinada */
.table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-soft);
    background: var(--soft-white);
}

.table thead {
    background-color: var(--soft-black);
    color: #fff;
    font-weight: 600;
}

.table th, .table td {
    padding: 0.75rem 1rem;
    text-align: left;
    vertical-align: middle;
    font-size: 0.95rem;
}

.table th.text-end, .table td.text-end {
    text-align: right;
}

/* Linhas com hover */
.table tbody tr {
    transition: background 0.2s ease;
}
.table tbody tr:hover {
    background-color: rgba(247,115,22,0.08);
}

/* Status ativo/inativo */
.status-badge {
    padding: 0.25rem 0.5rem;
    border-radius: var(--radius-md);
    font-weight: 500;
    font-size: 0.85rem;
    display: inline-block;
    text-align: center;
    min-width: 60px;
}

.status-ativa {
    background-color: rgba(34,197,94,0.1);
    color: #22c55e;
}

.status-inativa {
    background-color: rgba(239,68,68,0.1);
    color: #ef4444;
}

/* Saldo positivo/negativo */
.text-success {
    color: #16a34a !important;
    font-weight: 500;
}

.text-danger {
    color: #dc2626 !important;
    font-weight: 500;
}

/* Botão Ver */
.btn-primary {
    background-color: var(--brand-primary);
    color: #fff;
    padding: 0.375rem 0.75rem;
    border-radius: var(--radius-md);
    font-size: 0.85rem;
    font-weight: 500;
    transition: all 0.2s ease;
    display: inline-block;
    text-decoration: none;
    border: none;
}
.btn-primary:hover {
    background: linear-gradient(90deg, var(--brand-from), var(--brand-to));
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.12);
}
</style>

<div class="container">
    <h1>
        Contas Correntes — {{ $empresaLabel }}
    </h1>

    <div class="alert alert-info">
        <strong>Saldo total consolidado:</strong>
        R$ {{ number_format($saldoTotal, 2, ',', '.') }}
    </div>

    <div style="overflow-x:auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>Empresa</th>
                    <th>Descrição</th>
                    <th>Banco</th>
                    <th>Agência</th>
                    <th>Conta</th>
                    <th class="text-end">Saldo Atual</th>
                    <th class="text-end">Limite</th>
                    <th>Status</th>
                    <th width="80">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($contas as $conta)
                <tr>
                    <td>{{ $conta->empresa_nome }}</td>
                    <td>{{ $conta->descricao }}</td>
                    <td>{{ $conta->codigo_banco }}</td>
                    <td>{{ $conta->codigo_agencia ?? '-' }}</td>
                    <td>{{ $conta->numero_conta_corrente ?? '-' }}</td>
                    <td class="text-end {{ $conta->saldo_atual < 0 ? 'text-danger' : 'text-success' }}">
                        R$ {{ number_format($conta->saldo_atual, 2, ',', '.') }}
                    </td>
                    <td class="text-end">
                        R$ {{ number_format($conta->valor_limite, 2, ',', '.') }}
                    </td>
                    <td>
                        <span class="status-badge {{ $conta->inativo === 'N' ? 'status-ativa' : 'status-inativa' }}">
                            {{ $conta->inativo === 'N' ? 'Ativa' : 'Inativa' }}
                        </span>
                    </td>
                    <td>
                        <a
                            href="{{ route('omie.contas-correntes.show', [
                                'empresa' => $empresaSlug,
                                'contaCorrente' => $conta->id
                            ]) }}"
                            class="btn btn-primary"
                        >
                            Ver
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center text-muted">
                        Nenhuma conta encontrada para esta empresa.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
