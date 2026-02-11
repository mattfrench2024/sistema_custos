@extends('layouts.app')

@section('title', 'Financeiro Consolidado')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endpush

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<style>
    :root {
        /* Cores claras */
        --bg-primary: #f8fafc;
        --bg-card: #ffffff;
        --text-primary: #1e293b;
        --text-secondary: #64748b;
        --text-muted: #94a3b8;
        --accent: #f97316;
        --accent-hover: #ea580c;
        --success: #10b981;
        --success-light: #d1fae5;
        --danger: #ef4444;
        --danger-light: #fee2e2;
        --warning: #f59e0b;
        --info: #3b82f6;
        --purple: #8b5cf6;
        --border: rgba(226, 232, 240, 0.8);
        --shadow-sm: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --shadow-md: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 20px 40px -10px rgba(0, 0, 0, 0.15);
        --radius: 1.25rem;
        --transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        --glass-bg: rgba(255, 255, 255, 0.88);
        --glass-border: rgba(255, 255, 255, 0.3);
    }

    .dark {
        /* Cores escuras */
        --bg-primary: #0f172a;
        --bg-card: #1e293b;
        --text-primary: #f1f5f9;
        --text-secondary: #cbd5e1;
        --text-muted: #64748b;
        --accent: #fb923c;
        --accent-hover: #f97316;
        --success: #34d399;
        --danger: #f87171;
        --border: rgba(148, 163, 184, 0.3);
        --glass-bg: rgba(30, 41, 59, 0.75);
        --glass-border: rgba(148, 163, 184, 0.2);
        --shadow-lg: 0 20px 40px -10px rgba(0, 0, 0, 0.4);
    }

    body {
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
        min-height: 100vh;
        transition: var(--transition);
        color: var(--text-primary);
    }

    .dark body {
        background: linear-gradient(135deg, #020617 0%, #1e293b 100%);
    }

    .glass-card {
        background: var(--glass-bg);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        box-shadow: var(--shadow-lg);
        transition: var(--transition);
        position: relative;
        overflow: hidden;
    }

    .glass-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(90deg, var(--accent), var(--warning));
        border-radius: var(--radius) var(--radius) 0 0;
    }

    .glass-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 35px 70px -15px rgba(0, 0, 0, 0.25);
    }

    .kpi-card {
        padding: 2rem;
        text-align: center;
        position: relative;
    }

    .kpi-icon {
        position: absolute;
        top: 1.5rem;
        right: 1.5rem;
        font-size: 3.5rem;
        opacity: 0.12;
        transition: var(--transition);
    }

    .glass-card:hover .kpi-icon {
        opacity: 0.2;
        transform: scale(1.1);
    }

    .kpi-label {
        font-size: 0.95rem;
        color: var(--text-secondary);
        font-weight: 600;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        margin-bottom: 0.75rem;
    }

    .kpi-value {
        font-size: 2.75rem;
        font-weight: 800;
        margin: 0.75rem 0;
        background: linear-gradient(135deg, var(--text-primary), var(--accent));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .kpi-trend {
        font-size: 0.925rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        margin-top: 0.75rem;
    }

    .text-success { color: var(--success); }
    .text-danger { color: var(--danger); }

    .section-title {
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--text-primary);
        position: relative;
        padding-bottom: 1rem;
        margin-bottom: 1.5rem;
    }

    .section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100px;
        height: 5px;
        background: linear-gradient(90deg, var(--accent), transparent);
        border-radius: 3px;
    }

    .chart-container {
        position: relative;
        height: 440px;
        margin-top: 1.5rem;
    }

    .filter-select {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 0.75rem;
        padding: 0.75rem 1.25rem;
        font-size: 1rem;
        min-width: 160px;
        transition: var(--transition);
        color: var(--text-primary);
    }

    .filter-select:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.25);
    }

    .btn-primary {
        background: var(--accent);
        color: white;
        font-weight: 600;
        padding: 0.75rem 2rem;
        border-radius: 0.75rem;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        box-shadow: var(--shadow-md);
    }

    .btn-primary:hover {
        background: var(--accent-hover);
        transform: translateY(-3px);
        box-shadow: 0 15px 30px -8px rgba(249, 115, 22, 0.4);
    }

    .header-title {
        font-size: 2.75rem;
        font-weight: 900;
        background: linear-gradient(135deg, var(--text-primary), var(--accent));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        line-height: 1.1;
    }

    .header-subtitle {
        font-size: 1.25rem;
        color: var(--text-secondary);
        margin-top: 0.75rem;
        font-weight: 500;
    }

    .update-info {
        font-size: 0.95rem;
        color: var(--text-muted);
        margin-top: 0.5rem;
    }

    /* Tabelas */
    .table-premium {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 0.75rem;
    }

    .table-premium th {
        font-size: 0.8rem;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: var(--text-muted);
        padding: 1rem 1.25rem;
        background: rgba(248, 250, 252, 0.6);
        border-bottom: 2px solid var(--border);
    }

    .table-premium td {
        padding: 1.25rem;
        background: var(--bg-card);
        border-radius: 0.75rem;
        box-shadow: var(--shadow-sm);
    }

    .table-premium tbody tr {
        transition: var(--transition);
    }

    .table-premium tbody tr:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-md);
    }

    /* Animações */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(40px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes pulse {
        0%, 100% { opacity: 0.7; }
        50% { opacity: 1; }
    }

    .animate-fade-in {
        animation: fadeInUp 0.9s ease-out forwards;
    }

    /* Responsividade */
    @media (max-width: 1024px) {
        .header-title { font-size: 2.25rem; }
        .kpi-value { font-size: 2.25rem; }
        .chart-container { height: 380px; }
    }

    @media (max-width: 768px) {
        .header-title { font-size: 2rem; }
        .kpi-value { font-size: 2rem; }
        .section-title { font-size: 1.5rem; }
        .chart-container { height: 320px; }
        .kpi-card { padding: 1.5rem; }
    }

    @media (max-width: 480px) {
        .header-title { font-size: 1.75rem; }
        .kpi-value { font-size: 1.75rem; }
        .btn-primary { padding: 0.65rem 1.5rem; font-size: 0.95rem; }
    }

    /* Dark mode ajustes específicos */
    .dark .table-premium th {
        background: rgba(30, 41, 59, 0.8);
    }

    .dark .table-premium td {
        background: rgba(15, 23, 42, 0.6);
    }
</style>

<div class="container mx-auto px-4 py-12 max-w-7xl">

    {{-- HEADER COM CONTROLES --}}
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-start gap-8 mb-12 animate-fade-in">
        <div>
            <h1 class="header-title">
                Financeiro Consolidado
            </h1>
            <p class="header-subtitle">
                {{ $empresaNome }} • Visão Executiva Completa
            </p>
            <p class="update-info">
                Dados atualizados em {{ now()->format('d/m/Y \à\s H:i') }}
            </p>
        </div>

        <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center">
            <button id="darkModeToggle" class="p-3 rounded-xl bg-gray-200 dark:bg-gray-700 text-xl transition-all hover:scale-110">
            </button>

            <a href="/omie/{{ $empresa }}/movimentos-financeiros" class="btn-primary text-lg">
                <i class="fas fa-list-ul"></i>
                Ver Movimentos Financeiros
            </a>
        </div>
    </div>

    {{-- KPIs PRINCIPAIS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 mb-16">

        <!-- Receber Realizado -->
        <div class="glass-card kpi-card animate-fade-in" style="animation-delay: 0.15s;">
            <i class="fas fa-hand-holding-dollar kpi-icon" style="color: var(--success);"></i>
            <div class="kpi-label">Total a Receber (Realizado)</div>
            <div class="kpi-value" style="background: linear-gradient(135deg, var(--success), #34d399); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                R$ {{ number_format($kpis['receber']['valor'], 2, ',', '.') }}
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $kpis['receber']['qtd'] }} títulos</p>
            <div class="kpi-trend text-success">
            </div>
        </div>

        <!-- Pagar Realizado -->
        <div class="glass-card kpi-card animate-fade-in" style="animation-delay: 0.3s;">
            <i class="fas fa-money-bill-transfer kpi-icon" style="color: var(--danger);"></i>
            <div class="kpi-label">Total a Pagar (Realizado)</div>
            <div class="kpi-value" style="background: linear-gradient(135deg, var(--danger), #f87171); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                R$ {{ number_format($kpis['pagar']['valor'], 2, ',', '.') }}
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $kpis['pagar']['qtd'] }} títulos</p>
            <div class="kpi-trend text-danger">
            </div>
        </div>

        <!-- Saldo Realizado -->
        <div class="glass-card kpi-card animate-fade-in" style="animation-delay: 0.45s;">
            <i class="fas fa-scale-balanced kpi-icon" style="color: {{ $kpis['saldo_realizado'] >= 0 ? 'var(--success)' : 'var(--danger)' }};"></i>
            <div class="kpi-label">Saldo Realizado</div>
            <div class="kpi-value" style="color: {{ $kpis['saldo_realizado'] >= 0 ? 'var(--success)' : 'var(--danger)' }};">
                R$ {{ number_format($kpis['saldo_realizado'], 2, ',', '.') }}
            </div>
            <div class="kpi-trend {{ $kpis['saldo_realizado'] >= 0 ? 'text-success' : 'text-danger' }}">
                <i class="fas {{ $kpis['saldo_realizado'] >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                {{ $kpis['saldo_realizado'] >= 0 ? '+' : '' }}{{ number_format(abs(($kpis['saldo_realizado'] / ($kpis['receber']['valor'] + $kpis['pagar']['valor'] ?: 1)) * 100), 1, ',', '.') }}%
            </div>
        </div>

        <!-- Registros -->
        <div class="glass-card kpi-card animate-fade-in" style="animation-delay: 0.6s;">
            <i class="fas fa-file-invoice-dollar kpi-icon" style="color: var(--purple);"></i>
            <div class="kpi-label">Movimentos Analisados</div>
            <div class="kpi-value" style="background: linear-gradient(135deg, var(--purple), #c4b5fd); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                {{ number_format($kpis['total_registros'], 0, '', '.') }}
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400">Total de registros processados</p>
        </div>

    </div>

    {{-- SEÇÃO DE PROJEÇÃO E DISTRIBUIÇÃO --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 mb-12">

        {{-- Projeção Financeira --}}
        <div class="glass-card p-8 animate-fade-in" style="animation-delay: 0.7s;">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 mb-8">
                <h2 class="section-title">Projeção Financeira por Horizonte</h2>
                <select id="tipoGraficoProjecao" class="filter-select">
                    <option value="bar">Barras</option>
                    <option value="line">Linhas</option>
                    <option value="radar">Radar</option>
                </select>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                Distribuição de títulos em aberto por período de vencimento
            </p>
            <div class="chart-container">
                <canvas id="graficoProjecao"></canvas>
            </div>

           
        </div>

        {{-- Distribuição por Período --}}
<div class="glass-card p-8 animate-fade-in" style="animation-delay: 0.85s;">
    <h2 class="section-title">Distribuição por Período</h2>
    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
        Análise proporcional do saldo projetado por horizonte de vencimento
    </p>

    <div class="chart-container">
        <canvas id="graficoPiePeriodos"></canvas>
    </div>

    <div class="mt-8 overflow-x-auto">
        <table class="min-w-full table-premium">
            <thead>
                <tr>
                    <th class="text-left">Período</th>
                    <th class="text-right">A Receber</th>
                    <th class="text-right">A Pagar</th>
                    <th class="text-right">Saldo Projetado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($projecoes as $dias => $p)
                    <tr>
                        <td class="font-semibold">{{ $dias }} dias</td>
                        <td class="text-right text-success font-medium">
                            R$ {{ number_format($p['receber'], 2, ',', '.') }}
                        </td>
                        <td class="text-right text-danger font-medium">
                            R$ {{ number_format($p['pagar'], 2, ',', '.') }}
                        </td>
                        <td class="text-right font-bold {{ $p['saldo'] >= 0 ? 'text-success' : 'text-danger' }}">
                            R$ {{ number_format($p['saldo'], 2, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>


    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    Chart.register(ChartDataLabels);

    const moeda = v => 'R$ ' + Number(v).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    // Dark mode
    const darkToggle = document.getElementById('darkModeToggle');
    const darkIcon = darkToggle.querySelector('i');
    const savedTheme = localStorage.getItem('darkMode') === 'true';
    if (savedTheme) {
        document.body.classList.add('dark');
        darkIcon.classList.replace('fa-moon', 'fa-sun');
    }

    darkToggle.addEventListener('click', () => {
        document.body.classList.toggle('dark');
        const isDark = document.body.classList.contains('dark');
        localStorage.setItem('darkMode', isDark);
        darkIcon.classList.toggle('fa-moon', !isDark);
        darkIcon.classList.toggle('fa-sun', isDark);
    });

    // Dados
    let projecoes = @json($projecoes);
    let labelsProj = Object.keys(projecoes).map(d => `${d} dias`);
    let receberProj = Object.values(projecoes).map(p => p.receber);
    let pagarProj = Object.values(projecoes).map(p => p.pagar);
    let saldosProj = Object.values(projecoes).map(p => p.saldo);

    const totalReceber = receberProj.reduce((a, b) => a + b, 0);
    const totalPagar = pagarProj.reduce((a, b) => a + b, 0);

    // Gráfico de Projeção
    let ctxProj = document.getElementById('graficoProjecao')?.getContext('2d');
    let chartProj;
    function renderProj(tipo) {
        if (chartProj) chartProj.destroy();

        chartProj = new Chart(ctxProj, {
            type: tipo,
            data: {
                labels: labelsProj,
                datasets: [
                    {
                        label: 'A Receber',
                        data: receberProj,
                        backgroundColor: 'rgba(16, 185, 129, 0.75)',
                        borderColor: '#10b981',
                        borderWidth: 3,
                        borderRadius: tipo === 'bar' ? 8 : 0,
                    },
                    {
                        label: 'A Pagar',
                        data: pagarProj,
                        backgroundColor: 'rgba(239, 68, 68, 0.75)',
                        borderColor: '#ef4444',
                        borderWidth: 3,
                        borderRadius: tipo === 'bar' ? 8 : 0,
                    },
                    {
                        label: 'Saldo Projetado',
                        data: saldosProj,
                        type: 'line',
                        borderColor: '#f97316',
                        backgroundColor: 'rgba(249, 115, 22, 0.15)',
                        borderWidth: 5,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 8,
                        pointHoverRadius: 12,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    title: { display: true, text: 'Projeção por Horizonte Temporal', font: { size: 16, weight: 'bold' }, color: 'var(--text-primary)' },
                    legend: { position: 'top', labels: { font: { size: 14 }, padding: 20 } },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.95)',
                        titleFont: { size: 15 },
                        bodyFont: { size: 14 },
                        callbacks: { label: c => `${c.dataset.label}: ${moeda(c.raw)}` }
                    },
                    datalabels: {
                        color: '#ffffff',
                        font: { weight: 'bold', size: 13 },
                        formatter: (value) => value === 0 ? '' : moeda(value),
                        anchor: 'end',
                        align: 'top'
                    }
                },
                scales: {
                    y: { ticks: { callback: moeda, font: { size: 13 } }, grid: { color: 'rgba(148, 163, 184, 0.15)' } },
                    x: { grid: { display: false } }
                }
            }
        });
    }
    renderProj('bar');
    document.getElementById('tipoGraficoProjecao')?.addEventListener('change', e => renderProj(e.target.value));

    // Gráfico Pie
// Gráfico Pie por Período
let ctxPiePeriodos = document.getElementById('graficoPiePeriodos')?.getContext('2d');

if (ctxPiePeriodos) {
    new Chart(ctxPiePeriodos, {
        type: 'doughnut',
        data: {
            labels: labelsProj,
            datasets: [{
                data: saldosProj,
                backgroundColor: [
                    '#10b981',
                    '#f59e0b',
                    '#ef4444'
                ],
                borderColor: [
                    '#065f46',
                    '#92400e',
                    '#991b1b'
                ],
                borderWidth: 3,
                hoverOffset: 25
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        font: { size: 14 }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: c => `${c.label}: ${moeda(c.raw)}`
                    }
                },
                datalabels: {
                    color: '#ffffff',
                    font: { weight: 'bold', size: 16 },
                    formatter: (value, ctx) => {
                        const total = ctx.chart.data.datasets[0].data
                            .reduce((a, b) => a + b, 0);

                        const percentage = total > 0
                            ? Math.round((value / total) * 100) + '%'
                            : '0%';

                        return [moeda(value), percentage];
                    }
                }
            }
        }
    });
}

});
</script>
@endpush
@endsection