

<style>
:root{
  --brand-from: #F9821A;
  --brand-to: #FC940D;
  --glass-bg: rgba(255,255,255,0.6);
  --muted: #6B7280;
  --card-radius: 1rem;
  --glass-border: rgba(0,0,0,0.04);
}

/* --- Layout & cards --- */
.card-glass{
  border-radius: var(--card-radius);
  backdrop-filter: blur(8px) saturate(1.05);
  background: linear-gradient(180deg, rgba(255,255,255,0.66), rgba(255,255,255,0.5));
  border: 1px solid var(--glass-border);
}
.dark .card-glass{
  background: linear-gradient(180deg, rgba(17,24,39,0.6), rgba(17,24,39,0.45));
  border: 1px solid rgba(255,255,255,0.04);
}

/* Header */
.page-title { letter-spacing: -0.02em; }

/* KPIs */
.stat-card{ transition: box-shadow .25s cubic-bezier(.2,.8,.2,1), transform .25s cubic-bezier(.2,.8,.2,1); }
.stat-card:hover{ transform: translateY(-6px); box-shadow: 0 10px 30px rgba(10,10,10,0.06); }
.stat-value { transition: transform .25s ease, color .2s ease; }

/* KPIs micro */
.kpi-trend { display:inline-flex; align-items:center; gap:.35rem; padding:.28rem .5rem; border-radius:999px; font-weight:700; font-size:.78rem; }
.kpi-up { background: rgba(34,197,94,0.12); color: #16a34a; }
.kpi-down { background: rgba(239,68,68,0.12); color: #ef4444; }

/* Table improvements */
.table-header {
  background: linear-gradient(90deg, var(--brand-from), var(--brand-to));
  color: white;
}
.table-modern thead th{ text-transform:uppercase; letter-spacing:0.06em; font-size:.72rem; position: sticky; top:0; z-index: 10; }
.table-modern tbody tr{ transition: background .12s ease, transform .12s ease; }
.table-modern tbody tr:hover{ transform: translateY(-1px); background: rgba(0,0,0,0.02); }
.total-col{ font-weight:700; color:var(--brand-from); }

/* Responsive scrollbar */
.scrollbar-x { overflow-x:auto; -webkit-overflow-scrolling:touch; scrollbar-width: thin; }

/* Sticky left cell */
.sticky-left { position: sticky; left:0; background: inherit; z-index:9; }

/* Compact badges and chips */
.kpi-chip{ background: linear-gradient(90deg,var(--brand-from),var(--brand-to)); color:white; padding:.25rem .6rem; border-radius:999px; font-weight:700; font-size:.75rem; }

/* subtle glass effect for small icons */
.icon-glass { background: rgba(255,255,255,0.6); border-radius: .5rem; padding:.45rem; }

/* Filters area */
.filter-bar .select, .filter-bar .search {
  min-width: 160px;
}

/* Tiny tooltip base (JS will position) */
.tooltip {
  position: absolute;
  background: rgba(17,24,39,0.95);
  color: #fff;
  padding: .45rem .6rem;
  border-radius: .5rem;
  font-size: .78rem;
  pointer-events: none;
  transform-origin: top center;
  transition: opacity .12s ease, transform .12s ease;
  opacity: 0;
  transform: translateY(-6px) scale(.98);
  z-index: 60;
}
.tooltip.show { opacity:1; transform: translateY(0) scale(1); }

/* Small responsive tweaks */
@media (max-width:1024px){
  .stat-value { font-size: 1.375rem; }
}
@media (max-width:640px){
  .filter-bar { flex-direction: column; gap:.5rem; align-items:stretch; }
  .filter-bar .select, .filter-bar .search { width:100%; }
}

/* table zebra for better scanning */
.table-modern tbody tr:nth-child(odd){ background: rgba(0,0,0,0.01); }
.dark .table-modern tbody tr:nth-child(odd){ background: rgba(255,255,255,0.02); }

/* subtle focus ring */
:focus { outline: none; box-shadow: 0 0 0 4px rgba(249,130,26,0.12); border-radius: .375rem; }
</style>

{{-- resources\views\dashboards\financeiro.blade.php --}}
@extends('layouts.app')

@section('content')

@php
  // Nota: variáveis Blade originais ($costs, $totals, etc.) são usadas sem alteração.
@endphp
<!--
<div class="max-w-7xl mx-auto px-6 py-8 space-y-8">

  {{-- HEADER --}}
  <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
    <div>
      <h1 class="page-title text-3xl md:text-4xl font-extrabold text-gray-900 dark:text-white">Painel Financeiro</h1>
      <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Panorama anual com profundidade — insights, anomalias e rankings.</p>
    </div>
    <div class="flex items-center gap-3">
      <div class="text-right">
        <div class="text-xs text-gray-500 dark:text-gray-400">Exibindo dados</div>
        <div class="mt-1 inline-flex items-center gap-2 px-4 py-2 rounded-lg card-glass border dark:border-gray-800">
          <svg class="w-5 h-5 text-orange-500" viewBox="0 0 24 24" fill="none"><path d="M3 12h18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
          <div class="text-sm font-medium">{{ date('Y') }}</div>
        </div>
      </div>
    </div>
  </div>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  {{-- FILTERS / CONTROLS --}}
  <div class="filter-bar flex flex-wrap items-center gap-3">
    <div class="flex items-center gap-2">
      <label class="text-sm text-gray-500 dark:text-gray-400">Mês</label>
      <select id="filterMonth" class="select rounded-md border card-glass px-3 py-2 text-sm">
        <option value="all">Todos</option>
        @foreach(['jan','fev','mar','abr','mai','jun','jul','ago','set','out','nov','dez'] as $m)
          <option value="{{ $m }}">{{ ucfirst($m) }}</option>
        @endforeach
      </select>
    </div>

    <div class="flex items-center gap-2">
      <label class="text-sm text-gray-500 dark:text-gray-400">Categoria</label>
      <select id="filterCategory" class="select rounded-md border card-glass px-3 py-2 text-sm">
        <option value="all">Todas</option>
        @foreach($costs->pluck('Categoria')->unique() as $cat)
          <option value="{{ str_replace(' ','_',$cat) }}">{{ $cat }}</option>
        @endforeach
      </select>
    </div>

    <div class="flex items-center gap-2 ml-auto flex-wrap">
      <input id="searchInput" class="search rounded-md border card-glass px-3 py-2 text-sm" placeholder="Pesquisar categoria..." />
      <button id="toggleCompact" class="px-3 py-2 rounded-md card-glass border hover:shadow-sm text-sm">Compactar</button>
      <button id="exportCsv" class="px-3 py-2 rounded-md bg-gradient-to-r from-[#F9821A] to-[#FC940D] text-white text-sm">Exportar CSV</button>
    </div>
  </div>

  {{-- TOP KPIs --}}
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
    {{-- CUSTOS TOTAIS --}}
    <div class="stat-card p-6 rounded-2xl card-glass shadow-sm">
      <div class="flex items-start justify-between">
        <div>
          <div class="text-xs uppercase text-gray-500 dark:text-gray-400 font-semibold">Custos Totais (Quadrimestral)</div>
          <div class="mt-3 flex items-baseline gap-3">
            <div class="text-2xl md:text-3xl font-extrabold text-gray-900 dark:text-white stat-value">R$ {{ number_format($totals['total_ano'], 2, ',', '.') }}</div>
            <div class="kpi-chip">Acumulado</div>
          </div>
        </div>
        <div class="ml-4 flex flex-col items-end">
          @php
            $last = end($totals['por_mes']);
            $prev = prev($totals['por_mes']) ?? $last;
            $pct = ($prev && $last) ? (($last - $prev)/max(1,$prev))*100 : 0;
          @endphp
          @if($pct >= 0)
            <div class="kpi-trend kpi-up">▲ {{ number_format($pct,1) }}%</div>
          @else
            <div class="kpi-trend kpi-down">▼ {{ number_format(abs($pct),1) }}%</div>
          @endif
          <div class="text-xs text-gray-400 mt-2">Último mês</div>
        </div>
      </div>
    </div>

    {{-- CATEGORIAS --}}
    <div class="stat-card p-6 rounded-2xl card-glass shadow-sm">
      <div class="flex items-start justify-between">
        <div>
          <div class="text-xs uppercase text-gray-500 dark:text-gray-400 font-semibold">Categorias Cadastradas</div>
          <div class="mt-3 text-2xl md:text-3xl font-extrabold text-blue-700 dark:text-blue-400">{{ $costs->count() }}</div>
          <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">itens recorrentes</div>
        </div>
        <div class="ml-4 flex items-center">
          <svg class="w-10 h-10 text-blue-500 icon-glass" viewBox="0 0 24 24" fill="none">
            <path d="M3 13h4v7H3zM10 7h4v13h-4zM17 2h4v18h-4z" fill="currentColor" opacity=".95"></path>
          </svg>
        </div>
      </div>
    </div>

    {{-- NOTAS FISCAIS --}}
    <div class="stat-card p-6 rounded-2xl card-glass shadow-sm">
      <div class="flex items-start justify-between">
        <div>
          <div class="text-xs uppercase text-gray-500 dark:text-gray-400 font-semibold">Notas Fiscais</div>
          <div class="mt-3 text-2xl md:text-3xl font-extrabold text-yellow-600">{{ $totals['invoices_count'] ?? 0 }}</div>
          <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">lançadas neste ano</div>
        </div>
        <div class="ml-4 flex items-center">
          <svg class="w-10 h-10 text-yellow-500 icon-glass" viewBox="0 0 24 24" fill="none">
            <path d="M4 4h16v2H4zM4 8h10v12H4z" fill="currentColor"></path>
          </svg>
        </div>
      </div>
    </div>

    {{-- CUSTO MÉDIO --}}
    <div class="stat-card p-6 rounded-2xl card-glass shadow-sm">
      <div class="flex items-start justify-between">
        <div>
          <div class="text-xs uppercase text-gray-500 dark:text-gray-400 font-semibold">Custo Médio / Categoria</div>
          <div class="mt-3 text-2xl md:text-3xl font-extrabold text-gray-900 dark:text-white">
            R$ @php
              $avgPerCategory = $costs->map(function($c){
                return collect([
                  $c->{'Pago jan'}, $c->{'Pago fev'}, $c->{'Pago mar'}, $c->{'Pago abr'}, $c->{'Pago mai'}, $c->{'Pago jun'},
                  $c->{'Pago jul'}, $c->{'Pago ago'}, $c->{'Pago set'}, $c->{'Pago out'}, $c->{'Pago nov'}, $c->{'Pago dez'}
                ])->sum();
              })->avg();
            @endphp
            {{ number_format($avgPerCategory, 2, ',', '.') }}
          </div>
          <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">média anual</div>
        </div>
        <div class="ml-4 flex items-center">
          <svg class="w-8 h-8 text-gray-400" viewBox="0 0 24 24" fill="none"><path d="M12 2v20" stroke="currentColor" stroke-width="1.5"/></svg>
        </div>
      </div>
    </div>
  </div>
  {{-- GRÁFICOS --}}
  <!--
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 p-6 rounded-2xl card-glass shadow-sm">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Evolução Mensal dos Custos</h2>
          <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Tendência, variação mensal e detecção de picos.</p>
        </div>
        <div class="flex items-center gap-3 text-sm text-gray-500 dark:text-gray-400">
          <span class="w-3 h-3 rounded-full" style="background:linear-gradient(90deg,var(--brand-from),var(--brand-to)); display:inline-block;"></span>
          Totais | <div class="px-2 py-1 rounded-md card-glass text-xs">Últimos 12 meses</div>
        </div>
      </div>
      <div class="mt-6 relative">
        <canvas id="costsChart" class="w-full h-72" aria-label="Gráfico de evolução mensal dos custos"></canvas>
        <div id="chartInsight" class="absolute top-4 right-4 bg-white/80 dark:bg-slate-800/80 p-3 rounded-md text-sm shadow-sm hidden">
          <div class="text-xs text-gray-500">Insight</div>
          <div id="insightText" class="font-medium text-gray-900 dark:text-white">—</div>
        </div>
      </div>
    </div>

    <div class="p-6 rounded-2xl card-glass shadow-sm">
      <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Top Categorias (Último Mês)</h3>
        <div class="text-sm text-gray-500 dark:text-gray-400">Último mês — Dez</div>
      </div>
      <div class="mt-6 h-72 flex items-center">
        <canvas id="categoryChart" class="w-full h-72" aria-label="Gráfico de barras por categoria"></canvas>
      </div>
    </div>
  </div>

  {{-- RANKING + TABELA RESUMO --}}
            -->

    
    <style>
        :root {
            --brand-from: #F9821A;
            --brand-to: #FC940D;
        }
        .glass-header {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
        }
        .dark .glass-header {
            background: rgba(31, 41, 55, 0.85);
        }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .custom-scrollbar::-webkit-scrollbar { width: 5px; height: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #E5E7EB; border-radius: 9999px; }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #374151; }
        .tabular-nums { font-variant-numeric: tabular-nums; }

        /* Modal styles */
        .modal-overlay {
            background: rgba(0,0,0,0.45);
            backdrop-filter: blur(6px);
        }
        .modal-card {
            background: white;
            border-radius: 12px;
            padding: 18px;
            width: 100%;
            max-width: 640px;
            box-shadow: 0 16px 50px rgba(2,6,23,0.4);
        }
        .status-chip {
            display:inline-flex;
            align-items:center;
            gap:8px;
            padding:6px 10px;
            border-radius:999px;
            font-weight:700;
            font-size:13px;
        }
    </style>
    <div class="max-w-7xl mx-auto px-6 py-8">

    <!-- TÍTULO / HEADER -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">
            Fluxos – Dashboard Financeiro
        </h1>
        <p class="mt-1 text-gray-500 dark:text-gray-400">
            Visualize de forma clara os lançamentos, pagamentos e tendências financeiras.
        </p>
    </div>
<div x-data="costsDetail()" x-init="init()" class="min-h-screen font-sans text-gray-900 antialiased bg-[#F3F4F6] dark:bg-[#0f1117] selection:bg-orange-100 selection:text-orange-600">

    <table class="w-full border-separate border-spacing-0 rounded-xl overflow-hidden shadow-sm
           bg-white/80 dark:bg-gray-900/60 backdrop-blur-sm ring-1 ring-gray-200/60 dark:ring-gray-700/50">

        <thead>
        <tr class="bg-gray-50/70 dark:bg-gray-800/70 border-b border-gray-200/60 dark:border-gray-700/60">
            @php
                function thSortable($label, $column) {
                    return "
                    <th @click=\"sortBy('$column')\"
                        class='sortable px-6 py-4 text-xs font-semibold text-gray-600 dark:text-gray-300 
                               tracking-wide uppercase cursor-pointer transition group select-none'>

                        <div class='flex items-center gap-1.5'>
                            <span class=\"th-label\">$label</span>

                            <span x-show=\"sort.column === '$column'\"
                                  class=\"text-[10px] text-[--brand-from] font-bold\">
                                <template x-if=\"sort.direction === 'asc'\">↑</template>
                                <template x-if=\"sort.direction === 'desc'\">↓</template>
                            </span>
                        </div>
                    </th>";
                }
            @endphp

            {!! thSortable('ID', 'id') !!}
            {!! thSortable('Categoria', 'Categoria') !!}
            {!! thSortable('Utilities (%)', 'Percentual') !!}
            {!! thSortable('Mês Atual', 'ValorAtual') !!}
            {!! thSortable('Total (Ano)', 'TotalPago') !!}
            {!! thSortable('Média', 'Media') !!}

            <th class="px-6 py-4"></th>
        </tr>
        </thead>

        <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">

@foreach($costs as $cost)

@php
    $difValor   = $cost->ValorAtual - $cost->MediaPagos;
$difPercent = $cost->MediaPagos > 0 ? ($difValor / $cost->MediaPagos) * 100 : 0;

@endphp

<tr class="group transition duration-200 hover:bg-gray-50/70 dark:hover:bg-gray-800/40">

    <td class="px-6 py-4 text-xs font-mono text-gray-400 dark:text-gray-600 group-hover:text-gray-500">
        #{{ str_pad($cost->id, 3, '0', STR_PAD_LEFT) }}
    </td>

    <td class="px-6 py-4">
        <div class="flex items-center gap-4">
            <div class="relative w-9 h-9 rounded-xl flex items-center justify-center
                        bg-gradient-to-br from-gray-100 to-gray-300 dark:from-gray-700 dark:to-gray-600
                        text-xs font-bold text-gray-700 dark:text-gray-100 shadow-inner 
                        group-hover:from-[--brand-from] group-hover:to-[--brand-to]
                        group-hover:text-white transition-all duration-500">
                {{ substr($cost->Categoria, 0, 1) }}
            </div>

            <div class="flex flex-col">
                <span class="text-sm font-semibold text-gray-900 dark:text-gray-100 
                             group-hover:text-[--brand-from]">
                    {{ $cost->Categoria }}
                </span>

                {{-- Comparativo FIXO / MENSAL --}}
                <span class="text-xs font-semibold
                    @if($difValor <= 0) text-green-600 dark:text-green-400 
                    @else text-red-600 dark:text-red-400 @endif">
                    {{ number_format($difPercent, 1) }}%
                    @if($difValor <= 0)
                        abaixo da média
                    @else
                        acima da média
                    @endif
                </span>
            </div>
        </div>
    </td>

    <td class="px-6 py-4">
        <div class="max-w-[200px]">
            <div class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 mb-1.5">
                {{ number_format($cost->Percentual, 2) }}%
            </div>

            <div class="h-1.5 w-full bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                <div class="h-full rounded-full bg-gradient-to-r from-[--brand-from] to-[--brand-to]"
                     style="width: {{ $cost->Percentual }}%"></div>
            </div>
        </div>
    </td>

    <td class="px-6 py-4 text-right">
        <span class="text-sm font-medium tabular-nums">
            R$ {{ number_format($cost->ValorAtual, 2, ',', '.') }}
        </span>
    </td>

    <td class="px-6 py-4 text-right">
        <span class="text-sm font-semibold tabular-nums">
            R$ {{ number_format($cost->TotalPago, 2, ',', '.') }}
        </span>
    </td>

    <td class="px-6 py-4 text-right leading-tight">
        <span class="text-sm font-semibold tabular-nums">
            R$ {{ number_format($cost->Media, 2, ',', '.') }}
            <span class="text-[10px] text-gray-400">/12 meses</span>
        </span>
        <br>
        <span class="text-xs text-gray-500 dark:text-gray-400 tabular-nums">
            R$ {{ number_format($cost->MediaPagos, 2, ',', '.') }}
            <span class="text-[10px] text-gray-400">/pagos</span>
        </span>
    </td>

    <td class="px-6 py-4 text-center">
        <button 
            @click="openDetail({ 
                    id: {{ $cost->id }},
                    categoria: '{{ $cost->Categoria }}',
                    valorAtual: {{ $cost->ValorAtual }},
                    media: {{ $cost->Media }},
                    difValor: {{ $difValor }},
                    difPercent: {{ $difPercent }}
            })"
            class="p-2 rounded-lg text-gray-400 transition hover:bg-orange-50 dark:hover:bg-orange-900/20">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
        </button>
    </td>

</tr>

@endforeach
<script>
function costsDetail() {
    return {
        modalOpen: false,
        detail: {},

        openDetail(data) {
            this.detail = data;
            this.modalOpen = true;
        }
    };
}

document.addEventListener("alpine:init", () => {
    Alpine.data("costsDetail", costsDetail);
});
</script>

</tbody>

    </table>

    <!-- 
<div 
    x-show="modalOpen"
    class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center p-6"
    x-transition
>
    <div class="bg-white dark:bg-gray-900 p-6 rounded-xl shadow-xl w-full max-w-md"
         @click.away="modalOpen=false">

        <h2 class="text-lg font-bold mb-3 text-gray-900 dark:text-gray-100">
            Detalhes — <span x-text="detail.categoria"></span>
        </h2>

        <p class="text-sm text-gray-600 dark:text-gray-300">
            Valor atual:
            <strong>R$ <span x-text="detail.valorAtual.toFixed(2)"></span></strong>
        </p>

        <p class="text-sm text-gray-600 dark:text-gray-300">
            Média mensal:
            <strong>R$ <span x-text="detail.media.toFixed(2)"></span></strong>
        </p>

        <p class="mt-3 text-sm"
           :class="detail.difValor <= 0 ? 'text-green-600' : 'text-red-600'">
            <span x-text="detail.difPercent.toFixed(1)"></span>% —
            <span x-text="detail.difValor <= 0 ? 'Abaixo da média' : 'Acima da média'"></span>
        </p>

        <button 
            @click="modalOpen=false"
            class="mt-6 w-full py-2 rounded-lg bg-gray-800 text-white hover:bg-gray-700">
            Fechar
        </button>
    </div>
</div>

    </div>
-->

<script>


        closeModal() {
            this.showModal = false;
        },

        // INIT
        init() {
            this.tbodyEl = this.$el.querySelector("tbody");
            if (!this.tbodyEl) return;

            const trEls = Array.from(this.tbodyEl.querySelectorAll("tr"));
            this.rows = trEls.map((tr, index) => ({
                el: tr,
                cellsText: Array.from(tr.querySelectorAll("td"))
                    .map(td => td.innerText.trim()),
                originalIndex: index
            }));
        },

        _normalizeValue(text) {
            if (!text) return "";

            text = text.trim();

            if (text.includes("%")) {
                const n = text
                    .replace('%', '')
                    .replace(/\s/g, '')
                    .replace(',', '.')
                    .replace(/[^\d.-]/g, '');
                return parseFloat(n) || 0;
            }

            if (text.includes("R$")) {
                const n = text
                    .replace(/R\$\s?/, '')
                    .replace(/\./g, '')
                    .replace(/,/g, '.')
                    .replace(/[^\d.-]/g, '');
                return parseFloat(n) || 0;
            }

            const maybe = text
                .replace(/\./g, '')
                .replace(/,/g, '.')
                .replace(/[^\d.-]/g, '');

            if (!isNaN(maybe) && maybe !== '') return parseFloat(maybe);

            return text.toLowerCase();
        },

        // SORT FIXED — preserves Alpine
        sortBy(columnKey) {
            const idx = this.colIndexMap[columnKey];
            if (idx === undefined) return;

            if (this.sort.column === columnKey) {
                this.sort.direction = this.sort.direction === "asc" ? "desc" : "asc";
            } else {
                this.sort.column = columnKey;
                this.sort.direction = "asc";
            }

            const dir = this.sort.direction === "asc" ? 1 : -1;

            this.rows.sort((a, b) => {
                const aVal = this._normalizeValue(a.cellsText[idx]);
                const bVal = this._normalizeValue(b.cellsText[idx]);

                if (aVal === bVal) return a.originalIndex - b.originalIndex;

                return aVal > bVal ? dir : -dir;
            });

            // Preserve Alpine.js + event listeners
            this.tbodyEl.replaceChildren(...this.rows.map(r => r.el));

            // Refresh cached text
            this.rows.forEach(r => {
                r.cellsText = Array.from(r.el.querySelectorAll("td"))
                    .map(td => td.innerText.trim());
            });
        }

    };
}
</script>


