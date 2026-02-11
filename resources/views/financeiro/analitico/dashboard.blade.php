@extends('layouts.app')

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

    /* ===============================
       SIDEBAR — PREMIUM SAAS
       =============================== */

    .sidebar-container {
        background: linear-gradient(
            180deg,
            rgba(255,255,255,.96),
            rgba(249,250,251,.92)
        );
    }

    /* Scroll refinado */
    .sidebar-scroll {
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: rgba(0,0,0,.25) transparent;
    }

    .sidebar-scroll::-webkit-scrollbar {
        width: 6px;
    }
    .sidebar-scroll::-webkit-scrollbar-thumb {
        background-color: rgba(0,0,0,.25);
        border-radius: 999px;
    }
    .sidebar-scroll::-webkit-scrollbar-thumb:hover {
        background-color: rgba(0,0,0,.45);
    }

    /* Títulos */
    .sidebar-title {
        margin-bottom: .4rem;
        font-size: .65rem;
        letter-spacing: .14em;
        text-transform: uppercase;
        color: var(--text-muted);
    }

    /* Links principais */
    .sidebar-link {
        display: flex;
        align-items: center;
        gap: .55rem;
        padding: .6rem .75rem;
        border-radius: 12px;
        font-weight: 500;
        color: var(--text-primary);
        transition: all .18s ease;
    }

    .sidebar-link:hover {
        background: linear-gradient(
            90deg,
            rgba(249,115,22,.10),
            rgba(251,191,36,.10)
        );
    }

    .sidebar-link.active {
        background: linear-gradient(
            90deg,
            var(--accent),
            var(--warning)
        );
        color: #fff;
        font-weight: 600;
        box-shadow:
            0 6px 18px rgba(249,115,22,.25),
            inset 0 1px 0 rgba(255, 123, 0, 1);
    }

    /* Sub-links */
    .sidebar-sub {
        position: relative;
        display: flex;
        align-items: center;
        padding: .42rem .75rem .42rem 1.4rem;
        margin-left: .4rem;
        border-radius: 10px;
        font-size: .78rem;
        color: var(--text-muted);
        transition: all .18s ease;
    }

    .sidebar-sub::before {
        content: '';
        position: absolute;
        left: .55rem;
        top: 50%;
        width: 4px;
        height: 4px;
        background: currentColor;
        border-radius: 50%;
        transform: translateY(-50%);
        opacity: .4;
    }

    .sidebar-sub:hover {
        color: var(--text-primary);
        background: rgba(0,0,0,.035);
    }

    .sidebar-sub.active {
        color: var(--accent);
        font-weight: 600;
        background: rgba(249,115,22,.10);
    }

    .sidebar-section {
        margin-top: 1.35rem;
    }
</style>

