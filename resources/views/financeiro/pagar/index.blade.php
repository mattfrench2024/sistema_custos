@extends('layouts.app')

@section('content')


<meta name="csrf-token" content="{{ csrf_token() }}">
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
[x-cloak] {
  display: none !important;
}

/* ===== FIX STICKY GAP ===== */

/* larguras fixas das colunas */
.col-empresa {
  width: 220px;
  min-width: 220px;
}

.col-categoria {
  width: 240px;
  min-width: 240px;
}

.col-custo {
  width: 160px;
  min-width: 160px;
}

/* posições corretas */
.sticky-empresa {
  left: 0;
}

.sticky-categoria {
  left: 220px; /* largura da empresa */
}

.sticky-custo {
  left: 460px; /* 220 + 240 */
}

</style>
@php
$months = ['jan','fev','mar','abr','mai','jun','jul','ago','set','out','nov','dez'];
$labels = ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'];

$monthlyTotals = array_fill_keys($months, 0);

foreach ($costs as $c) {
    foreach ($months as $m) {
        $field = "pago_$m";
        $value = (float) ($c->{$field} ?? 0);

        if ($value > 0) {
            $monthlyTotals[$m] += $value;
        }
    }
}

$annualTotal = array_sum($monthlyTotals);
@endphp

<div class="glass-card p-5 mb-6">

    <!-- HEADER -->
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-base font-semibold text-gray-900">
                Custos Mensais Pagos
            </h3>
            <p class="text-xs text-gray-500">
                Visão consolidada por mês
            </p>
        </div>

        <div class="text-right">
            <p class="text-[10px] text-gray-400 uppercase tracking-widest">
                Total anual
            </p>
            <p class="text-base font-semibold text-gray-900">
                R$ {{ number_format($annualTotal, 2, ',', '.') }}
            </p>
        </div>
    </div>

    <!-- CONTAINER COM ALTURA CONTROLADA -->
    <div class="relative h-[180px]">
        <canvas id="monthlyCostsChart"></canvas>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {

    const labels = @json($labels);
    const values = @json(array_values($monthlyTotals));
    const annualTotal = {{ $annualTotal > 0 ? $annualTotal : 1 }};

    const percents = values.map(v =>
        Number(((v / annualTotal) * 100).toFixed(1))
    );

    const canvas = document.getElementById('monthlyCostsChart');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');

    // Gradiente sutil (não chamativo)
    const gradient = ctx.createLinearGradient(0, 0, 0, 180);
    gradient.addColorStop(0, '#ff9a3c');
    gradient.addColorStop(1, '#ff7a00');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                data: values,
                backgroundColor: gradient,
                hoverBackgroundColor: '#ff8a1c',
                borderRadius: 8,
                barThickness: 18
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 600,
                easing: 'easeOutCubic'
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(17,24,39,0.92)',
                    padding: 10,
                    cornerRadius: 8,
                    displayColors: false,
                    titleColor: '#fff',
                    bodyColor: '#e5e7eb',
                    callbacks: {
                        title: ctx => ctx[0].label,
                        label: ctx => {
                            const value = ctx.raw || 0;
                            const percent = percents[ctx.dataIndex] || 0;
                            return `R$ ${value.toLocaleString('pt-BR', { minimumFractionDigits: 2 })} • ${percent}%`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        color: '#6b7280',
                        font: { size: 11 }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.03)',
                        drawBorder: false
                    },
                    ticks: {
                        color: '#9ca3af',
                        font: { size: 11 },
                        callback: v => 'R$ ' + v.toLocaleString('pt-BR')
                    }
                }
            }
        }
    });

});
</script>






<div x-data="costsTable()" class="max-w-7xl mx-auto px-6 py-8">



    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-8">

        <div>
            <h1 class="text-3xl font-semibold text-gray-900 dark:text-white tracking-tight">
                Contas a Pagar
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Visão geral de fornecedores, gastos e tendências
            </p>
        </div>

        <!-- FILTERS -->
        <div class="flex items-center gap-3 w-full md:w-auto">

            <!-- SEARCH -->
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

            <!-- FILTER EMPRESA -->
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




    


    <!-- TABLE WRAPPER -->
    <div class="table-glass overflow-x-auto">
<table class="table-premium">

    <thead>
    <tr>
        <th class="sticky sticky-empresa col-empresa z-20 px-4 py-4 bg-inherit">
            Empresa
        </th>
        <th class="sticky sticky-categoria col-categoria z-20 px-4 py-4 bg-inherit">
            Categoria
        </th>
        <th class="sticky sticky-custo col-custo z-20 px-4 py-4 bg-inherit">
            Custo Fixo
        </th>

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
        (float) $c->pago_jan,
        (float) $c->pago_fev,
        (float) $c->pago_mar,
        (float) $c->pago_abr,
        (float) $c->pago_mai,
        (float) $c->pago_jun,
        (float) $c->pago_jul,
        (float) $c->pago_ago,
        (float) $c->pago_set,
        (float) $c->pago_out,
        (float) $c->pago_nov,
        (float) $c->pago_dez,
    ];

    $total = array_sum($rowVals);
    $avg   = round($total / 12, 2);
    $custoFixo = $avg;
@endphp

<tr
    x-show="rowVisible(
        '{{ strtolower($c->Categoria ?? $c->categoria) }}',
        '{{ strtolower($c->cnpj ?? '') }}'
    )"
>

    <!-- EMPRESA -->
    <td class="sticky sticky-empresa col-empresa px-4 py-3 text-gray-800 font-medium bg-white z-10">
        {{ $c->cnpj ?? '—' }}
    </td>

    <!-- CATEGORIA -->
    <td class="sticky sticky-categoria col-categoria px-4 py-3 font-medium bg-white z-10">
        {{ $c->Categoria ?? $c->categoria }}
    </td>

    <!-- CUSTO FIXO -->
    <td class="sticky sticky-custo col-custo px-4 py-3 text-right font-semibold text-blue-600 bg-white z-10">
        {{ number_format($custoFixo, 2, ',', '.') }}
    </td>

    <!-- MESES -->
    @foreach(['jan','fev','mar','abr','mai','jun','jul','ago','set','out','nov','dez'] as $m)

        @php
            $field = "pago_$m";
            $val = (float) ($c->{$field} ?? 0);

            $percent = $custoFixo > 0
                ? (($val - $custoFixo) / $custoFixo) * 100
                : 0;

            $colorClass = $val <= $custoFixo
                ? 'text-green-600 font-semibold'
                : 'text-red-600 font-semibold';
        @endphp

        <td
            class="text-right cursor-pointer hover:bg-black/5 transition {{ $colorClass }}"
            data-id="{{ $c->id }}"
            data-categoria="{{ $c->Categoria ?? $c->categoria }}"
            data-valor="{{ number_format($val, 2, '.', '') }}"
            data-month="{{ $m }}"
            @click="openNotaModalFromElement($event)"
        >
            {{ number_format($val, 2, ',', '.') }}
            <span class="text-xs opacity-70 ml-1">
                ({{ number_format($percent, 1, ',', '.') }}%)
            </span>
        </td>

    @endforeach

    <!-- TOTAL -->
    <td class="text-right font-semibold">
        {{ number_format($total, 2, ',', '.') }}
    </td>

    <!-- MÉDIA -->
    <td class="text-right">
        {{ number_format($avg, 2, ',', '.') }}
    </td>

</tr>
@endforeach
</tbody>


</table>


    </div>


    <!-- MODAL -->
    <div 
x-show="showModal === true"
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



@endsection
