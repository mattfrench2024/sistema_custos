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
    margin-top: .25rem;
    color: var(--text-secondary);
}

/* =========================
   💳 KPI CARDS
========================= */
.kpi-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
    margin-bottom: 2.5rem;
}

.kpi-card {
    background: var(--surface);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-soft);
    padding: 1.75rem;
    position: relative;
    overflow: hidden;
}

.kpi-card::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(
        120deg,
        var(--brand-orange-soft),
        var(--brand-purple-soft)
    );
    opacity: .35;
    z-index: 0;
}

.kpi-card > * {
    position: relative;
    z-index: 1;
}

.kpi-title {
    font-size: .8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .05em;
    color: var(--text-secondary);
}

.kpi-value {
    margin-top: .5rem;
    font-size: 1.6rem;
    font-weight: 700;
    color: var(--text-primary);
}

.kpi-sub {
    margin-top: .25rem;
    font-size: .8rem;
    color: var(--text-secondary);
}

/* =========================
   💰 STATES
========================= */
.text-positive {
    color: #16a34a;
    font-weight: 700;
}

.text-negative {
    color: #dc2626;
    font-weight: 700;
}

.badge-meta {
    display: inline-flex;
    align-items: center;
    padding: .25rem .6rem;
    border-radius: 999px;
    font-size: .7rem;
    font-weight: 600;
    margin-top: .5rem;
    background: #f1f5f9;
    color: var(--text-secondary);
}

/* =========================
   📈 TABLE WRAPPER
========================= */
.fin-table-wrapper {
    background: var(--surface);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-soft);
    overflow: hidden;
}

/* =========================
   📊 TABLE
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

.fin-table tbody td {
    padding: 1rem;
    border-top: 1px solid #f1f5f9;
    font-size: .95rem;
    color: var(--text-primary);
}

.fin-table tbody tr {
    transition: all .2s ease;
}

.fin-table tbody tr:hover {
    background: #fff7ed;
}

/* =========================
   🔘 BUTTONS
========================= */
.btn-back {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    margin-top: 2rem;
    background: #e5e7eb;
    color: #374151;
    padding: .55rem 1.25rem;
    border-radius: 999px;
    font-size: .8rem;
    font-weight: 600;
    transition: all .2s ease;
}

.btn-back:hover {
    background: #d1d5db;
    transform: translateY(-1px);
}

/* =========================
   📱 RESPONSIVE
========================= */
@media (max-width: 768px) {
    .kpi-grid {
        grid-template-columns: 1fr;
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
        <h2>📅 Resumo Financeiro</h2>
        <p>{{ $resumo->data_referencia->format('d/m/Y') }} — visão detalhada do dia</p>
    </div>

    {{-- KPI --}}
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-title">Saldo em Contas</div>
            <div class="kpi-value {{ $resumo->saldo_contas < 0 ? 'text-negative' : 'text-positive' }}">
                R$ {{ number_format($resumo->saldo_contas, 2, ',', '.') }}
            </div>
            <div class="kpi-sub">Disponível hoje</div>
        </div>

        <div class="kpi-card">
            <div class="kpi-title">Limite de Crédito</div>
            <div class="kpi-value">
                R$ {{ number_format($resumo->limite_credito, 2, ',', '.') }}
            </div>
            <div class="kpi-sub">Crédito aprovado</div>
        </div>

        <div class="kpi-card">
            <div class="kpi-title">Indicador Visual</div>
            <div class="kpi-value">
                {{ $resumo->icone }}
            </div>
            <div class="badge-meta">
                {{ $resumo->cor }}
            </div>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="fin-table-wrapper">
        <table class="fin-table">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Pagar</th>
                    <th>Receber</th>
                    <th>Saldo</th>
                </tr>
            </thead>

            <tbody>
                @foreach (data_get($resumo->payload, 'fluxoCaixa', []) as $dia)
                    <tr>
                        <td data-label="Data">
                            {{ $dia['dDia'] }}
                        </td>

                        <td data-label="Pagar" class="text-negative">
                            R$ {{ number_format($dia['vPagar'], 2, ',', '.') }}
                        </td>

                        <td data-label="Receber" class="text-positive">
                            R$ {{ number_format($dia['vReceber'], 2, ',', '.') }}
                        </td>

                        <td data-label="Saldo">
                            R$ {{ number_format($dia['vSaldo'], 2, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- BACK --}}
    <a
        href="{{ route('omie.resumo-financas.index', $empresa) }}"
        class="btn-back"
    >
        ← Voltar para resumo geral
    </a>

</div>
@endsection