{{-- SIDEBAR --}}
<aside class="fixed top-0 left-0 h-screen w-64 p-5 hidden lg:flex z-40">

    <div class="sidebar-container h-full w-full rounded-[18px]
                border border-[var(--border)]
                shadow-[var(--shadow-lg)]
                flex flex-col">

        {{-- MENU --}}
        <nav class="flex-1 px-4 py-4 text-sm sidebar-scroll space-y-7">

            {{-- VISÃO GERAL --}}
            <div class="sidebar-section">
                <p class="sidebar-title">Visão Geral</p>

                <a href="/financeiro/analitico"
                   class="sidebar-link {{ request()->is('financeiro/analitico') ? 'active' : '' }}">
                    📊 Dashboard Geral
                </a>
            </div>

            {{-- FINANCEIRO ANALÍTICO --}}
            <div class="sidebar-section">
                <p class="sidebar-title">Financeiro Analítico</p>

                <a href="/financeiro/analitico/empresa/sv"
                   class="sidebar-link {{ request()->is('financeiro/analitico/empresa/sv*') ? 'active' : '' }}">
                    ⚖️ Advogados (SV)
                </a>

                <a href="/financeiro/analitico/empresa/vs"
                   class="sidebar-link {{ request()->is('financeiro/analitico/empresa/vs*') ? 'active' : '' }}">
                    💼 Soluções (VS)
                </a>

                <a href="/financeiro/analitico/empresa/gv"
                   class="sidebar-link {{ request()->is('financeiro/analitico/empresa/gv*') ? 'active' : '' }}">
                    🏢 Grupo (GV)
                </a>

                <a href="/financeiro/analitico/empresa/cs"
                   class="sidebar-link {{ request()->is('financeiro/analitico/empresa/cs*') ? 'active' : '' }}">
                    🧠 Consultoria (CS)
                </a>
            </div>

            {{-- OMIE --}}
            <div class="sidebar-section">
                <p class="sidebar-title">Omie</p>

                {{-- RESUMO FINANCEIRO --}}
                <div class="space-y-1">
                    <p class="text-xs text-[var(--text-muted)] mb-1">Resumo Financeiro</p>

                    <a href="/omie/sv/resumo-financas"
                       class="sidebar-sub {{ request()->is('omie/sv/resumo-financas*') ? 'active' : '' }}">SV</a>

                    <a href="/omie/vs/resumo-financas"
                       class="sidebar-sub {{ request()->is('omie/vs/resumo-financas*') ? 'active' : '' }}">VS</a>

                    <a href="/omie/gv/resumo-financas"
                       class="sidebar-sub {{ request()->is('omie/gv/resumo-financas*') ? 'active' : '' }}">GV</a>

                    <a href="/omie/cs/resumo-financas"
                       class="sidebar-sub {{ request()->is('omie/cs/resumo-financas*') ? 'active' : '' }}">CS</a>
                </div>

                {{-- PROJEÇÕES --}}
                <div class="space-y-1 mt-4">
                    <p class="text-xs text-[var(--text-muted)] mb-1">Projeções</p>

                    <a href="/omie/sv/financeiro-consolidado"
                       class="sidebar-sub {{ request()->is('omie/sv/financeiro-consolidado*') ? 'active' : '' }}">SV</a>

                    <a href="/omie/vs/financeiro-consolidado"
                       class="sidebar-sub {{ request()->is('omie/vs/financeiro-consolidado*') ? 'active' : '' }}">VS</a>

                    <a href="/omie/gv/financeiro-consolidado"
                       class="sidebar-sub {{ request()->is('omie/gv/financeiro-consolidado*') ? 'active' : '' }}">GV</a>

                    <a href="/omie/cs/financeiro-consolidado"
                       class="sidebar-sub {{ request()->is('omie/cs/financeiro-consolidado*') ? 'active' : '' }}">CS</a>
                </div>

                {{-- MOVIMENTOS FINANCEIROS --}}
                <div class="space-y-1 mt-4">
                    <p class="text-xs text-[var(--text-muted)] mb-1">Movimentos Financeiros</p>

                    <a href="/omie/sv/movimentos-financeiros" class="sidebar-sub {{ request()->is('omie/sv/movimentos-financeiros*') ? 'active' : '' }}">SV</a>
                    <a href="/omie/vs/movimentos-financeiros" class="sidebar-sub {{ request()->is('omie/vs/movimentos-financeiros*') ? 'active' : '' }}">VS</a>
                    <a href="/omie/gv/movimentos-financeiros" class="sidebar-sub {{ request()->is('omie/gv/movimentos-financeiros*') ? 'active' : '' }}">GV</a>
                    <a href="/omie/cs/movimentos-financeiros" class="sidebar-sub {{ request()->is('omie/cs/movimentos-financeiros*') ? 'active' : '' }}">CS</a>
                </div>

                {{-- CONTAS A PAGAR --}}
                <div class="space-y-1 mt-4">
                    <p class="text-xs text-[var(--text-muted)] mb-1">Contas a Pagar</p>

                    @foreach (['sv','vs','gv','cs'] as $emp)
                        <a href="{{ route('omie.pagar.index', ['empresa' => $emp]) }}"
                           class="sidebar-sub {{ request()->is("omie/$emp/pagar*") ? 'active' : '' }}">
                            {{ strtoupper($emp) }} · Listar
                        </a>

                        <a href="{{ route('omie.pagar.create', ['empresa' => $emp]) }}"
                           class="sidebar-sub text-orange-600 hover:text-orange-700">
                            ➕ Novo Custo
                        </a>
                    @endforeach
                </div>

                {{-- CONTAS A RECEBER --}}
                <div class="space-y-1 mt-4">
                    <p class="text-xs text-[var(--text-muted)] mb-1">Contas a Receber</p>

                    @foreach (['sv','vs','gv','cs'] as $emp)
                        <a href="{{ route('omie.receber.index', ['empresa' => $emp]) }}"
                           class="sidebar-sub {{ request()->is("omie/$emp/receber*") ? 'active' : '' }}">
                            {{ strtoupper($emp) }} · Listar
                        </a>

                        <a href="{{ route('omie.receber.create', ['empresa' => $emp]) }}"
                           class="sidebar-sub text-emerald-600 hover:text-emerald-700">
                            ➕ Novo Recebimento
                        </a>
                    @endforeach
                </div>

                {{-- CONTAS CORRENTES --}}
                <div class="space-y-1 mt-4">
                    <p class="text-xs text-[var(--text-muted)] mb-1">Contas Correntes</p>
                    @foreach (['sv','vs','gv','cs'] as $emp)
                        <a href="/omie/{{ $emp }}/contas-correntes"
                           class="sidebar-sub {{ request()->is("omie/$emp/contas-correntes*") ? 'active' : '' }}">
                            {{ strtoupper($emp) }}
                        </a>
                    @endforeach
                </div>
                {{-- NOTAS FISCAIS --}}
                <div class="space-y-1 mt-4">
                    <p class="text-xs text-[var(--text-muted)] mb-1">Notas Fiscais</p>
                    @foreach (['sv','vs','gv','cs'] as $emp)
                        <a href="/omie/{{ $emp }}/notas-fiscais"
                           class="sidebar-sub {{ request()->is("omie/$emp/notas-fiscais*") ? 'active' : '' }}">
                            {{ strtoupper($emp) }}
                        </a>
                    @endforeach
                </div>

                {{-- CLIENTES --}}
                <div class="space-y-1 mt-4">
                    <p class="text-xs text-[var(--text-muted)] mb-1">Clientes</p>
                    @foreach (['sv','vs','gv','cs'] as $emp)
                        <a href="/omie/{{ $emp }}/clientes"
                           class="sidebar-sub {{ request()->is("omie/$emp/clientes*") ? 'active' : '' }}">
                            {{ strtoupper($emp) }}
                        </a>
                    @endforeach
                </div>

                {{-- CONTRATOS --}}
                <div class="space-y-1 mt-4">
                    <p class="text-xs text-[var(--text-muted)] mb-1">Contratos</p>
                    @foreach (['sv','vs','gv','cs'] as $emp)
                        <a href="/omie/{{ $emp }}/contratos"
                           class="sidebar-sub {{ request()->is("omie/$emp/contratos*") ? 'active' : '' }}">
                            {{ strtoupper($emp) }}
                        </a>
                    @endforeach
                </div>

                {{-- SERVIÇOS --}}
                <div class="space-y-1 mt-4">
                    <p class="text-xs text-[var(--text-muted)] mb-1">Serviços</p>
                    @foreach (['sv','vs','gv','cs'] as $emp)
                        <a href="/omie/{{ $emp }}/servicos"
                           class="sidebar-sub {{ request()->is("omie/$emp/servicos*") ? 'active' : '' }}">
                            {{ strtoupper($emp) }}
                        </a>
                    @endforeach
                </div>

                {{-- CATEGORIAS --}}
                <div class="space-y-1 mt-4">
                    <p class="text-xs text-[var(--text-muted)] mb-1">Categorias</p>
                    @foreach (['sv','vs','gv','cs'] as $emp)
                        <a href="/omie/categorias/{{ $emp }}"
                           class="sidebar-sub {{ request()->is("omie/categorias/$emp*") ? 'active' : '' }}">
                            {{ strtoupper($emp) }}
                        </a>
                    @endforeach
                </div>

                {{-- EMPRESAS --}}
                <div class="space-y-1 mt-4">
                    <p class="text-xs text-[var(--text-muted)] mb-1">Empresas</p>
                    @foreach (['sv','vs','gv','cs'] as $emp)
                        <a href="/omie/{{ $emp }}/empresas"
                           class="sidebar-sub {{ request()->is("omie/$emp/empresas*") ? 'active' : '' }}">
                            {{ strtoupper($emp) }}
                        </a>
                    @endforeach
                </div>

            </div>
        </nav>

        {{-- FOOTER --}}
        <div class="px-4 py-3 border-t text-xs text-[var(--text-muted)]">
            © {{ date('Y') }} Grupo Verreschi
        </div>

    </div>
