@extends('layouts.app')

@section('content')

{{-- Título principal --}}
<div class="brand-gradient p-6 rounded-lg text-white shadow mb-6">
    <h1 class="text-3xl font-bold">Admin — Painel Operacional Completo</h1>
    <p class="text-white/90 text-lg">Visão estratégica e técnica de todo o ambiente do sistema de custos.</p>
</div>

{{-- KPIs principais --}}
<div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-6">

    <div class="kpi bg-blue-50 border-l-4 border-blue-500 p-5 rounded shadow">
        <h4 class="text-gray-600">Produtos Cadastrados</h4>
        <div class="text-4xl font-bold text-blue-700">{{ $totals['produtos_count'] }}</div>
        <a href="{{ route('products.index') }}" class="text-blue-600 underline mt-1 inline-block">
            Gerenciar Produtos
        </a>
    </div>

    <div class="kpi bg-green-50 border-l-4 border-green-500 p-5 rounded shadow">
        <h4 class="text-gray-600">Custos Totais</h4>
        <div class="text-4xl font-bold text-green-700">
            R$ {{ number_format($totals['custos_totais'], 2, ',', '.') }}
        </div>
    </div>

    <div class="kpi bg-yellow-50 border-l-4 border-yellow-500 p-5 rounded shadow">
        <h4 class="text-gray-600">Categorias</h4>
        <div class="text-4xl font-bold text-yellow-700">{{ $totals['categorias_count'] }}</div>
        <a href="{{ route('categories.index') }}" class="text-yellow-700 underline mt-1 inline-block">
            Gerenciar Categorias
        </a>
    </div>

    <div class="kpi bg-purple-50 border-l-4 border-purple-500 p-5 rounded shadow">
        <h4 class="text-gray-600">Usuários Ativos</h4>
        <div class="text-4xl font-bold text-purple-700">{{ $totals['usuarios_count'] }}</div>
        <a href="{{ route('roles.index') }}" class="text-purple-700 underline mt-1 inline-block">
            Papéis & Permissões
        </a>
    </div>

    <div class="kpi bg-indigo-50 border-l-4 border-indigo-500 p-5 rounded shadow">
        <h4 class="text-gray-600">Preços Mensais</h4>
        <div class="text-4xl font-bold text-indigo-700">{{ $totals['precos_count'] ?? '—' }}</div>
        <a href="{{ route('product_prices.index') }}" class="text-indigo-700 underline mt-1 inline-block">
            Gerenciar Preços
        </a>
    </div>

</div>

{{-- Gráfico — Top 10 Produtos por Valor --}}
<div class="chart-card mb-10 bg-white p-6 rounded shadow">
    <h3 class="font-semibold mb-4 text-xl">Top 10 Produtos por Valor</h3>
    <canvas id="chartTop10"></canvas>
</div>

{{-- Blocos adicionais --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">

    {{-- Logs Recentes --}}
    <div class="bg-white rounded shadow p-6">
        <h3 class="text-xl font-semibold mb-4">Logs Recentes</h3>

        @if(count($audit_logs))
            <ul class="divide-y">
                @foreach($audit_logs as $log)
                    <li class="py-3">
                        <p class="font-semibold">{{ $log->acao }}</p>
                        <p class="text-gray-600 text-sm">{{ $log->created_at }} — Usuário: {{ $log->user->name ?? 'N/A' }}</p>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-gray-500">Nenhum log registrado recentemente.</p>
        @endif

        <a href="{{ route('logs.index') }}" class="mt-3 inline-block text-blue-600 underline">
            Ver todos os logs
        </a>
    </div>

    {{-- Alertas do Sistema --}}
    <div class="bg-white rounded shadow p-6">
        <h3 class="text-xl font-semibold mb-4">Alertas do Sistema</h3>
        <ul class="space-y-3">

            <li class="p-3 bg-red-50 border-l-4 border-red-500 rounded">
                <span class="font-bold text-red-700">Categorias duplicadas detectadas</span>
            </li>

            <li class="p-3 bg-yellow-50 border-l-4 border-yellow-500 rounded">
                <span class="font-bold text-yellow-700">Planilha recente importada com divergências</span>
            </li>

            <li class="p-3 bg-blue-50 border-l-4 border-blue-500 rounded">
                <span class="font-bold text-blue-700">Jobs pendentes na fila</span>
            </li>

        </ul>
    </div>

</div>

{{-- Ferramentas de Administrador --}}
<div class="bg-white p-6 rounded shadow mb-10">
    <h3 class="text-xl font-semibold mb-5">Ferramentas de Manutenção</h3>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <a href="#" class="tool-card">
            <div class="p-4 bg-gray-50 border rounded text-center shadow hover:bg-gray-100">
                Limpar Cache do Sistema
            </div>
        </a>

        <a href="#" class="tool-card">
            <div class="p-4 bg-gray-50 border rounded text-center shadow hover:bg-gray-100">
                Regenerar Índices
            </div>
        </a>

        <a href="#" class="tool-card">
            <div class="p-4 bg-gray-50 border rounded text-center shadow hover:bg-gray-100">
                Baixar Logs de Depuração
            </div>
        </a>

    </div>
</div>

{{-- JS do Gráfico --}}
<script>
const ctx = document.getElementById('chartTop10');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: @json($chartLabels),
        datasets: [{
            label: 'Valor (R$)',
            data: @json($chartValues),
            borderWidth: 1,
            backgroundColor: '#4f46e5'
        }]
    }
});
</script>

@endsection
