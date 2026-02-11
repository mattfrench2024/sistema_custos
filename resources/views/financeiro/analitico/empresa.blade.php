@extends('layouts.app')

@section('title', 'Financeiro Analítico — ' . $empresaNome)

@section('content')

<style>
    :root {
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
        --shadow-sm: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --shadow-md: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 20px 40px -10px rgba(0, 0, 0, 0.15);
        --border: rgba(226, 232, 240, 0.8);
        --radius: 1rem;
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    body {
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
        min-height: 100vh;
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        box-shadow: var(--shadow-lg);
        transition: var(--transition);
    }

    .glass-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.2);
    }

    .kpi-card {
        padding: 1.75rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .kpi-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, var(--accent), var(--warning));
        border-radius: var(--radius) var(--radius) 0 0;
    }

    .kpi-label {
        font-size: 0.95rem;
        color: var(--text-secondary);
        font-weight: 500;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }

    .kpi-value {
        font-size: 2.5rem;
        font-weight: 800;
        margin: 0.5rem 0;
        background: linear-gradient(135deg, var(--text-primary), var(--accent));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .kpi-trend {
        font-size: 0.875rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary);
        position: relative;
        padding-bottom: 0.75rem;
    }

    .section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 80px;
        height: 4px;
        background: var(--accent);
        border-radius: 2px;
    }

    .chart-container {
        position: relative;
        height: 420px;
        margin-top: 1.5rem;
    }

    .filter-select {
        background: white;
        border: 1px solid var(--border);
        border-radius: 0.5rem;
        padding: 0.65rem 1rem;
        font-size: 0.95rem;
        min-width: 140px;
        transition: var(--transition);
    }

    .filter-select:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.2);
    }

    .filter-button {
        background: var(--accent);
        color: white;
        font-weight: 600;
        padding: 0.65rem 1.5rem;
        border-radius: 0.5rem;
        transition: var(--transition);
    }

    .filter-button:hover {
        background: var(--accent-hover);
        transform: translateY(-2px);
    }

    .header-title {
        font-size: 2.25rem;
        font-weight: 800;
        color: var(--text-primary);
        background: linear-gradient(135deg, var(--text-primary), var(--accent));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .header-subtitle {
        font-size: 1.125rem;
        color: var(--text-secondary);
        margin-top: 0.5rem;
    }

    /* Animações */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in {
        animation: fadeInUp 0.8s ease-out forwards;
    }

    /* Responsividade aprimorada */
    @media (max-width: 768px) {
        .header-title {
            font-size: 1.875rem;
        }
        .kpi-value {
            font-size: 2rem;
        }
        .chart-container {
            height: 300px;
        }
    }
</style>

<div class="container mx-auto px-4 py-12 max-w-7xl">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-8 mb-12 animate-fade-in">
        <div>
            <h1 class="header-title">
                Financeiro Analítico — {{ $empresaNome }}
            </h1>
            <p class="header-subtitle">
                Ano {{ $ano }}{{ $mes 
                    ? ' • ' . \Carbon\Carbon::create()->month((int) $mes)->locale('pt_BR')->isoFormat('MMMM')
                    : ' • Visão Completa' 
                }}
            </p>
        </div>

        <form method="GET" class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center">
            <select name="ano" class="filter-select">
                @for ($y = now()->year; $y >= now()->year - 5; $y--)
                    <option value="{{ $y }}" @selected($y == $ano)>{{ $y }}</option>
                @endfor
            </select>

            <select name="mes" class="filter-select">
                <option value="">Todos os Meses</option>
                @for ($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" @selected($mes == $m)>
                        {{ \Carbon\Carbon::create()->month($m)->locale('pt_BR')->isoFormat('MMMM') }}
                    </option>
                @endfor
            </select>

            <button type="submit" class="filter-button">
                Aplicar Filtro
            </button>
        </form>
    </div>

    <!-- KPIs -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
        <div class="glass-card kpi-card animate-fade-in" style="animation-delay: 0.1s;">
            <div class="kpi-label">Receita Total</div>
            <div class="kpi-value" style="background: linear-gradient(135deg, var(--success), #34d399); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                R$ {{ number_format($kpis['receita'], 2, ',', '.') }}
            </div>
        </div>

        <div class="glass-card kpi-card animate-fade-in" style="animation-delay: 0.2s;">
            <div class="kpi-label">Custos Totais</div>
            <div class="kpi-value" style="background: linear-gradient(135deg, var(--danger), #f87171); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                R$ {{ number_format($kpis['custos'], 2, ',', '.') }}
            </div>
        </div>

        <div class="glass-card kpi-card animate-fade-in" style="animation-delay: 0.3s;">
            <div class="kpi-label">Resultado Líquido</div>
            <div class="kpi-value" style="color: {{ $kpis['saldo'] >= 0 ? 'var(--success)' : 'var(--danger)' }};">
                R$ {{ number_format($kpis['saldo'], 2, ',', '.') }}
            </div>
        </div>

        <div class="glass-card kpi-card animate-fade-in" style="animation-delay: 0.4s;">
            <div class="kpi-label">Margem Líquida</div>
            <div class="kpi-value" style="background: linear-gradient(135deg, var(--purple), #a78bfa); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                {{ number_format($kpis['margem'], 2, ',', '.') }}%
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-10">

        <!-- Evolução Financeira -->
        <div class="glass-card p-8 animate-fade-in" style="animation-delay: 0.5s;">
            <div class="flex justify-between items-center mb-6">
                <h2 class="section-title">Evolução Financeira Mensal</h2>
                <select id="tipoGrafico" class="filter-select text-sm">
                    <option value="bar">Barras</option>
                    <option value="line">Linhas</option>
                </select>
            </div>
            <div class="chart-container">
                <canvas id="graficoMensal"></canvas>
            </div>
        </div>

        <!-- Concentração de Receita por Cliente -->
<div class="glass-card p-8 animate-fade-in" style="animation-delay: 0.6s;">
    <h2 class="section-title mb-6"> Receita por Cliente (Top 5)</h2>
    <div class="chart-container relative">
        <canvas id="graficoConcentracao"></canvas>
        <div id="concentracaoEmpty" class="absolute inset-0 flex flex-col items-center justify-center bg-white/80 backdrop-blur-sm rounded-xl hidden">
            <svg class="w-20 h-20 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            <p class="text-lg font-medium text-gray-500">Sem dados de receita no período selecionado</p>
        </div>
    </div>
</div>



@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {

    const moeda = v => 'R$ ' + Number(v).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const percent = v => Number(v).toFixed(1).replace('.', ',') + '%';

    let dados = @json($mensal);
    let concentracao = @json($concentracaoClientes);

    // === Gráfico Mensal (inalterado – continua funcionando 100%) ===
    let labels = dados.map(i => i.mes);
    let receitas = dados.map(i => i.receita);
    let custos = dados.map(i => i.custos);
    let saldos = dados.map(i => i.receita - i.custos);

    let ctxMensal = document.getElementById('graficoMensal')?.getContext('2d');
    if (ctxMensal) {
        let chartMensal;

        function renderMensal(tipo) {
            if (chartMensal) chartMensal.destroy();

            chartMensal = new Chart(ctxMensal, {
                type: tipo,
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Receita',
                            data: receitas,
                            backgroundColor: 'rgba(16, 185, 129, 0.7)',
                            borderColor: '#10b981',
                            borderWidth: 2,
                            borderRadius: 8,
                            borderSkipped: false,
                        },
                        {
                            label: 'Custos',
                            data: custos,
                            backgroundColor: 'rgba(239, 68, 68, 0.7)',
                            borderColor: '#ef4444',
                            borderWidth: 2,
                            borderRadius: 8,
                            borderSkipped: false,
                        },
                        {
                            label: 'Resultado',
                            data: saldos,
                            type: 'line',
                            borderColor: '#f97316',
                            backgroundColor: 'rgba(249, 115, 22, 0.1)',
                            borderWidth: 4,
                            tension: 0.4,
                            fill: true,
                            pointRadius: 6,
                            pointHoverRadius: 10,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { position: 'top', labels: { font: { size: 14, weight: '600' } } },
                        tooltip: {
                            backgroundColor: 'rgba(30, 41, 59, 0.95)',
                            titleFont: { size: 14 },
                            bodyFont: { size: 13 },
                            callbacks: { label: c => `${c.dataset.label}: ${moeda(c.raw)}` }
                        }
                    },
                    scales: {
                        y: {
                            ticks: { callback: moeda, font: { size: 12 } },
                            grid: { color: 'rgba(148, 163, 184, 0.2)' }
                        },
                        x: { grid: { display: false } }
                    }
                }
            });
        }

        renderMensal('bar');
        document.getElementById('tipoGrafico')?.addEventListener('change', e => renderMensal(e.target.value));
    }

    // === Doughnut Concentração (agora sempre mostra algo se houver receita) ===
    const ctxConcentracao = document.getElementById('graficoConcentracao')?.getContext('2d');
    const emptyState = document.getElementById('concentracaoEmpty');

    if (!ctxConcentracao || !concentracao || concentracao.length === 0) {
        if (emptyState) emptyState.classList.remove('hidden');
        return;
    }

    if (emptyState) emptyState.classList.add('hidden');

    const totalGeral = concentracao.reduce((sum, item) => sum + parseFloat(item.total || 0), 0);

    new Chart(ctxConcentracao, {
        type: 'doughnut',
        data: {
            labels: concentracao.map(item => item.cliente),
            datasets: [{
                data: concentracao.map(item => parseFloat(item.total || 0)),
                backgroundColor: [
                    '#f97316', '#ea580c', '#fb923c', '#fbbf24', '#fde68a',
                    '#fdba74', '#fecaca', '#a78bfa', '#c4b5fd', '#e9d5ff',
                    '#94a3b8', '#64748b', '#475569', '#1e293b'
                ],
                borderWidth: 4,
                borderColor: '#ffffff',
                hoverOffset: 24,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '68%',
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        padding: 20,
                        font: { size: 13, weight: '500' },
                        generateLabels: function(chart) {
                            const data = chart.data;
                            if (data.labels.length && data.datasets.length) {
                                return data.labels.map((label, i) => {
                                    const value = data.datasets[0].data[i];
                                    const percentage = totalGeral > 0 ? (value / totalGeral) * 100 : 0;
                                    return {
                                        text: `${label} (${percent(percentage)}%)`,
                                        fillStyle: data.datasets[0].backgroundColor[i],
                                        strokeStyle: data.datasets[0].backgroundColor[i],
                                        hidden: false,
                                        index: i
                                    };
                                });
                            }
                            return [];
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(30, 41, 59, 0.95)',
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            const valor = context.raw || 0;
                            const percentual = totalGeral > 0 ? (valor / totalGeral) * 100 : 0;
                            return `${context.label}: ${moeda(valor)} (${percent(percentual)}%)`;
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endsection