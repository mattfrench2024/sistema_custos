@extends('layouts.app')

@section('content')

<style>
:root {
    --brand-orange: #ff6200ff;
    --brand-orange-soft: #ffedd5;

    --brand-purple: #f97316;
    --brand-purple-soft: #ede9fe;

    --surface: #ffffff;
    --surface-muted: #f9fafb;

    --text-primary: #111827;
    --text-secondary: #6b7280;

    --radius-lg: 1rem;
    --radius-md: 0.75rem;

    --shadow-soft:
        0 1px 2px rgba(0,0,0,.04),
        0 12px 32px rgba(0,0,0,.08);
}
</style>

<div class="max-w-7xl mx-auto px-6 py-8 text-[var(--text-primary)]">

    {{-- HEADER --}}
    <div class="mb-10 flex items-start justify-between">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight">
                Contratos
            </h1>
            <p class="mt-1 text-sm text-[var(--text-secondary)]">
                {{ $empresaLabel }} • Visão consolidada dos contratos de serviço
            </p>
        </div>
    </div>

    {{-- KPI CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-10">

        {{-- Total contratos --}}
        <div
            class="bg-[var(--surface)] p-6 border border-gray-100"
            style="border-radius: var(--radius-lg); box-shadow: var(--shadow-soft);"
        >
            <div class="text-xs uppercase tracking-wide text-[var(--text-secondary)]">
                Total de contratos
            </div>
            <div class="mt-2 text-3xl font-semibold">
                {{ number_format($totais['quantidade'], 0, ',', '.') }}
            </div>
        </div>

        {{-- Valor mensal --}}
        <div
            class="bg-[var(--surface)] p-6 border border-gray-100"
            style="border-radius: var(--radius-lg); box-shadow: var(--shadow-soft);"
        >
            <div class="text-xs uppercase tracking-wide text-[var(--text-secondary)]">
                Valor mensal contratado
            </div>
            <div class="mt-2 text-3xl font-semibold text-[var(--brand-orange)]">
                R$ {{ number_format($totais['valor_mensal'], 2, ',', '.') }}
            </div>
        </div>

    </div>

    {{-- TABLE CARD --}}
    <div
        class="bg-[var(--surface)] border border-gray-100 overflow-hidden"
        style="border-radius: var(--radius-lg); box-shadow: var(--shadow-soft);"
    >

        <table class="min-w-full text-sm">
            <thead class="bg-[var(--surface-muted)] border-b">
                <tr class="text-xs uppercase tracking-wide text-[var(--text-secondary)]">
                    <th class="px-5 py-4 text-left">Contrato</th>
                    <th class="px-5 py-4 text-left">Cliente</th>
                    <th class="px-5 py-4 text-left">Categoria</th>
                    <th class="px-5 py-4 text-right">Valor mensal</th>
                    <th class="px-5 py-4 text-center">Vigência</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                @foreach ($contratos as $contrato)
                    <tr class="group transition hover:bg-[var(--surface-muted)]">

                        {{-- CONTRATO --}}
                        <td class="px-5 py-4 font-medium">
                            <a
                                href="{{ route('omie.contratos.show', [$empresaSlug, $contrato->id]) }}"
                                class="inline-flex items-center gap-1 text-[var(--brand-orange)] hover:underline"
                            >
                                {{ $contrato->cNumCtr ?? 'Contrato #' . $contrato->nCodCtr }}
                                <span class="opacity-0 group-hover:opacity-100 transition text-xs">→</span>
                            </a>
                        </td>

                        {{-- CLIENTE --}}
                        <td class="px-5 py-4 text-sm text-gray-700">
                            {{ $contrato->cliente->nome_fantasia
                                ?? $contrato->cliente->razao_social
                                ?? '—' }}
                        </td>

                        {{-- CATEGORIA --}}
                        <td class="px-5 py-4">
                            @if($contrato->categoria)
                                <span
                                    class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium"
                                    style="background: var(--brand-orange-soft); color: var(--brand-orange);"
                                >
                                    {{ $contrato->categoria->descricao }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>

                        {{-- VALOR --}}
                        <td class="px-5 py-4 text-right font-semibold">
                            R$ {{ number_format($contrato->nValTotMes, 2, ',', '.') }}
                        </td>

                        {{-- VIGÊNCIA --}}
                        <td class="px-5 py-4 text-center text-xs text-[var(--text-secondary)] whitespace-nowrap">
                            {{ optional($contrato->dVigInicial)->format('d/m/Y') }}
                            <span class="mx-1">→</span>
                            {{ optional($contrato->dVigFinal)->format('d/m/Y') }}
                        </td>

                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- PAGINAÇÃO --}}
        <div class="px-6 py-4 border-t bg-[var(--surface-muted)]">
            {{ $contratos->links() }}
        </div>

    </div>

</div>
@endsection
