@extends('layouts.app')

@section('title', 'Financeiro Consolidado')

@section('content')

<style>
:root{
    --brand-from: #f97316;
    --brand-to:   #fbbf24;
    --brand-primary: #f97316;
    --soft-white: rgba(255,255,255,0.96);
    --soft-black: rgba(17,24,39,0.92);
    --glass-border: rgba(255,255,255,0.12);
    --text-primary: #111827;
    --muted: #6b7280;
    --radius-lg: 1rem;
    --radius-md: 0.75rem;
    --shadow-soft:
        0 1px 2px rgba(0,0,0,.04),
        0 12px 32px rgba(0,0,0,.08);
}

.glass {
    background: var(--soft-white);
    backdrop-filter: blur(14px);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-soft);
    border: 1px solid var(--glass-border);
}

.kpi-card {
    transition: transform .2s ease, box-shadow .2s ease;
}
.kpi-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 18px 40px rgba(0,0,0,.12);
}

.badge-soft {
    padding: .25rem .6rem;
    border-radius: .5rem;
    font-size: .75rem;
    font-weight: 600;
}

.table-premium th {
    font-size: .75rem;
    letter-spacing: .04em;
    text-transform: uppercase;
    color: var(--muted);
}
.table-premium td {
    vertical-align: middle;
}
</style>

<div class="max-w-7xl mx-auto px-6 py-6 space-y-6">

    {{-- HEADER --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">
                Financeiro Consolidado
            </h1>
            <p class="text-sm text-gray-500">
                {{ $empresaNome }} • Visão Executiva
            </p>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">

        {{-- RECEBER --}}
        <div class="glass p-5 kpi-card">
            <p class="text-xs text-gray-500 mb-1">
                Total a Receber (Realizado)
            </p>
            <p class="text-2xl font-semibold text-green-600">
                R$ {{ number_format($kpis['receber']['valor'], 2, ',', '.') }}
            </p>
            <p class="text-xs text-gray-400 mt-1">
                {{ $kpis['receber']['qtd'] }} títulos
            </p>
        </div>

        {{-- PAGAR --}}
        <div class="glass p-5 kpi-card">
            <p class="text-xs text-gray-500 mb-1">
                Total a Pagar (Realizado)
            </p>
            <p class="text-2xl font-semibold text-red-600">
                R$ {{ number_format($kpis['pagar']['valor'], 2, ',', '.') }}
            </p>
            <p class="text-xs text-gray-400 mt-1">
                {{ $kpis['pagar']['qtd'] }} títulos
            </p>
        </div>

        {{-- SALDO --}}
        <div class="glass p-5 kpi-card">
            <p class="text-xs text-gray-500 mb-1">
                Saldo Realizado
            </p>
            <p class="text-2xl font-semibold {{ $kpis['saldo_realizado'] >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                R$ {{ number_format($kpis['saldo_realizado'], 2, ',', '.') }}
            </p>
        </div>

        {{-- REGISTROS --}}
        <div class="glass p-5 kpi-card">
            <p class="text-xs text-gray-500 mb-1">
                Movimentos Analisados
            </p>
            <p class="text-2xl font-semibold text-gray-900">
                {{ $kpis['total_registros'] }}
            </p>
        </div>

    </div>

    {{-- PROJEÇÃO --}}
    <div class="glass overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200/60">
            <h2 class="font-semibold text-gray-900">
                Projeção Financeira
            </h2>
            <p class="text-xs text-gray-500">
                Títulos em aberto por horizonte de tempo
            </p>
        </div>

        <table class="min-w-full table-premium">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left">Período</th>
                    <th class="px-5 py-3 text-right">A Receber</th>
                    <th class="px-5 py-3 text-right">A Pagar</th>
                    <th class="px-5 py-3 text-right">Saldo</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($projecoes as $dias => $p)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-medium text-gray-800">
                            {{ $dias }} dias
                        </td>
                        <td class="px-5 py-3 text-right text-green-600">
                            R$ {{ number_format($p['receber'], 2, ',', '.') }}
                        </td>
                        <td class="px-5 py-3 text-right text-red-600">
                            R$ {{ number_format($p['pagar'], 2, ',', '.') }}
                        </td>
                        <td class="px-5 py-3 text-right font-semibold {{ $p['saldo'] >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                            R$ {{ number_format($p['saldo'], 2, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- MOVIMENTOS --}}
    <div class="glass overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200/60">
            <h2 class="font-semibold text-gray-900">
                Últimos Movimentos Financeiros
            </h2>
        </div>

        <table class="min-w-full table-premium">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3">Data</th>
                    <th class="px-5 py-3">Tipo</th>
                    <th class="px-5 py-3 text-right">Valor</th>
                    <th class="px-5 py-3">Conta</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($movimentos as $m)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3">
                            {{ \Carbon\Carbon::parse($m->data_movimento)->format('d/m/Y') }}
                        </td>
                        <td class="px-5 py-3">
                            {{ $m->tipo_movimento }}
                        </td>
                        <td class="px-5 py-3 text-right font-medium">
                            R$ {{ number_format($m->valor, 2, ',', '.') }}
                        </td>
                        <td class="px-5 py-3 text-gray-500">
                            {{ $m->codigo_conta_corrente }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="px-5 py-4 border-t border-gray-200/60">
            {{ $movimentos->links() }}
        </div>
    </div>

</div>
@endsection

