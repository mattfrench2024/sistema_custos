@extends('layouts.app')

@section('content')

<style>
/* =========================
   🌈 DESIGN SYSTEM
========================= */
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

/* =========================
   🧱 BASE
========================= */
body {
    background: linear-gradient(180deg, #fff 0%, #f9fafb 100%);
}

.container {
    max-width: 1200px;
}

/* =========================
   🧠 HEADER
========================= */
.fin-header {
    background: var(--surface);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-soft);
    padding: 2rem;
    margin-bottom: 2rem;
}

.fin-header h2 {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--text-primary);
}

.fin-header p {
    color: var(--text-secondary);
    margin-top: .25rem;
}

/* =========================
   📊 TABLE WRAPPER
========================= */
.fin-table-wrapper {
    background: var(--surface);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-soft);
    overflow: hidden;
}

/* =========================
   📋 TABLE
========================= */
.fin-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

.fin-table thead {
    background: linear-gradient(
        90deg,
        var(--brand-orange-soft),
        var(--brand-purple-soft)
    );
}

.fin-table thead th {
    padding: 1rem;
    font-size: .75rem;
    text-transform: uppercase;
    letter-spacing: .05em;
    color: var(--text-secondary);
    font-weight: 600;
}

.fin-table tbody tr {
    transition: all .2s ease;
}

.fin-table tbody tr:hover {
    background: #fff7ed;
}

.fin-table tbody td {
    padding: 1rem;
    vertical-align: middle;
    font-size: .95rem;
    color: var(--text-primary);
    border-top: 1px solid #f1f5f9;
}

/* =========================
   💰 FINANCIAL STATES
========================= */
.text-positive {
    color: #16a34a;
    font-weight: 600;
}

.text-negative {
    color: #dc2626;
    font-weight: 600;
}

.text-muted {
    color: var(--text-secondary);
    font-size: .85rem;
}

/* =========================
   🔘 BUTTON
========================= */
.btn-view {
    background: linear-gradient(
        135deg,
        var(--brand-orange),
        var(--brand-purple)
    );
    color: #fff;
    padding: .45rem 1rem;
    font-size: .75rem;
    font-weight: 600;
    border-radius: 999px;
    box-shadow: 0 8px 20px rgba(249,115,22,.25);
    transition: all .2s ease;
}

.btn-view:hover {
    transform: translateY(-1px);
    box-shadow: 0 12px 28px rgba(249,115,22,.35);
    color: #fff;
}

/* =========================
   📎 BADGES
========================= */
.badge {
    display: inline-flex;
    align-items: center;
    padding: .25rem .6rem;
    border-radius: 999px;
    font-size: .7rem;
    font-weight: 600;
}

.badge-receber {
    background: #ecfdf5;
    color: #16a34a;
}

.badge-pagar {
    background: #fef2f2;
    color: #dc2626;
}

/* =========================
   📄 PAGINATION
========================= */
.pagination {
    margin-top: 2rem;
    display: flex;
    justify-content: center;
}

.pagination .page-link {
    border-radius: 999px !important;
    margin: 0 .15rem;
    border: none;
    color: var(--text-secondary);
}

.pagination .active .page-link {
    background: linear-gradient(
        135deg,
        var(--brand-orange),
        var(--brand-purple)
    );
    color: #fff;
    box-shadow: var(--shadow-soft);
}

/* =========================
   📱 RESPONSIVE
========================= */
@media (max-width: 768px) {
    .fin-header {
        padding: 1.5rem;
    }

    .fin-table thead {
        display: none;
    }

    .fin-table tbody tr {
        display: block;
        padding: 1rem;
    }

    .fin-table tbody td {
        display: flex;
        justify-content: space-between;
        border: none;
        padding: .5rem 0;
    }

    .fin-table tbody td::before {
        content: attr(data-label);
        font-weight: 600;
        color: var(--text-secondary);
    }
}
</style>

<div class="container mx-auto px-4">

    {{-- HEADER --}}
    <div class="fin-header">
        <h2>📊 Resumo Financeiro</h2>
        <p>{{ $empresaNome }} — visão consolidada diária</p>
    </div>

    {{-- TABLE --}}
    <div class="fin-table-wrapper">
        <table class="fin-table">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Saldo</th>
                    <th>Limite</th>
                    <th>Receber</th>
                    <th>Pagar</th>
                    <th></th>
                </tr>
            </thead>

            <tbody>
                @foreach ($resumos as $r)
                    <tr>
                        <td data-label="Data">
                            {{ $r->data_referencia->format('d/m/Y') }}
                        </td>

                        <td data-label="Saldo"
                            class="{{ $r->saldo_contas < 0 ? 'text-negative' : 'text-positive' }}">
                            R$ {{ number_format($r->saldo_contas, 2, ',', '.') }}
                        </td>

                        <td data-label="Limite">
                            R$ {{ number_format($r->limite_credito, 2, ',', '.') }}
                        </td>

                        <td data-label="Receber">
                            <span class="badge badge-receber">
                                {{ $r->qtd_receber }} títulos
                            </span>
                            <div class="text-muted">
                                R$ {{ number_format($r->total_receber, 2, ',', '.') }}
                            </div>
                        </td>

                        <td data-label="Pagar">
                            <span class="badge badge-pagar">
                                {{ $r->qtd_pagar }} títulos
                            </span>
                            <div class="text-muted">
                                R$ {{ number_format($r->total_pagar, 2, ',', '.') }}
                            </div>
                        </td>

                        <td>
                            <a
                                href="{{ route('omie.resumo-financas.show', [$empresa, $r]) }}"
                                class="btn-view"
                            >
                                Ver detalhes
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- PAGINATION --}}
    {{ $resumos->links() }}

</div>
@endsection
