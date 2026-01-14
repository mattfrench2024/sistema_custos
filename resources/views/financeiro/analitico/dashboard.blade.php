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
    color: var(--muted);
}

/* Links principais */
.sidebar-link {
    display: flex;
    align-items: center;
    gap: .55rem;
    padding: .6rem .75rem;
    border-radius: 12px;
    font-weight: 500;
    color: #111827;
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
        var(--brand-from),
        var(--brand-to)
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
    color: var(--muted);
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
    color: #111827;
    background: rgba(0,0,0,.035);
}

.sidebar-sub.active {
    color: var(--brand-from);
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
                border border-[var(--glass-border)]
                shadow-[var(--shadow-soft)]
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
            </div>

            {{-- OMIE --}}
            <div class="sidebar-section">
                <p class="sidebar-title">Omie</p>

                {{-- RESUMO FINANCEIRO --}}
                <div class="space-y-1">
                    <p class="text-xs text-[var(--muted)] mb-1">Resumo Financeiro</p>

                    <a href="/omie/sv/resumo-financas"
                       class="sidebar-sub {{ request()->is('omie/sv/resumo-financas*') ? 'active' : '' }}">
                        SV
                    </a>

                    <a href="/omie/vs/resumo-financas"
                       class="sidebar-sub {{ request()->is('omie/vs/resumo-financas*') ? 'active' : '' }}">
                        VS
                    </a>

                    <a href="/omie/gv/resumo-financas"
                       class="sidebar-sub {{ request()->is('omie/gv/resumo-financas*') ? 'active' : '' }}">
                        GV
                    </a>
                </div>
                {{-- FINANCEIRO CONSOLIDADO --}}
<div class="space-y-1 mt-4">
    <p class="text-xs text-[var(--muted)] mb-1">Projeções</p>

    <a href="/omie/sv/financeiro-consolidado"
       class="sidebar-sub {{ request()->is('omie/sv/financeiro-consolidado*') ? 'active' : '' }}">
        SV
    </a>

    <a href="/omie/vs/financeiro-consolidado"
       class="sidebar-sub {{ request()->is('omie/vs/financeiro-consolidado*') ? 'active' : '' }}">
        VS
    </a>

    <a href="/omie/gv/financeiro-consolidado"
       class="sidebar-sub {{ request()->is('omie/gv/financeiro-consolidado*') ? 'active' : '' }}">
        GV
    </a>
</div>

                {{-- MOVIMENTOS FINANCEIROS --}}
                <div class="space-y-1 mt-4">
                    <p class="text-xs text-[var(--muted)] mb-1">Movimentos Financeiros</p>

                    <a href="/omie/sv/movimentos-financeiros"
                       class="sidebar-sub {{ request()->is('omie/sv/movimentos-financeiros*') ? 'active' : '' }}">SV</a>

                    <a href="/omie/vs/movimentos-financeiros"
                       class="sidebar-sub {{ request()->is('omie/vs/movimentos-financeiros*') ? 'active' : '' }}">VS</a>

                    <a href="/omie/gv/movimentos-financeiros"
                       class="sidebar-sub {{ request()->is('omie/gv/movimentos-financeiros*') ? 'active' : '' }}">GV</a>
                </div>

                {{-- CONTAS A PAGAR --}}
                <div class="space-y-1 mt-4">
                    <p class="text-xs text-[var(--muted)] mb-1">Contas a Pagar</p>

                    <a href="/omie/sv/pagar"
                       class="sidebar-sub {{ request()->is('omie/sv/pagar*') ? 'active' : '' }}">SV</a>

                    <a href="/omie/vs/pagar"
                       class="sidebar-sub {{ request()->is('omie/vs/pagar*') ? 'active' : '' }}">VS</a>

                    <a href="/omie/gv/pagar"
                       class="sidebar-sub {{ request()->is('omie/gv/pagar*') ? 'active' : '' }}">GV</a>
                </div>

                {{-- CONTAS A RECEBER --}}
                <div class="space-y-1 mt-4">
                    <p class="text-xs text-[var(--muted)] mb-1">Contas a Receber</p>

                    <a href="/omie/sv/receber"
                       class="sidebar-sub {{ request()->is('omie/sv/receber*') ? 'active' : '' }}">SV</a>

                    <a href="/omie/vs/receber"
                       class="sidebar-sub {{ request()->is('omie/vs/receber*') ? 'active' : '' }}">VS</a>

                    <a href="/omie/gv/receber"
                       class="sidebar-sub {{ request()->is('omie/gv/receber*') ? 'active' : '' }}">GV</a>
                </div>

                {{-- CONTAS CORRENTES --}}
                <div class="space-y-1 mt-4">
                    <p class="text-xs text-[var(--muted)] mb-1">Contas Correntes</p>

                    <a href="/omie/sv/contas-correntes"
                       class="sidebar-sub {{ request()->is('omie/sv/contas-correntes*') ? 'active' : '' }}">SV</a>

                    <a href="/omie/vs/contas-correntes"
                       class="sidebar-sub {{ request()->is('omie/vs/contas-correntes*') ? 'active' : '' }}">VS</a>

                    <a href="/omie/gv/contas-correntes"
                       class="sidebar-sub {{ request()->is('omie/gv/contas-correntes*') ? 'active' : '' }}">GV</a>
                </div>

                {{-- CLIENTES --}}
                <div class="space-y-1 mt-4">
                    <p class="text-xs text-[var(--muted)] mb-1">Clientes</p>

                    <a href="/omie/sv/clientes"
                       class="sidebar-sub {{ request()->is('omie/sv/clientes*') ? 'active' : '' }}">SV</a>

                    <a href="/omie/vs/clientes"
                       class="sidebar-sub {{ request()->is('omie/vs/clientes*') ? 'active' : '' }}">VS</a>

                    <a href="/omie/gv/clientes"
                       class="sidebar-sub {{ request()->is('omie/gv/clientes*') ? 'active' : '' }}">GV</a>
                </div>

                {{-- CONTRATOS --}}
                <div class="space-y-1 mt-4">
                    <p class="text-xs text-[var(--muted)] mb-1">Contratos</p>

                    <a href="/omie/sv/contratos"
                       class="sidebar-sub {{ request()->is('omie/sv/contratos*') ? 'active' : '' }}">SV</a>

                    <a href="/omie/vs/contratos"
                       class="sidebar-sub {{ request()->is('omie/vs/contratos*') ? 'active' : '' }}">VS</a>

                    <a href="/omie/gv/contratos"
                       class="sidebar-sub {{ request()->is('omie/gv/contratos*') ? 'active' : '' }}">GV</a>
                </div>

                {{-- SERVIÇOS --}}
                <div class="space-y-1 mt-4">
                    <p class="text-xs text-[var(--muted)] mb-1">Serviços</p>

                    <a href="/omie/sv/servicos"
                       class="sidebar-sub {{ request()->is('omie/sv/servicos*') ? 'active' : '' }}">SV</a>

                    <a href="/omie/vs/servicos"
                       class="sidebar-sub {{ request()->is('omie/vs/servicos*') ? 'active' : '' }}">VS</a>

                    <a href="/omie/gv/servicos"
                       class="sidebar-sub {{ request()->is('omie/gv/servicos*') ? 'active' : '' }}">GV</a>
                </div>

                {{-- CATEGORIAS --}}
                <div class="space-y-1 mt-4">
                    <p class="text-xs text-[var(--muted)] mb-1">Categorias</p>

                    <a href="/omie/categorias/sv"
                       class="sidebar-sub {{ request()->is('omie/categorias/sv*') ? 'active' : '' }}">SV</a>

                    <a href="/omie/categorias/vs"
                       class="sidebar-sub {{ request()->is('omie/categorias/vs*') ? 'active' : '' }}">VS</a>

                    <a href="/omie/categorias/gv"
                       class="sidebar-sub {{ request()->is('omie/categorias/gv*') ? 'active' : '' }}">GV</a>
                </div>
                {{-- EMPRESAS --}}
                <div class="space-y-1 mt-4">
                    <p class="text-xs text-[var(--muted)] mb-1">Empresas</p>
                    <a href="/omie/sv/empresas"
                       class="sidebar-sub {{ request()->is('omie/sv/empresas*') ? 'active' : '' }}">SV</a>
                    <a href="/omie/vs/empresas"
                       class="sidebar-sub {{ request()->is('omie/vs/empresas*') ? 'active' : '' }}">VS</a>
                    <a href="/omie/gv/empresas"
                       class="sidebar-sub {{ request()->is('omie/gv/empresas*') ? 'active' : '' }}">GV</a>
                </div>
            </div>
        </nav>

        {{-- FOOTER --}}
        <div class="px-4 py-3 border-t text-xs text-[var(--muted)]">
            © {{ date('Y') }} Grupo Verreschi
        </div>

    </div>
</aside>





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

</script>
@endsection