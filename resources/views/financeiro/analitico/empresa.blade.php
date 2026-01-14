@extends('layouts.app')

@section('title', 'Financeiro Analítico — ' . $empresaNome)

@section('content')

<style>
:root{
    --success:#16a34a;
    --danger:#dc2626;
    --info:#2563eb;
    --warning:#f59e0b;
    --purple:#7c3aed;
    --card:#ffffff;
    --muted:#6b7280;
    --shadow:0 15px 35px rgba(0,0,0,.08);
}
.glass-card{
    background:var(--card);
    border-radius:1rem;
    box-shadow:var(--shadow);
}
.kpi{
    display:flex;
    flex-direction:column;
    gap:.25rem;
}
.kpi span{
    font-size:.85rem;
    color:var(--muted);
}
.kpi strong{
    font-size:1.9rem;
    font-weight:700;
}
.badge{
    padding:.25rem .6rem;
    border-radius:999px;
    font-size:.7rem;
    font-weight:600;
}
</style>

<div class="space-y-10">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Financeiro Analítico — {{ $empresaNome }}
            </h1>
            <p class="text-sm text-gray-500">
                Ano {{ $ano }} {{ $mes ? '• ' . \Carbon\Carbon::create()->month($mes)->locale('pt_BR')->isoFormat('MMMM') : '' }}
            </p>
        </div>

        <form method="GET" class="flex gap-2 items-center">
            <select name="ano" class="border rounded-md px-3 py-1 text-sm">
                @for ($y = now()->year; $y >= now()->year - 5; $y--)
                    <option value="{{ $y }}" @selected($y == $ano)>{{ $y }}</option>
                @endfor
            </select>

            <select name="mes" class="border rounded-md px-3 py-1 text-sm">
                <option value="">Todos</option>
                @for ($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" @selected($mes == $m)>
                        {{ \Carbon\Carbon::create()->month($m)->locale('pt_BR')->isoFormat('MMMM') }}
                    </option>
                @endfor
            </select>

            <button class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-1 rounded-md text-sm">
                Filtrar
            </button>
        </form>
    </div>

    {{-- KPIs --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="glass-card p-5 kpi">
            <span>Receita</span>
            <strong style="color:var(--success)">
                R$ {{ number_format($kpis['receita'],2,',','.') }}
            </strong>
        </div>

        <div class="glass-card p-5 kpi">
            <span>Custos</span>
            <strong style="color:var(--danger)">
                R$ {{ number_format($kpis['custos'],2,',','.') }}
            </strong>
        </div>

        <div class="glass-card p-5 kpi">
            <span>Resultado</span>
            <strong style="color:{{ $kpis['saldo'] >= 0 ? 'var(--success)' : 'var(--danger)' }}">
                R$ {{ number_format($kpis['saldo'],2,',','.') }}
            </strong>
        </div>

        <div class="glass-card p-5 kpi">
            <span>Margem</span>
            <strong style="color:{{ $kpis['margem'] >= 0 ? 'var(--purple)' : 'var(--danger)' }}">
                {{ number_format($kpis['margem'],2,',','.') }}%
            </strong>
        </div>
    </div>

    {{-- EVOLUÇÃO FINANCEIRA --}}
    <div class="glass-card p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Evolução Financeira</h2>
            <select id="tipoGrafico" class="border rounded-md px-2 py-1 text-sm">
                <option value="bar">Barras</option>
                <option value="line">Linha</option>
            </select>
        </div>
        <div class="relative h-[360px]">
            <canvas id="graficoMensal"></canvas>
        </div>
    </div>


</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {

const moeda = v => 'R$ ' + Number(v).toLocaleString('pt-BR', {minimumFractionDigits:2});

let dados = @json($mensal);

let labels = dados.map(i => i.mes);
let receitas = dados.map(i => i.receita);
let custos = dados.map(i => i.custos);
let saldos = dados.map(i => i.receita - i.custos);

let ctx = document.getElementById('graficoMensal').getContext('2d');
let chart;

function render(tipo){
    if(chart) chart.destroy();

    chart = new Chart(ctx,{
        type: tipo,
        data:{
            labels,
            datasets:[
                { label:'Receita', data:receitas, backgroundColor:'rgba(22,163,74,.6)' },
                { label:'Custos', data:custos, backgroundColor:'rgba(220,38,38,.6)' },
                { label:'Resultado', data:saldos, borderColor:'#2563eb', type:'line', tension:.4 }
            ]
        },
        options:{
            responsive:true,
            maintainAspectRatio:false,
            plugins:{
                tooltip:{ callbacks:{ label:c => `${c.dataset.label}: ${moeda(c.raw)}` } }
            },
            scales:{ y:{ ticks:{ callback:v => moeda(v) } } }
        }
    });
}

render('bar');
document.getElementById('tipoGrafico').addEventListener('change', e => render(e.target.value));

new Chart(document.getElementById('graficoConcentracao'),{
    type:'doughnut',
    data:{
        labels:@json($concentracaoClientes->pluck('cliente')),
        datasets:[{
            data:@json($concentracaoClientes->pluck('percentual')),
            backgroundColor:['#f97316','#fbbf24','#fb923c','#fde68a','#fdba74']
        }]
    },
    options:{ responsive:true, maintainAspectRatio:false }
});

});
</script>
@endpush

@endsection