</aside>






<div class="container mx-auto px-4 py-12 max-w-7xl">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-8 mb-12 animate-fade-in">
        <div>
            <h1 class="header-title">
                Financeiro 
            </h1>
            <p class="header-subtitle">
                Visão consolidada e auditável — {{ $ano }}
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

    {{-- KPI EXECUTIVO --}}
    <section class="space-y-3">
        <div>
            <h2 class="section-title">Resumo Executivo</h2>
            
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
            <div class="glass-card kpi-card animate-fade-in" style="animation-delay: 0.1s;">
                <div class="kpi-label">Total a Receber</div>
                <div class="kpi-value" style="background: linear-gradient(135deg, var(--success), #34d399); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                    R$ {{ number_format($kpis['receita'], 2, ',', '.') }}
                </div>
            </div>

            <div class="glass-card kpi-card animate-fade-in" style="animation-delay: 0.2s;">
                <div class="kpi-label">Total a Pagar</div>
                <div class="kpi-value" style="background: linear-gradient(135deg, var(--danger), #f87171); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                    R$ {{ number_format($kpis['custos'], 2, ',', '.') }}
                </div>
            </div>

            <div class="glass-card kpi-card animate-fade-in" style="animation-delay: 0.3s;">
                <div class="kpi-label">Saldo</div>
                <div class="kpi-value" style="color: {{ $kpis['saldo'] >= 0 ? 'var(--success)' : 'var(--danger)' }};">
                    R$ {{ number_format($kpis['saldo'], 2, ',', '.') }}
                </div>
            </div>

            <div class="glass-card kpi-card animate-fade-in" style="animation-delay: 0.4s;">
                <div class="kpi-label">Margem (%)</div>
                <div class="kpi-value" style="background: linear-gradient(135deg, var(--purple), #a78bfa); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                    {{ number_format($kpis['margem'], 2, ',', '.') }}%
                </div>
            </div>
        </div>
    </section>
    <br>

    {{-- EVOLUÇÃO MENSAL --}}
    <section class="space-y-3">
        <div class="glass-card p-8 animate-fade-in" style="animation-delay: 0.5s;">
            <div class="flex justify-between items-center mb-6">
                <h2 class="section-title">Evolução Mensal</h2>
                <select id="tipoGrafico" class="filter-select text-sm">
                    <option value="bar">Barras</option>
                    <option value="line">Linhas</option>
                </select>
            </div>
            <p class="header-subtitle mb-4">
                Recebimentos × Pagamentos mês a mês
            </p>
            <div class="chart-container">
                <canvas id="graficoMensal"></canvas>
            </div>
        </div>
    </section>



@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {

    const moeda = v => 'R$ ' + Number(v).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const percent = v => Number(v).toFixed(1).replace('.', ',') + '%';

    let dados = @json($mensal);

    // === Gráfico Mensal ===
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
                            label: 'Receber',
                            data: receitas,
                            backgroundColor: 'rgba(16, 185, 129, 0.7)',
                            borderColor: '#10b981',
                            borderWidth: 2,
                            borderRadius: 8,
                            borderSkipped: false,
                        },
                        {
                            label: 'Pagar',
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

});
</script>
@endpush
@endsection