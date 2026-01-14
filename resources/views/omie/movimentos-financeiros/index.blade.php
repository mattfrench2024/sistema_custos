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

    .card {
        background: var(--soft-white);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-soft);
    }

    .glass {
        backdrop-filter: blur(12px);
        border: 1px solid var(--glass-border);
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--brand-from), var(--brand-to));
        color: white;
        font-weight: 600;
        border-radius: var(--radius-md);
        transition: all .25s ease;
    }

    .btn-primary:hover {
        opacity: .92;
        transform: translateY(-1px);
    }

    .input,
    .select {
        width: 100%;
        margin-top: .25rem;
        padding: .55rem .75rem;
        border-radius: var(--radius-md);
        border: 1px solid #e5e7eb;
        background: white;
        transition: all .2s ease;
    }

    .input:focus,
    .select:focus {
        outline: none;
        border-color: var(--brand-primary);
        box-shadow: 0 0 0 2px rgba(249,115,22,.2);
    }

    .badge {
        padding: .25rem .75rem;
        border-radius: 999px;
        font-size: .75rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .badge-credit {
        background: rgba(34,197,94,.15);
        color: #15803d;
    }

    .badge-debit {
        background: rgba(239,68,68,.15);
        color: #b91c1c;
    }

    .table-row:hover {
        background: rgba(249,250,251,.8);
    }

    .link {
        color: var(--brand-primary);
        font-weight: 600;
        transition: color .2s ease;
    }

    .link:hover {
        text-decoration: underline;
    }
</style>

<div class="space-y-6">

    {{-- HEADER --}}
    <div
        class="card p-6 text-white"
        style="background: linear-gradient(135deg, var(--brand-from), var(--brand-to));"
    >
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold leading-tight">
                    Movimentos Financeiros
                </h1>
                <p class="opacity-90 text-sm">
                    {{ $empresaNome }}
                </p>
            </div>

            <div
                class="text-sm px-4 py-2 rounded-lg glass"
                style="background: rgba(255,255,255,.18);"
            >
                Total de registros:
                <strong>{{ $movimentos->total() }}</strong>
            </div>
        </div>
    </div>

    {{-- FILTROS --}}
<form method="GET" class="card p-5">
    <div class="grid grid-cols-1 md:grid-cols-6 gap-4">

        {{-- Data inicial --}}
        <div>
            <label class="text-sm text-[var(--muted)]">Data inicial</label>
            <input
                type="date"
                name="data_de"
                value="{{ request('data_de') }}"
                class="input"
            >
        </div>

        {{-- Data final --}}
        <div>
            <label class="text-sm text-[var(--muted)]">Data final</label>
            <input
                type="date"
                name="data_ate"
                value="{{ request('data_ate') }}"
                class="input"
            >
        </div>

        {{-- Origem (cGrupo) --}}
        <div>
            <label class="text-sm text-[var(--muted)]">Origem</label>
            <select name="grupo" class="select">
                <option value="">Todas</option>

                <option value="receber" @selected(request('grupo') === 'receber')>
                    Contas a Receber
                </option>

                <option value="pagar" @selected(request('grupo') === 'pagar')>
                    Contas a Pagar
                </option>

                <option value="cc" @selected(request('grupo') === 'cc')>
                    Conta Corrente
                </option>
            </select>
        </div>

        {{-- Tipo técnico --}}
        <div>
            <label class="text-sm text-[var(--muted)]">Tipo</label>
            <select name="tipo" class="select">
                <option value="">Todos</option>
                <option value="R" @selected(request('tipo') === 'R')>Receber</option>
                <option value="P" @selected(request('tipo') === 'P')>Pagar</option>
                <option value="C" @selected(request('tipo') === 'C')>Crédito CC</option>
                <option value="D" @selected(request('tipo') === 'D')>Débito CC</option>
            </select>
        </div>

        {{-- Ações --}}
        <div class="flex items-end col-span-2 gap-2">
            <button class="btn-primary w-full py-2">
                Filtrar
            </button>

            <a
                href="{{ route('omie.movimentos.index', $empresa) }}"
                class="w-full py-2 text-center rounded-md border text-sm font-semibold text-gray-600 hover:bg-gray-50"
            >
                Limpar
            </a>
        </div>

    </div>
</form>



    {{-- TABELA --}}
    <div class="card overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs uppercase text-[var(--muted)]">
                <tr>
                    <th class="px-4 py-3 text-left">Data</th>
                    <th class="px-4 py-3 text-left">Tipo</th>
                    <th class="px-4 py-3 text-left">Origem</th>
                    <th class="px-4 py-3 text-right">Valor</th>
                    <th class="px-4 py-3 text-center">Ações</th>
                </tr>
            </thead>

            <tbody class="divide-y">

                @forelse ($movimentos as $mov)

                    <tr class="table-row transition">

                        <td class="px-4 py-3">
                            {{ optional($mov->data_movimento)->format('d/m/Y') }}
                        </td>

                        <td class="px-4 py-3">
<span class="badge
    @if($mov->isEntradaGerencial())
        badge-credit
    @elseif($mov->isSaidaGerencial())
        badge-debit
    @else
        bg-gray-100 text-gray-600
    @endif
">
    {{ $mov->tipo_label }}
</span>
                                {{ $mov->tipo_label }}
                            </span>
                        </td>

                        <td class="px-4 py-3 text-[var(--muted)]">
                            {{ $mov->origem }}
                        </td>

                        <td
                            class="px-4 py-3 text-right font-semibold
@if($mov->isEntradaGerencial())
    text-green-600
@elseif($mov->isSaidaGerencial())
    text-red-600
@else
    text-gray-500
@endif"

                        >
                            {{ number_format($mov->valor, 2, ',', '.') }}
                        </td>

                        <td class="px-4 py-3 text-center">
                            <a
                                href="{{ route('omie.movimentos.show', [$empresa, $mov->id]) }}"
                                class="link"
                            >
                                Ver
                            </a>
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-[var(--muted)]">
                            Nenhum movimento encontrado para os filtros selecionados.
                        </td>
                    </tr>

                @endforelse

            </tbody>
        </table>
    </div>

    {{-- PAGINAÇÃO --}}
    <div>
        {{ $movimentos->links() }}
    </div>

</div>

@endsection
