@extends('layouts.app')

@section('content')
<style>
:root {
    --brand-orange: #ff6200ff;
    --brand-orange-soft: #ffedd5;
    --brand-purple: #f97316;
    --brand-purple-soft: #ede9fe;

    --surface: #ffffff;
    --surface-muted: #f9fafb;

    --text-primary: #111827;
    --text-secondary: #6b7280;

    --radius-lg: 1rem;
    --radius-md: 0.75rem;

    --shadow-soft:
        0 1px 2px rgba(0,0,0,.04),
        0 12px 32px rgba(0,0,0,.08);
}

/* ===== Typography ===== */
h1 {
    font-weight: 700;
    letter-spacing: -0.02em;
    color: var(--text-primary);
}

/* ===== Container ===== */
.table-container {
    background: var(--surface);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-soft);
    overflow: hidden;
}

/* ===== Table ===== */
table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

thead th {
    background: var(--surface-muted);
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    font-weight: 600;
    color: var(--text-secondary);
    padding: 0.9rem 1.25rem;
    text-align: left;
    border-bottom: 1px solid #e5e7eb;
}

tbody tr {
    transition: background 0.15s ease;
}

tbody tr:hover {
    background: #fafafa;
}

tbody td {
    padding: 0.9rem 1.25rem;
    font-size: 0.875rem;
    color: var(--text-primary);
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
}

/* ===== Status Badge ===== */
.badge {
    display: inline-flex;
    align-items: center;
    padding: 0.3rem 0.7rem;
    font-size: 0.7rem;
    font-weight: 600;
    border-radius: 9999px;
    letter-spacing: 0.04em;
}

.status-recebido {
    background: #dcfce7;
    color: #166534;
}

.status-pendente {
    background: var(--brand-orange-soft);
    color: #9a3412;
}

.status-atrasado {
    background: #fee2e2;
    color: #991b1b;
}

/* ===== Action ===== */
.action-link {
    color: var(--brand-purple);
    font-weight: 600;
    font-size: 0.8rem;
    transition: color .15s ease, transform .15s ease;
}

.action-link:hover {
    color: var(--brand-orange);
    transform: translateX(2px);
}

/* ===== Pagination ===== */
.pagination {
    padding: 1rem 1.25rem;
    background: var(--surface);
    border-top: 1px solid #e5e7eb;
}

/* ===== Empty State ===== */
.empty-state {
    padding: 3rem 1rem;
    text-align: center;
    color: var(--text-secondary);
    font-size: 0.875rem;
}

/* ===== Mobile ===== */
@media (max-width: 768px) {
    table, thead, tbody, th, td, tr {
        display: block;
    }

    thead {
        display: none;
    }

    tbody tr {
        margin: 1rem;
        border-radius: var(--radius-lg);
        background: var(--surface);
        box-shadow: var(--shadow-soft);
        padding: 0.5rem 0;
    }

    tbody td {
        display: flex;
        justify-content: space-between;
        padding: 0.6rem 1rem;
        border: none;
    }

    tbody td::before {
        content: attr(data-label);
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        color: var(--text-secondary);
        letter-spacing: 0.04em;
    }

    .text-center {
        text-align: right !important;
    }
}
</style>

<div class="max-w-7xl mx-auto px-6 py-8 space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-3xl">
            Contas a Receber
            <span class="text-sm font-medium text-[var(--text-secondary)]">
                — {{ $empresaNome }}
            </span>
        </h1>
    </div>
<form method="GET" class="mb-4 flex flex-wrap gap-2 items-end">
    <div>
        <label class="block text-gray-700 text-xs font-semibold">Mês</label>
        <select name="mes" class="form-select">
            <option value="">Todos</option>
            @for($m=1; $m<=12; $m++)
                <option value="{{ $m }}" @if(request('mes') == $m) selected @endif>
                    {{ str_pad($m,2,'0',STR_PAD_LEFT) }}
                </option>
            @endfor
        </select>
    </div>

    <div>
        <label class="block text-gray-700 text-xs font-semibold">Ano</label>
        <select name="ano" class="form-select">
            <option value="">Todos</option>
            @for($y = now()->year-5; $y <= now()->year+1; $y++)
                <option value="{{ $y }}" @if(request('ano') == $y) selected @endif>{{ $y }}</option>
            @endfor
        </select>
    </div>

    <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded">
        Filtrar
    </button>
</form>

    {{-- Table --}}
    <div class="table-container">

        <table>
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Vencimento</th>
                    <th>Valor</th>
                    <th>Categoria</th>
                    <th>Status</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($receber as $item)
                   

                    <tr>


                        <td data-label="Cliente">
    {{ $item->nome_cliente }}
</td>

                        <td data-label="Vencimento">
                            {{ $item->data_vencimento?->format('d/m/Y') }}
                        </td>

                        <td data-label="Valor">
                            R$ {{ number_format($item->valor_documento, 2, ',', '.') }}
                        </td>
                        <td data-label="Categoria">
    @if($item->categoria)
        {{ $item->categoria->descricao }}
    @else
        <span class="text-gray-400 italic">Não definida</span>
    @endif
</td>


                        <td data-label="Status">
                            <span class="badge {{ $item->statusColor() }}">
                                {{ $item->status_calculado }}
                            </span>

                        </td>

                        <td data-label="Ações" class="text-center">
                            <a
    href="{{ route('omie.receber.show', ['empresa' => $empresa, 'receber' => $item]) }}"
    class="action-link"
>
    Ver →
</a>

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty-state">
                            Nenhuma conta encontrada para este período.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination">
            {{ $receber->links() }}
        </div>

    </div>
</div>
@endsection
