@extends('layouts.app')

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
    backdrop-filter: blur(12px);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-soft);
}

.card {
    transition: transform .2s ease, box-shadow .2s ease;
}
.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 18px 40px rgba(0,0,0,.12);
}

.badge {
    padding: .25rem .6rem;
    border-radius: .5rem;
    font-weight: 500;
    font-size: .75rem;
}

.gradient-text {
    background: linear-gradient(90deg, var(--brand-from), var(--brand-to));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.table-row {
    transition: background .15s ease;
}
.table-row:hover {
    background: rgba(249,115,22,.05);
}
</style>

<div class="max-w-7xl mx-auto px-6 py-6 space-y-8">

    {{-- HEADER --}}
    <div class="flex flex-col gap-2">
        <a href="{{ route('omie.contratos.index', $empresaSlug) }}"
           class="text-sm text-gray-400 hover:text-orange-500 transition">
            ← Voltar para contratos
        </a>

        <h1 class="text-3xl font-bold gradient-text">
            {{ $contrato->cNumCtr ?? 'Contrato #' . $contrato->nCodCtr }}
        </h1>

        <p class="text-sm text-gray-500">
            {{ $empresaLabel }}
        </p>
    </div>

    {{-- CARDS FINANCEIROS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <div class="glass card p-6">
            <p class="text-sm text-gray-500 mb-1">Valor mensal</p>
            <p class="text-3xl font-bold text-orange-500">
                R$ {{ number_format($financeiro['valor_mensal'], 2, ',', '.') }}
            </p>
        </div>

        <div class="glass card p-6">
            <p class="text-sm text-gray-500 mb-1">Total recebido (Este Mês)</p>
            <p class="text-3xl font-bold text-emerald-600">
                R$ {{ number_format($financeiro['total_recebido'], 2, ',', '.') }}
            </p>
        </div>

        <div class="glass card p-6">
            <p class="text-sm text-gray-500 mb-1">Total pendente (Este Mês)</p>
            <p class="text-3xl font-bold text-amber-500">
                R$ {{ number_format($financeiro['total_pendente'], 2, ',', '.') }}
            </p>
        </div>

    </div>

    {{-- INFORMAÇÕES DO CONTRATO --}}
    <div class="glass p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-6">
            Informações do contrato
        </h2>

        <dl class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">

            <div>
                <dt class="text-gray-400">Cliente</dt>
                <dd class="font-medium text-gray-800">
                    {{ $contrato->cliente->nome_fantasia
                        ?? $contrato->cliente->razao_social
                        ?? '-' }}
                </dd>
            </div>

            <div>
                <dt class="text-gray-400">Categoria</dt>
                <dd class="font-medium text-gray-800">
                    {{ $contrato->categoria->descricao ?? '-' }}
                </dd>
            </div>

            <div>
                <dt class="text-gray-400">Vigência</dt>
                <dd class="font-medium text-gray-800">
                    {{ optional($contrato->dVigInicial)->format('d/m/Y') }}
                    →
                    {{ optional($contrato->dVigFinal)->format('d/m/Y') }}
                </dd>
            </div>

            <div>
                <dt class="text-gray-400">Status</dt>
                <dd>
                    <span class="badge bg-orange-100 text-orange-700">
                        {{ $contrato->cCodSit }}
                    </span>
                </dd>
            </div>

        </dl>
    </div>

    {{-- TÍTULOS A RECEBER --}}
    <div class="glass p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-800">
                Títulos a receber
            </h2>
            <span class="text-sm text-gray-500">
                {{ $financeiro['quantidade_titulos'] }} registros
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-500">
                    <tr>
                        <th class="px-4 py-3 text-left">Vencimento</th>
                        <th class="px-4 py-3 text-right">Valor</th>
                        <th class="px-4 py-3 text-center">Status</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @foreach ($contrato->receber as $titulo)
                        <tr class="table-row">
                            <td class="px-4 py-3">
                                {{ optional($titulo->data_vencimento)->format('d/m/Y') }}
                            </td>

                            <td class="px-4 py-3 text-right font-medium">
                                R$ {{ number_format($titulo->valor_documento, 2, ',', '.') }}
                            </td>

                            <td class="px-4 py-3 text-center">
                                <span class="badge
                                    {{ $titulo->status === 'liquidado'
                                        ? 'bg-green-100 text-green-700'
                                        : 'bg-amber-100 text-amber-700' }}">
                                    {{ ucfirst($titulo->status) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>

</div>
@endsection
