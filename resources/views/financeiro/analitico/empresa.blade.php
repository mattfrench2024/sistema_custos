@extends('layouts.app')

@section('content')

<style>
:root{
    --brand-from: #ff7a18;
    --brand-to: #ffb347;
    --brand-purple: #7e22cc;

    --soft-white: #ffffff;
    --soft-black: #0f172a;

    --glass-border: rgba(0,0,0,0.05);
    --muted: #6b7280;

    --positive: #16a34a;
    --negative: #dc2626;
    --warning: #f59e0b;
}

/* ===== BASE ===== */
body{
    background:linear-gradient(180deg,#ffffff 0%,#f5f6f8 100%);
    color:var(--soft-black);
    font-feature-settings:"ss01","cv02","cv03";
    -webkit-font-smoothing:antialiased;
}

/* ===== CARDS ===== */
.card{
    background:linear-gradient(180deg,#ffffff 0%,#fbfbfc 100%);
    border:1px solid var(--glass-border);
    border-radius:22px;
    padding:1.8rem;
    box-shadow:
        0 8px 24px rgba(15,23,42,.04),
        inset 0 1px 0 rgba(255,255,255,.6);
    transition:
        box-shadow .25s ease,
        transform .25s ease;
}

.card:hover{
    transform:translateY(-2px);
    box-shadow:
        0 14px 40px rgba(15,23,42,.06),
        inset 0 1px 0 rgba(255,255,255,.6);
}

/* ===== KPI ===== */
.kpi-label{
    font-size:.62rem;
    text-transform:uppercase;
    letter-spacing:.18em;
    color:var(--muted);
    font-weight:600;
}

.kpi-value{
    font-size:2.05rem;
    font-weight:750;
    margin-top:.25rem;
    line-height:1.15;
}

.positive{color:var(--positive);}
.negative{color:var(--negative);}

/* ===== SECTIONS ===== */
.section-title{
    font-size:1.15rem;
    font-weight:650;
    letter-spacing:.01em;
}

.section-subtitle{
    font-size:.85rem;
    color:var(--muted);
    margin-top:.15rem;
}

/* ===== FILTERS ===== */
.filter-input{
    border:1px solid var(--glass-border);
    border-radius:14px;
    padding:.6rem .9rem;
    font-size:.85rem;
    background:#fff;
    transition:.2s ease;
}

.filter-input:hover{
    border-color:rgba(0,0,0,.12);
}

.filter-input:focus{
    outline:none;
    border-color:var(--brand-from);
    box-shadow:0 0 0 3px rgba(255,122,24,.18);
}

.filter-button{
    background:linear-gradient(135deg,var(--brand-from),var(--brand-to));
    color:white;
    border-radius:14px;
    padding:.65rem 1.35rem;
    font-size:.85rem;
    font-weight:650;
    box-shadow:0 6px 16px rgba(255,122,24,.25);
    transition:.25s ease;
}

.filter-button:hover{
    transform:translateY(-1px);
    box-shadow:0 10px 24px rgba(255,122,24,.3);
    opacity:.95;
}

/* ===== LISTS ===== */
.card .border-b{
    border-color:rgba(0,0,0,.05);
}

.card .flex:hover{
    background:rgba(0,0,0,.015);
    border-radius:10px;
}

/* ===== CHART CONTAINERS ===== */
canvas{
    image-rendering:optimizeQuality;
}

/* ===== RESPONSIVE ===== */
@media (max-width:640px){
    .kpi-value{
        font-size:1.7rem;
    }
    .section-title{
        font-size:1.05rem;
    }
}

</style>
<br>
{{-- HEADER --}}
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-end gap-6 mb-8">
    <div>
        <h1 class="text-3xl font-bold tracking-tight">
            {{ $empresaNome }}
        </h1>
        <p class="text-sm text-gray-500 mt-1">
            Análise financeira executiva • {{ $ano }}
        </p>
    </div>

    <form method="GET" class="flex gap-3 items-center">
        <input type="number" name="ano" value="{{ $ano }}" class="filter-input w-24">

        <select name="mes" class="filter-input">
            <option value="">Ano completo</option>
           @for($m = 1; $m <= 12; $m++)
    <option value="{{ $m }}" @selected($mes == $m)>
        {{ \Carbon\Carbon::createFromDate(null, $m, 1)->locale('pt_BR')->isoFormat('MMMM') }}
    </option>
@endfor

        </select>

        <button class="filter-button">Aplicar</button>
    </form>
</div>

{{-- KPIs --}}
<section class="mb-8">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

        <div class="card">
            <p class="kpi-label">Receita</p>
            <p class="kpi-value positive">
                R$ {{ number_format($kpis['receita'],2,',','.') }}
            </p>
        </div>

        <div class="card">
            <p class="kpi-label">Pagamentos</p>
            <p class="kpi-value negative">
                R$ {{ number_format($kpis['custos'],2,',','.') }}
            </p>
        </div>

        <div class="card">
            <p class="kpi-label">Saldo Operacional</p>
            <p class="kpi-value {{ $kpis['saldo']<0?'negative':'positive' }}">
                R$ {{ number_format($kpis['saldo'],2,',','.') }}
            </p>
        </div>

        <div class="card">
            <p class="kpi-label">Margem</p>
            <p class="kpi-value">
                {{ $kpis['margem'] }}%
            </p>
        </div>

    </div>
</section>

{{-- EVOLUÇÃO --}}
<section class="card mb-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="section-title">Evolução Mensal</h2>
            <p class="section-subtitle">Receitas x Pagamentos</p>
        </div>
    </div>

    <div class="relative h-[320px]">
        <canvas id="mensalChart"></canvas>
    </div>
</section>

{{-- TOPS --}}
<section class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

    <div class="card">
        <h3 class="section-title mb-4">Top Clientes</h3>
        @forelse($topClientes as $c)
            <div class="flex justify-between items-center py-2 border-b last:border-0">
                <div>
                    <p class="text-sm font-medium">{{ $c['cliente'] }}</p>
                    <p class="text-xs text-gray-400">Código {{ $c['codigo'] }}</p>
                </div>
                <span class="positive font-semibold">
                    R$ {{ number_format($c['total'],2,',','.') }}
                </span>
            </div>
        @empty
            <p class="text-sm text-gray-400">Nenhum cliente encontrado.</p>
        @endforelse
    </div>

    <div class="card">
        <h3 class="section-title mb-4">Top Fornecedores</h3>
        @forelse($topFornecedores as $f)
            <div class="flex justify-between items-center py-2 border-b last:border-0">
                <div>
                    <p class="text-sm font-medium">{{ $f['fornecedor'] }}</p>
                    <p class="text-xs text-gray-400">Código {{ $f['codigo'] }}</p>
                </div>
                <span class="negative font-semibold">
                    R$ {{ number_format($f['total'],2,',','.') }}
                </span>
            </div>
        @empty
            <p class="text-sm text-gray-400">Nenhum fornecedor encontrado.</p>
        @endforelse
    </div>

</section>

{{-- CONCENTRAÇÃO --}}
<section class="card">
    <h2 class="section-title mb-4">Concentração de Receita</h2>
    <canvas id="concentracaoChart" height="120"></canvas>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const mensal=@json($mensal);

new Chart(document.getElementById('mensalChart'),{
    type:'line',
    data:{
        labels:mensal.map(m=>m.mes),
        datasets:[
            {
                label:'Receita',
                data:mensal.map(m=>m.receita),
                borderColor:'#16a34a',
                backgroundColor:'rgba(22,163,74,.15)',
                tension:.4,
                fill:true
            },
            {
                label:'Pagamentos',
                data:mensal.map(m=>m.custos),
                borderColor:'#dc2626',
                backgroundColor:'rgba(220,38,38,.15)',
                tension:.4,
                fill:true
            }
        ]
    },
    options:{
        responsive:true,
        maintainAspectRatio:false,
        plugins:{legend:{display:false}},
        scales:{
            x:{grid:{display:false}},
            y:{
                ticks:{
                    callback:v=>`R$ ${v.toLocaleString('pt-BR')}`
                }
            }
        }
    }
});

new Chart(document.getElementById('concentracaoChart'),{
    type:'doughnut',
    data:{
        labels:@json($concentracaoClientes->pluck('cliente')),
        datasets:[{
            data:@json($concentracaoClientes->pluck('percentual')),
            backgroundColor:['#ff7a18','#ffb347','#7e22cc','#16a34a','#2563eb']
        }]
    }
});
</script>

@endsection
