@extends('layouts.app')

<style>
    :root {
        --brand-orange: #f97316;
        --brand-orange-soft: #ffedd5;

        --brand-purple: #7c3aed;
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

    .card {
        background: var(--surface);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-soft);
        border: 1px solid #e5e7eb;
    }

    .table-row-hover:hover {
        background: linear-gradient(
            90deg,
            var(--brand-orange-soft),
            transparent 75%
        );
    }

    .badge-uf {
        background: #f3f4f6;
        color: #374151;
        font-weight: 600;
        border-radius: 0.5rem;
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }

    .tag {
        background: var(--brand-purple-soft);
        color: var(--brand-purple);
        border-radius: 999px;
        padding: 0.25rem 0.75rem;
        font-size: 11px;
        font-weight: 600;
        border: 1px solid rgba(124,58,237,.25);
        white-space: nowrap;
    }

    .btn-primary {
        background: var(--brand-orange);
        color: #fff;
        border-radius: var(--radius-md);
        padding: 0.5rem 0.9rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all .2s ease;
        box-shadow: 0 4px 12px rgba(249,115,22,.25);
    }

    .btn-primary:hover {
        filter: brightness(0.95);
        transform: translateY(-1px);
    }
</style>

@section('content')
<div class="max-w-7xl mx-auto px-6 py-10 space-y-8">

    {{-- HEADER --}}
    <div class="relative overflow-hidden rounded-2xl border border-orange-100
                bg-gradient-to-r from-orange-50 to-white p-6">

        <div class="absolute inset-y-0 left-0 w-1 bg-orange-500"></div>

        <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between relative z-10">

            <div>
                <h1 class="text-3xl font-semibold tracking-tight text-gray-900">
                    Clientes Omie
                </h1>

                <p class="text-sm text-gray-600 mt-1">
    {{ $empresaLabel }} · Código
    <span class="font-semibold text-orange-600">
        {{ $empresa }}
    </span> ·
    <span class="font-semibold text-orange-600">
        {{ $clientes->total() }}
    </span>
    clientes cadastrados
</p>

            </div>

            {{-- SEARCH --}}
<form method="GET"
      action="{{ route('omie.clientes.index', $empresa) }}"
      class="relative w-full sm:w-96">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Buscar por razão social, CNPJ ou CPF"
                    class="w-full pl-11 pr-4 py-3 rounded-xl text-sm
                           border border-orange-200 bg-white
                           focus:ring-2 focus:ring-orange-500/40
                           focus:border-orange-500
                           placeholder-gray-400 transition"
                >

                <svg
                    class="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-orange-500"
                    fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24"
                >
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M21 21l-4.35-4.35m1.85-5.65a7.5 7.5 0 11-15 0 7.5 7.5 0 0115 0z"/>
                </svg>
            </form>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="card overflow-hidden">

        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr class="text-left text-xs uppercase tracking-wide
                           text-gray-500 font-semibold">
                    <th class="px-6 py-4">Razão Social</th>
                    <th class="px-6 py-4">Documento</th>
                    <th class="px-6 py-4">Cidade</th>
                    <th class="px-6 py-4">UF</th>
                    <th class="px-6 py-4">Classificação</th>
                    <th class="px-6 py-4 text-right"></th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                @forelse ($clientes as $cliente)
                    <tr class="table-row-hover transition">

                        {{-- RAZÃO SOCIAL --}}
                        <td class="px-6 py-4">
                            <div class="font-semibold text-[15px]
                                        text-gray-900 leading-tight">
                                {{ $cliente->razao_social }}
                            </div>

                            @if($cliente->nome_fantasia &&
                                $cliente->nome_fantasia !== $cliente->razao_social)
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $cliente->nome_fantasia }}
                                </div>
                            @endif
                        </td>

                        {{-- DOCUMENTO --}}
                        <td class="px-6 py-4 font-mono text-xs text-gray-500">
                            {{ $cliente->cnpj_cpf }}
                        </td>

                        {{-- CIDADE --}}
                        <td class="px-6 py-4 text-gray-700">
                            {{ $cliente->cidade ?: '—' }}
                        </td>

                        {{-- UF --}}
                        <td class="px-6 py-4">
                            <span class="badge-uf">
                                {{ $cliente->estado ?: '—' }}
                            </span>
                        </td>

                        {{-- TAGS --}}
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-2">
                                @foreach ($cliente->tags ?? [] as $tag)
                                    <span class="tag">
                                        {{ $tag['tag'] ?? '' }}
                                    </span>
                                @endforeach
                            </div>
                        </td>

                        {{-- ACTION --}}
                        <td class="px-6 py-4 text-right">
                            <a
                                href="{{ route('omie.clientes.show', [
                                    'empresa' => $empresa,
                                    'cliente' => $cliente
                                ]) }}"
                                class="btn-primary"
                            >
                                Ver detalhes
                                <svg class="w-4 h-4"
                                     fill="none"
                                     stroke="currentColor"
                                     stroke-width="2"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="6"
                            class="px-6 py-20 text-center text-gray-500">
                            Nenhum cliente encontrado.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINATION --}}
    <div class="flex justify-end">
        {{ $clientes->withQueryString()->links() }}
    </div>

</div>
@endsection
