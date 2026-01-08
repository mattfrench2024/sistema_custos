@extends('layouts.app')

@section('content')
<style>
:root{
    /* Brand gradient — mais sóbrio e corporativo */
    --brand-from: #f97316;   /* laranja executivo */
    --brand-to:   #fbbf24;   /* dourado suave */

    /* Roxo institucional (autoridade / jurídico / estratégia) */
    --brand-purple: #6d28d9;

    /* Superfícies */
    --soft-white: rgba(255,255,255,0.96);
    --soft-black: rgba(17,24,39,0.92); /* slate escuro elegante */

    /* Bordas glass refinadas */
    --glass-border: rgba(255,255,255,0.12);

    /* Texto secundário mais legível */
    --muted: #6b7280;
}


body {
    background: linear-gradient(
        180deg,
        #f9fafb 0%,
        #e5e7eb 100%
    );
    color: #111827; /* texto real, não branco falso */
}


.card {
    background: linear-gradient(
        180deg,
        rgba(255,255,255,0.85),
        rgba(249,250,251,0.92)
    );
    border: 1px solid var(--glass-border);
    border-radius: 18px;
    padding: 1.6rem;
    backdrop-filter: blur(20px);
    box-shadow:
        0 10px 25px rgba(0,0,0,0.06),
        inset 0 1px 0 rgba(255,255,255,0.6);
}


.kpi-label {
    font-size: .72rem;
    text-transform: uppercase;
    letter-spacing: .1em;
    color: var(--muted);
}

.kpi-value {
    font-size: 1.85rem;
    font-weight: 700;
    color: #111827;
}


.positive { color: #16a34a; } 
.negative { color: #dc2626; } 


.section-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #111827;
}

.section-subtitle {
    font-size: .85rem;
    color: var(--muted);
}

</style>

<div class="max-w-7xl mx-auto px-6 py-10 space-y-14">

    {{-- HEADER --}}
    <div class="flex justify-between items-end">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">
                Financeiro 
            </h1>
            <p class="text-sm text-gray-400">
                Visão consolidada e auditável — {{ $ano }}
            </p>
        </div>

        <form method="GET" class="flex items-end gap-3">
    <div>
        <label class="text-xs text-muted">Ano</label>
        <input type="number"
               name="ano"
               value="{{ $ano }}"
               class="border rounded px-3 py-2 w-28 text-sm">
    </div>

    <div>
        <label class="text-xs text-muted">Mês</label>
        <select name="mes"
                class="border rounded px-3 py-2 text-sm">
            <option value="">Todos</option>
            @foreach(range(1,12) as $m)
                <option value="{{ $m }}"
                    {{ (int)$mes === $m ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                </option>
            @endforeach
        </select>
    </div>

    <button class="px-4 py-2 rounded bg-gradient-to-r from-[var(--brand-from)] to-[var(--brand-to)] text-white text-sm">
        Aplicar
    </button>
</form>

    </div>

    {{-- KPI EXECUTIVO --}}
    <section class="space-y-3">
        <div>
            <h2 class="section-title">Resumo Executivo</h2>
            <p class="section-subtitle">
                Indicadores financeiros consolidados
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="card">
                <p class="kpi-label">Total a Receber</p>

                <p class="kpi-value positive">
    R$ {{ number_format($kpis['receita'], 2, ',', '.') }}
</p>

            </div>

            <div class="card">
                <p class="kpi-label">Total a Pagar</p>
                <p class="kpi-value negative">
    R$ {{ number_format($kpis['custos'], 2, ',', '.') }}
</p>

            </div>

            <div class="card">
                <p class="kpi-label">Saldo</p>
                <p class="kpi-value {{ $kpis['saldo'] < 0 ? 'negative' : 'positive' }}">
                    R$ {{ number_format($kpis['saldo'], 2, ',', '.') }}
                </p>
            </div>

            <div class="card">
                <p class="kpi-label">Margem (%)</p>
                <p class="kpi-value">
                    {{ $kpis['margem'] }}%
                </p>
            </div>
        </div>
    </section>

    {{-- EVOLUÇÃO MENSAL --}}
    <section class="space-y-3">
        <div>
            <h2 class="section-title">Evolução Mensal</h2>
            <p class="section-subtitle">
                Recebimentos × Pagamentos mês a mês
            </p>
        </div>

        <div class="card">
            <canvas id="mensalChart" height="120"></canvas>
        </div>
    </section>



<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const mensal = @json($mensal);

new Chart(document.getElementById('mensalChart'), {
    type: 'bar',
    data: {
        labels: mensal.map(m => m.mes),
        datasets: [
            {
                label: 'Receber',
                data: mensal.map(m => m.receita),
                backgroundColor: '#22c55e'
            },
            {
                label: 'Pagar',
                data: mensal.map(m => m.custos)
,
                backgroundColor: '#ef4444'
            }
        ]
    },
    options: {
        plugins: { legend: { labels: { color: '#9ca3af' } } },
        scales: {
            x: { ticks: { color: '#9ca3af' } },
            y: { ticks: { color: '#9ca3af' } }
        }
    }
});

new Chart(document.getElementById('dependenciaChart'), {
    type: 'doughnut',
    data: {
labels: @json($concentracaoReceita->pluck('empresa')),
        datasets: [{
data:   @json($concentracaoReceita->pluck('percentual')),
            backgroundColor: [
                '#ff7a18',
                '#ffb347',
                '#7e22cc',
                '#22c55e',
                '#3b82f6'
            ]
        }]
    },
    options: {
        plugins: { legend: { labels: { color: '#9ca3af' } } }
    }
});
</script>
@endsection