</div>


<!--

            </div>
        </div>
    </div>
--
    {{-- MODAL REFINADO --}}
    <div 
        x-show="showModal"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
        role="dialog"
        aria-modal="true"
    >
        {{-- Backdrop com Blur Intenso --}}
        <div 
            x-show="showModal"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 backdrop-blur-none"
            x-transition:enter-end="opacity-100 backdrop-blur-sm"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 backdrop-blur-sm"
            x-transition:leave-end="opacity-0 backdrop-blur-none"
            class="absolute inset-0 bg-gray-900/30 dark:bg-black/60 backdrop-blur-sm"
            @click="showModal = false"
        ></div>

        {{-- Container Modal --}}
        <div 
            x-show="showModal"
            x-transition:enter="transition ease-out duration-300 cubic-bezier(0.16, 1, 0.3, 1)"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200 cubic-bezier(0.16, 1, 0.3, 1)"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-4"
            class="relative w-full max-w-2xl bg-white dark:bg-[#1C1C1E] rounded-2xl shadow-2xl ring-1 ring-black/5 dark:ring-white/10 overflow-hidden transform"
        >
            
            {{-- Modal Header --}}
            <div class="px-8 py-6 border-b border-gray-100 dark:border-white/10 flex justify-between items-start bg-white/80 dark:bg-[#1C1C1E]/80 backdrop-blur-md sticky top-0 z-20">
                <div>
                    <span class="block text-[11px] font-bold tracking-widest text-[--brand-from] uppercase mb-1">Detalhamento Financeiro</span>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight" x-text="detail.categoria"></h3>
                </div>
                <button 
                    @click="showModal = false" 
                    class="rounded-full p-2 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/10 transition-colors focus:outline-none focus:ring-2 focus:ring-[--brand-from]"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-8">
                {{-- Grid de KPIs --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                    <div class="group relative overflow-hidden rounded-xl bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-white/5 p-5 transition-all hover:shadow-md hover:border-[--brand-from]/30">
                        <div class="absolute top-0 right-0 p-3 opacity-10 group-hover:opacity-20 transition-opacity">
                             <svg class="w-12 h-12 text-[--brand-from]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        </div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Média Mensal</p>
                        <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white tabular-nums">
                            R$ <span x-text="detail.average ? detail.average.toLocaleString('pt-BR', {minimumFractionDigits: 2}) : '0,00'"></span>
                        </p>
                    </div>

            
<div class="rounded-xl bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-white/5 p-5 flex flex-col items-center justify-center">
    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Ajustes / Variação Mês</p>
    <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white tabular-nums">
        R$ <span x-text="detail.ajustes ? detail.ajustes.toLocaleString('pt-BR', {minimumFractionDigits: 2}) : '0,00'"></span>
    </p>
    <p class="mt-1 text-sm font-medium"
       :class="detail.percentChange > 0 ? 'text-red-500' : (detail.percentChange < 0 ? 'text-green-500' : 'text-gray-400')"
       x-text="detail.percentChange > 0 
                ? '+' + detail.percentChange + '% em relação ao mês anterior' 
                : (detail.percentChange < 0 
                    ? detail.percentChange + '% redução em relação ao mês anterior' 
                    : 'Sem alteração')">
    </p>
</div>


                    <div class="rounded-xl bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-white/5 p-5 flex flex-col justify-center">
                         <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">ID do Registro</p>
                        <p class="mt-2 text-2xl font-mono text-gray-700 dark:text-gray-300">
                            #<span x-text="detail.id"></span>
                        </p>
                    </div>
                </div>

                {{-- Lista de Meses com Scroll estilizado --}}
                <div class="relative">
                    <div class="absolute inset-x-0 top-0 h-4 bg-gradient-to-b from-white dark:from-[#1C1C1E] to-transparent z-10 pointer-events-none"></div>
                    
                    <div class="max-h-[320px] overflow-y-auto pr-2 custom-scrollbar">
                        <table class="w-full text-sm">
                            <thead class="sticky top-0 bg-white dark:bg-[#1C1C1E] z-10">
                                <tr class="border-b border-gray-100 dark:border-white/10">
                                    <th class="pb-3 text-left pl-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Mês de Referência</th>
                                    <th class="pb-3 text-right pr-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Gasto Mensal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-white/5">
                                <template x-for="(value, month) in detail.months" :key="month">
                                    <tr class="group hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                                        <td class="py-3.5 pl-4 font-medium text-gray-600 dark:text-gray-300 capitalize group-hover:text-[--brand-to] transition-colors" x-text="month"></td>
                                        <td class="py-3.5 pr-4 text-right font-bold text-gray-900 dark:text-white tabular-nums">
                                            R$ <span x-text="value.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

    {{-- Script Alpine (Lógica Original Mantida) --}}
    <script>
        function costsDetail() {
            return {
                showModal: false,
                detail: { months: [], average: 0, ajustes: 0, categoria: '' },
                openDetail(id) {
                    this.detail = { months: [], average: 0, ajustes: 0, categoria: 'Carregando...' };
                    this.showModal = true;

                    fetch(`/costs/${id}`)
                        .then(res => res.json())
                        .then(data => {
                            this.detail = data;
                        })
                        .catch(err => {
                            console.error('Erro:', err);
                            this.detail.categoria = 'Erro ao carregar';
                        });
                }
            }
        }
        
        // Fechar modal com ESC
        document.addEventListener('keydown', function(event) {
            if (event.key === "Escape") {
                let xData = document.querySelector('[x-data="costsDetail()"]');
                if(xData && xData.__x) {
                    xData.__x.$data.showModal = false;
                }
            }
        });
    </script>
</div>


    {{-- Modal refinado --}}

    {{-- Botão que abre o modal --}}
    

    {{-- Modal --}}
    {{-- Botões de abrir modal dentro da tabela Blade --}}


{{-- Modal único no final do HTML --}}
<div x-data="costsDetail()" class="relative">
    <div 
        x-show="showModal"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 p-4"
        style="display: none;"
    >
        <div class="bg-white dark:bg-gray-800 rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden relative">
            {{-- Cabeçalho --}}
            <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                    Detalhes - <span x-text="detail.categoria || '-'"></span>
                </h3>
                <button @click="closeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                    ✕
                </button>
            </div>

            {{-- Conteúdo --}}
            <div class="px-6 py-4 max-h-[400px] overflow-y-auto space-y-4">
                <div class="grid grid-cols-2 gap-4 text-sm text-gray-700 dark:text-gray-300">
                    <div><span class="font-semibold">ID:</span> <span x-text="detail.id || '-'"></span></div>
                    <div><span class="font-semibold">Ajustes:</span> R$ <span x-text="formatCurrency(detail.ajustes)"></span></div>
                    <div class="col-span-2"><span class="font-semibold">Custo Médio:</span> R$ <span x-text="formatCurrency(detail.average)"></span></div>
                </div>

                <div class="overflow-x-auto mt-4">
                    <table class="w-full text-sm rounded-xl overflow-hidden border border-gray-200/60 dark:border-white/10 shadow-sm">
                        <thead class="bg-gray-50/80 dark:bg-gray-800/50 backdrop-blur-lg border-b border-gray-200 dark:border-gray-700">
                            <tr>
                                <th class="p-3 text-left font-semibold text-gray-700 dark:text-gray-200">Mês</th>
                                <th class="p-3 text-right font-semibold text-gray-700 dark:text-gray-200">Valor (R$)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(value, month) in detail.months || {}" :key="month">
                                <tr class="border-b border-gray-100 dark:border-gray-800">
                                    <td class="p-3 font-medium text-gray-800 dark:text-gray-100" x-text="month"></td>
                                    <td class="p-3 text-right font-semibold text-gray-700 dark:text-gray-200" x-text="formatCurrency(value)"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 p-4 rounded-xl bg-gradient-to-br from-gray-50 to-white dark:from-white/10 dark:to-white/5 border border-gray-200/60 dark:border-white/10 shadow-inner">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Média dos meses pagos:</p>
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mt-1" x-text="'R$ ' + formatCurrency(detail.average)"></h4>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                <button @click="closeModal()" class="w-full px-4 py-2 rounded-xl font-semibold bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-100 hover:bg-gray-300 dark:hover:bg-gray-600 transition shadow-md hover:shadow-lg">
                    Fechar
                </button>
            </div>
        </div>
    </div>

    <script>
        function costsDetail() {
            return {
                showModal: false,
                detail: {},
                openDetail(id) {
                    if (!id) return;
                    fetch(`/costs/${id}`)
                        .then(res => res.json())
                        .then(data => {
                            this.detail = data;
                            this.showModal = true;
                        })
                        .catch(err => console.error('Erro ao carregar detalhes:', err));
                },
                closeModal() {
                    this.showModal = false;
                    this.detail = {};
                },
                formatCurrency(value) {
                    if (typeof value !== 'number') return '0,00';
                    return value.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }
            }
        }
    </script>
</div>

</div>


</div>
-->

<style>
:root{
  --brand-from: #ff7a00;
  --brand-to: #ff9a00;
  --brand-soft: rgba(255,140,60,0.15);
  --brand-hover: rgba(255,140,60,0.25);

  --glass-bg: rgba(255,255,255,0.60);
  --glass-border: rgba(255,120,20,0.12);

  --muted: #6B7280;
  --text: #1F2937;
  --card-radius: 1rem;
}

/* ---------- TABLE WRAPPER (GLASS CARD) ----------- */

.table-glass{
  backdrop-filter: blur(14px) saturate(1.20);
  background: var(--glass-bg);
  border-radius: var(--card-radius);

  border: 1px solid var(--glass-border);
  box-shadow: 0 8px 32px -8px rgba(0,0,0,0.20);
  overflow: hidden;
}

/* ---------- TABLE BASE STYLING ---------- */

.table-premium{
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
}

.table-premium tbody tr{
  transition: background-color .22s ease, transform .22s ease;
  border-bottom: 1px solid rgba(0,0,0,.04);
}

.table-premium tbody tr:hover{
  background-color: var(--brand-soft);
  transform: translateX(3px);
}

.table-premium td{
  padding: 12px 16px;
  font-size: .87rem;
  color: var(--text);
  white-space: nowrap;
  transition: color .2s ease;
}

/* clickable values */
.table-premium td[data-clickable]{
  cursor: pointer;
  font-weight: 500;
}

.table-premium td[data-clickable]:hover{
  color: var(--brand-from);
  text-decoration: underline;
}

/* ---------- STICKY COLUMNS ---------- */

td.sticky-cell, td.sticky-cell-2 {
   z-index: 5;
}


/* value highlighting */
.table-premium td.total{
  font-weight: 700;
  color: var(--brand-from);
}

.table-premium td.avg{
  color: var(--muted);
}

/* ---------- TABLE HEADER ---------- */

.table-premium th{
  padding: 12px 16px;
  font-size: .86rem;
  font-weight: 600;
  color: var(--text);

  background: #F9821A;;
  backdrop-filter: blur(12px);

  border-bottom: 1px solid rgba(255,120,20,0.10);
  white-space: nowrap;
}

/* ---------- MODAL ---------- */

.modal-overlay{
  background: rgba(0,0,0,0.55);
  backdrop-filter: blur(8px);
}

.modal-card{
  background: white;
  border-radius: var(--card-radius);
  box-shadow: 0 10px 40px -8px rgba(0,0,0,0.35);
  padding: 26px;
  width: 100%;
  max-width: 440px;

  animation: modalIn .25s ease-out;
}

@keyframes modalIn{
  from{transform: scale(.92) translateY(10px); opacity:0;}
  to{transform: scale(1) translateY(0); opacity:1;}
}

/* ---------- INPUTS ---------- */

.modal-card input{
  border: 1px solid rgba(0,0,0,.12);
  border-radius: .55rem;
  font-size: .92rem;

  padding: 10px 12px;
  width: 100%;
  transition: border .2s, box-shadow .2s;
}

.modal-card input:focus{
  outline: none;
  border-color: var(--brand-from);
  box-shadow: 0 0 0 3px var(--brand-hover);
}

/* ---------- BUTTONS ---------- */

.btn-primary{
  background: linear-gradient(90deg,var(--brand-from), var(--brand-to));
  color: white;
  border-radius: .55rem;

  padding: 10px 18px;
  font-size: .85rem;
  font-weight: 600;
  transition: opacity .2s;
}

.btn-primary:hover{
  opacity: .90;
}

.btn-secondary{
  background: rgba(0,0,0,.05);
  color: var(--text);

  border-radius: .55rem;
  padding: 10px 18px;
  font-size: .85rem;
  font-weight: 500;
}
/* ====== SCROLLBAR PREMIUM (LARANJA F9821A) ====== */

.table-glass {
    overflow-x: auto;
    scrollbar-width: thin;
    scrollbar-color: #F9821A rgba(0,0,0,0.1);
}

/* Firefox */
.table-glass::-webkit-scrollbar {
    height: 10px; /* altura da barra */
}

.table-glass::-webkit-scrollbar-track {
    background: rgba(0,0,0,0.05);
    border-radius: 6px;
}

.table-glass::-webkit-scrollbar-thumb {
    background: #F9821A;     /* cor da barra */
    border-radius: 6px;
}

.table-glass::-webkit-scrollbar-thumb:hover {
    background: #ff9a3b;     /* hover mais claro */
}

.table-premium td:not(.sticky):not(.sticky-cell):not(.sticky-cell-2) {
    position: relative;
    z-index: 20;
}


</style>


   <!-- <div x-data="costsTable()" class="max-w-7xl mx-auto px-6 py-8">

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-8">

        <div>
            <h1 class="text-3xl font-semibold text-gray-900 dark:text-white tracking-tight">
                Custos Mensais
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Visão geral de fornecedores, gastos e tendências
            </p>
        </div>

        <div class="flex items-center gap-3 w-full md:w-auto">

            <div class="relative w-full md:w-56">
                <input 
                    x-model="search"
                    type="text"
                    placeholder="Buscar categoria..."
                    class="w-full px-3 py-2 pl-9 rounded-xl bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border text-sm focus:ring-2 focus:ring-primary/70 focus:border-primary/70 outline-none transition"
                >
                <svg class="absolute left-2 top-2.5 w-4 h-4 text-gray-400 dark:text-gray-500" fill="none"
                     stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="m21 21-6-6m2-5a7 7 0 1 1-14 0 7 7 0 0 1 14 0z" />
                </svg>
            </div>

            <select 
                x-model="filterEmpresa"
                class="px-3 py-2 rounded-xl bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border focus:ring-2 focus:ring-primary/70 text-sm w-full md:w-44 transition"
            >
                <option value="">Empresa (CNPJ)</option>

                @foreach($empresas as $e)
                    <option value="{{ $e }}">{{ $e }}</option>
                @endforeach
            </select>
            <button
        class="px-4 py-2 text-sm font-semibold rounded-xl text-white hover:opacity-90 transition
               bg-gradient-to-r from-[var(--brand-from)] to-[var(--brand-to)]
               shadow-sm whitespace-nowrap"
        @click="window.location='/financeiro/lancamentos/novo';"
    >
        + Novo Custo
    </button>

        </div>
    </div>




    

<div x-data="costsDetail()" class="relative">

    <div class="table-glass overflow-x-auto">
<table class="table-premium">

    <thead>
        <tr>
            <th class="sticky left-0 z-20 px-4 py-4 bg-inherit">Empresa</th>
            <th class="sticky left-40 z-20 px-4 py-4 bg-inherit">Categoria</th>
            <th class="sticky left-80 z-20 px-4 py-4 bg-inherit">Custo Fixo</th>

            @foreach(['jan','fev','mar','abr','mai','jun','jul','ago','set','out','nov','dez'] as $m)
                <th class="px-4 py-4 text-right">{{ strtoupper($m) }}</th>
            @endforeach

            <th class="px-4 py-4 text-right">Total</th>
            <th class="px-4 py-4 text-right">Média</th>
        </tr>
    </thead>

    <tbody>

        @foreach($costs as $c)

        @php
            $rowVals = [
                $c->{'Pago jan'}, $c->{'Pago fev'}, $c->{'Pago mar'}, $c->{'Pago abr'},
                $c->{'Pago mai'}, $c->{'Pago jun'}, $c->{'Pago jul'}, $c->{'Pago ago'},
                $c->{'Pago set'}, $c->{'Pago out'}, $c->{'Pago nov'}, $c->{'Pago dez'},
            ];

            $total = collect($rowVals)->sum();
            $avg   = $total / 12;
            $custoFixo = $avg;
        @endphp

        <tr>

            <td class="sticky left-0 px-4 py-3 text-gray-800 font-medium bg-white">
                {{ $c->cnpj ?? '—' }}
            </td>

            <td class="sticky left-40 px-4 py-3 font-medium bg-white">
                {{ $c->Categoria ?? $c->categoria }}
            </td>

            <td class="sticky left-80 px-4 py-3 text-right font-semibold text-blue-600 bg-white">
                {{ number_format($custoFixo, 2, ',', '.') }}
            </td>

            @foreach(['jan','fev','mar','abr','mai','jun','jul','ago','set','out','nov','dez'] as $m)

                @php
                    $val = $c->{'Pago '.$m};

                    $percent = $custoFixo > 0
                        ? (($val - $custoFixo) / $custoFixo) * 100
                        : 0;

                    $colorClass = $val <= $custoFixo
                        ? 'text-green-600 font-semibold'
                        : 'text-red-600 font-semibold';
                @endphp

                <td
                    class="text-right cursor-pointer hover:bg-black/5 transition {{ $colorClass }}"
                    @click="openNotaModal('{{ $c->id }}','{{ $c->Categoria }}','{{ $val }}','{{ $m }}')"
                >
                    {{ number_format($val, 2, ',', '.') }}

                    <span class="text-xs opacity-70 ml-1">
                        ({{ number_format($percent, 1, ',', '.') }}%)
                    </span>
                </td>

            @endforeach

            <td class="text-right font-semibold">
                {{ number_format($total, 2, ',', '.') }}
            </td>

            <td class="text-right">
                {{ number_format($avg, 2, ',', '.') }}
            </td>

        </tr>

        @endforeach
    </tbody>

</table>


    </div>


    <div 
        x-show="showModal"
        x-transition.opacity
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
    >
        <div class="modal-card space-y-6">

            <h2 class="text-lg font-semibold text-gray-800">
                <span x-text="categoria"></span> – <span x-text="month"></span>
            </h2>

            <div class="text-sm text-gray-500">
                <strong>Valor atual:</strong> R$ <span x-text="valor_atual"></span>
            </div>

            <template x-if="file_url">
                <a :href="file_url" target="_blank" class="text-brand underline text-sm font-medium">
                    Ver Anexo
                </a>
            </template>

            <form @submit.prevent="saveNota" enctype="multipart/form-data">

                <div class="space-y-2">
                    <label class="text-sm text-gray-500">Novo valor</label>
                    <input 
                        type="number"
                        step="0.01"
                        class="w-full p-2 rounded border"
                        x-model="valor"
                    >
                </div>
                <div class="space-y-2">
    <label class="text-sm text-gray-500">Status</label>
    <select 
        x-model="status"
        class="w-full p-2 rounded border bg-white"
    >
        <option value="1">Pago</option>
        <option value="0">Pendente</option>
    </select>
</div>


                <div class="space-y-2">
                    <label class="text-sm text-gray-500">Arquivo (opcional)</label>
                    <input 
                        type="file" 
                        @change="file = $event.target.files[0]"
                    >
                </div>

                <div class="flex justify-end gap-3 pt-3">

                    <button 
                        type="button"
                        class="btn-secondary"
                        @click="closeModal"
                    >
                        Cancelar
                    </button>

                    <button 
                        type="submit"
                        class="btn-primary"
                    >
                        Salvar
                    </button>

                </div>

            </form>
        </div>
    </div>
</div>


<script src="//unpkg.com/alpinejs" defer></script>

<script src="//unpkg.com/alpinejs" defer></script>

<script>
/*
  Script único com dois Alpine components:
   - costsTable() -> controla filtros client-side (search por iniciais + filterEmpresa)
   - costsDetail() -> mantém toda a lógica do modal (GET/POST/status/file) como já estava
*/

/* -------------------------
   costsTable: filtros client
   ------------------------- */
document.addEventListener('alpine:init', () => {
  Alpine.data('costsTable', () => ({
    search: '',
    filterEmpresa: '',
    _debounceTimer: null,

    init() {
      // aplica filtro inicial
      this.applyFilter();

      // observa mudanças reativas no Alpine
      this.$watch('search', () => {
        // debounce 220ms
        clearTimeout(this._debounceTimer);
        this._debounceTimer = setTimeout(() => this.applyFilter(), 220);
      });

      this.$watch('filterEmpresa', () => {
        // filtro imediato (select)
        this.applyFilter();
      });
    },

    applyFilter() {
      // pega todas as linhas do tbody da tabela dentro deste escopo
      // garantimos que a função seja robusta caso existam múltiplas tabelas
      const tbody = document.querySelectorAll('[x-data="costsDetail()"] table.table-premium tbody');
      if (!tbody || tbody.length === 0) return;

      // compõe termos normalizados
      const searchTerm = (this.search || '').toString().trim().toLowerCase();
      const empresaTerm = (this.filterEmpresa || '').toString().trim();

      tbody.forEach(tb => {
        tb.querySelectorAll('tr').forEach(row => {
          // primeira coluna = categoria, segunda = cnpj (conforme seu HTML)
          const tdCategoria = row.querySelector('td:nth-child(1)');
          const tdCnpj = row.querySelector('td:nth-child(2)');

          const categoriaText = tdCategoria ? tdCategoria.textContent.trim().toLowerCase() : '';
          const cnpjText = tdCnpj ? tdCnpj.textContent.trim() : '';

          // Search por INICIAIS: startsWith
          const matchesSearch = searchTerm === '' 
            ? true 
            : categoriaText.startsWith(searchTerm);

          // Empresa filter: if empty match all, else exact match (trim)
          const matchesEmpresa = empresaTerm === '' 
            ? true 
            : cnpjText === empresaTerm;

          const shouldShow = matchesSearch && matchesEmpresa;

          row.style.display = shouldShow ? '' : 'none';
        });
      });
    }
  }));

  /* -------------------------
     costsDetail: modal + notas
     (mantive a lógica que você já usa,
      apenas consolidei aqui para garantir
      que ambos os componentes existam)
     ------------------------- */
  Alpine.data('costsDetail', () => ({

    showModal: false,
    costId: null,
    categoria: null,
    month: null,
    valor: null,
    valor_atual: null,
    file_url: null,
    file: null,
    status: 1, // default

    // ============================
    // OPEN MODAL
    // ============================
    openNotaModal(id, categoria, valor_atual, month){
        this.costId       = id;
        this.categoria    = categoria;
        this.valor_atual  = valor_atual;
        this.month        = month;
        this.valor        = valor_atual;
        this.file         = null;
        this.file_url     = null;
        this.status       = 1; // reset padrão

        // carrega dados do servidor e só depois abre (evita flash / loops)
        this.loadNota().then(() => {
          this.showModal = true;
        }).catch(() => {
          // mesmo em erro, abre modal com valores atuais (opcional)
          this.showModal = true;
        });
    },

    // ============================
    // CLOSE MODAL
    // ============================
    closeModal(){
        this.showModal = false;
    },

    // ============================
    // LOAD NOTA (GET)
    // ============================
    async loadNota(){
        try {
            const tokenTag = document.querySelector('meta[name=csrf-token]');
            const csrf = tokenTag ? tokenTag.getAttribute('content') : '';

            let r = await fetch(`/financeiro/notas/${this.costId}/${this.month}`, {
                headers: { 'X-CSRF-TOKEN': csrf }
            });

            if(!r.ok) {
                // não alterar estado se request falhar
                console.error('GET /financeiro/notas returned', r.status);
                return;
            }

            let data = await r.json();

            // atualiza apenas os campos esperados
            this.valor_atual = (data.valor !== undefined && data.valor !== null) ? data.valor : this.valor_atual;
            this.valor       = (data.valor !== undefined && data.valor !== null) ? data.valor : this.valor_atual;
            this.file_url    = data.file_url ?? null;
            this.status      = (data.status !== undefined && data.status !== null) ? Number(data.status) : 1;

        } catch(e){
            console.error('Erro ao carregar nota:', e);
            throw e;
        }
    },

    // ============================
    // SAVE NOTA (POST)
    // ============================
    async saveNota(){
        const tokenTag = document.querySelector('meta[name=csrf-token]');
        const csrf = tokenTag ? tokenTag.getAttribute('content') : '';

        const form = new FormData();
        form.append('valor', this.valor ?? 0);
        form.append('status', this.status ?? 1);

        if (this.file) {
            form.append('file', this.file);
        }

        try {
            const r = await fetch(`/financeiro/notas/${this.costId}/${this.month}`, {
                method: "POST",
                headers: { 'X-CSRF-TOKEN': csrf },
                body: form,
            });

            if (!r.ok) {
              console.error('POST /financeiro/notas returned', r.status);
              alert('Erro ao salvar (servidor retornou erro).');
              return;
            }

            this.closeModal();
            // atualiza a tabela — você já recarregava a página antes, mantive a reload
            window.location.reload();

        } catch(e){
            console.error(e);
            alert("Erro ao salvar nota");
        }
    },

  }));
});
</script>






    </div>

</div>

    </div>
  </div>

</div>

{{-- Tooltip --}}
<div id="tooltip" class="tooltip" role="tooltip" aria-hidden="true"></div>

{{-- Scripts de charts e AJAX --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  let chart1 = null;
  let chart2 = null;

  function currencyBR(value){
      return 'R$ ' + Number(value).toLocaleString('pt-BR', { minimumFractionDigits:2, maximumFractionDigits:2 });
  }

  function renderCharts(totals, costs){
      const monthlyData = Object.values(totals.por_mes);
      const monthlyLabels = ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'];
      const categoryLabels = costs.map(c => c.Categoria);
      const categoryData = costs.map(c => c['Pago dez']);

      if(chart1) chart1.destroy();
      if(chart2) chart2.destroy();

      /* LINE CHART */
      chart1 = new Chart(document.getElementById('costsChart').getContext('2d'), {
        type:'line',
        data:{ labels:monthlyLabels, datasets:[{ label:'Total Mensal (R$)', data:monthlyData, borderWidth:3, borderColor:'rgba(249,130,26,0.98)', pointRadius:3, tension:0.36, fill:true }]},
        options:{ maintainAspectRatio:false }
      });

      /* BAR CHART */
      chart2 = new Chart(document.getElementById('categoryChart').getContext('2d'), {
        type:'bar',
        data:{ labels:categoryLabels, datasets:[{ label:'Valor (R$)', data:categoryData, borderRadius:8, barThickness:14 }]},
        options:{ indexAxis:'y', maintainAspectRatio:false }
      });
  }

  document.getElementById('filterMonth').addEventListener('change', function(){
    fetch(`{{ url('/financeiro') }}?month=${this.value}`, { headers:{'X-Requested-With':'XMLHttpRequest'} })
      .then(r => r.json())
      .then(data => {
        document.getElementById('totalAno').textContent = currencyBR(data.totals.total_ano);
        const table = document.getElementById('tableData'); table.innerHTML = '';
        data.costs.forEach(c=>{
          table.innerHTML += `<tr><td>${c.Categoria}</td><td>${currencyBR(c['Pago dez'])}</td></tr>`;
        });
        renderCharts(data.totals, data.costs);
      });
  });

  renderCharts(@json($totals), @json($costs));
  cat.startsWith(s)

</script>
<script>
function costsTable() {
    return {
        search: '',
        filterEmpresa: '',
        
        filter(cat, emp) {

            let s = this.search.toLowerCase().trim();

            // busca prefix
            if (s && !cat.startsWith(s)) return false;

            // filtro empresa
            if (this.filterEmpresa && emp !== this.filterEmpresa) return false;

            return true;
        }
    }
}
</script>


@endsection
