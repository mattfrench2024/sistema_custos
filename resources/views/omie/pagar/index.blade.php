@extends('layouts.app')

@section('content')

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
    .tr {
    background: var(--brand-orange);
    color: white;
}

    .card {
        background: var(--surface);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-soft);
    }

    .table-header {
        background: linear-gradient(
            to bottom,
            var(--surface-muted),
            #f3f4f6
        );
    }

    .badge {
        padding: 0.35rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: .03em;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .badge-success {
        background: #dcfce7;
        color: #166534;
    }

    .badge-warning {
        background: var(--brand-orange-soft);
        color: #9a3412;
    }

    .row-hover {
        transition: background-color .2s ease, transform .15s ease;
    }

    .row-hover:hover {
        background-color: var(--surface-muted);
    }

    .action-link {
        color: var(--brand-orange);
        font-weight: 600;
        transition: color .15s ease;
    }

    .action-link:hover {
        color: #c2410c;
    }
</style>

<div class="max-w-7xl mx-auto px-6 py-8">

    {{-- Cabeçalho --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-semibold text-[var(--text-primary)] tracking-tight">
                Contas a Pagar
            </h1>

            <p class="mt-1 text-sm text-[var(--text-secondary)]">
                Empresa
                <span class="font-medium text-gray-700">
                    {{ strtoupper($empresaSlug) }}
                </span>
            </p>
        </div>
    </div>

<form method="GET" class="flex items-center gap-4 mb-4">
    <input type="hidden" name="empresa_codigo" value="{{ $empresaCodigo }}">
    <input type="hidden" name="empresa_slug" value="{{ $empresaSlug }}">

    <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">
            Tipo de documento
        </label>

        <select
            name="tipo_documento"
            onchange="this.form.submit()"
            class="rounded-lg border-gray-300 text-sm px-3 py-2 focus:ring focus:ring-gray-200"
        >
            <option value="">Todos</option>

            @foreach($tiposDocumento as $tipo)
                <option
                    value="{{ $tipo->codigo }}"
                    @selected($tipoSelecionado === $tipo->codigo)
                >
                    {{ $tipo->descricao }} ({{ $tipo->codigo }})
                </option>
            @endforeach
        </select>
    </div>
</form>

<form method="GET" class="mb-4 flex flex-wrap gap-2 items-end">
    <input type="hidden" name="empresa_codigo" value="{{ $empresaCodigo }}">
    <input type="hidden" name="empresa_slug" value="{{ $empresaSlug }}">

    <div>
        <label class="block text-gray-700 text-xs font-semibold">Mês</label>
        <select name="mes" class="form-select">
            <option value="">Todos</option>
            @for($m=1; $m<=12; $m++)
                <option value="{{ $m }}" @if(request('mes') == $m) selected @endif>
                    {{ str_pad($m,2,'0',STR_PAD_LEFT) }}
                </option>
            @endfor
        </select>
    </div>

    <div>
        <label class="block text-gray-700 text-xs font-semibold">Ano</label>
        <select name="ano" class="form-select">
            <option value="">Todos</option>
            @for($y = now()->year-5; $y <= now()->year+1; $y++)
                <option value="{{ $y }}" @if(request('ano') == $y) selected @endif>
                    {{ $y }}
                </option>
            @endfor
        </select>
    </div>

    <button type="submit"
        class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded">
        Filtrar
    </button>
</form>


    {{-- Tabela --}}
    <div class="card overflow-x-auto">
        <table class="min-w-full text-sm">
    <thead class="table-header text-[0.65rem] uppercase tracking-widest text-gray-500">
        <tr>
            <th class="px-5 py-4 text-left font-semibold">Fornecedor</th>
            <th class="px-5 py-4 text-center font-semibold">Categoria</th>
            <th class="px-5 py-4 text-right font-semibold">Valor</th>
            <th class="px-5 py-4 text-center font-semibold">Vencimento</th>
            <th class="px-5 py-4 text-center font-semibold">Status</th>
            <th class="px-5 py-4 text-center font-semibold">Ações</th>
            <th class="px-5 py-4 text-center font-semibold">Tipo</th>
        </tr>
    </thead>

    <tbody class="divide-y divide-gray-100">
        @forelse($contas as $conta)
            <tr class="row-hover">

                {{-- Fornecedor --}}
                <td class="px-5 py-4 font-medium text-gray-800">
                    @if($conta->fornecedor)
                        {{ $conta->fornecedor->razao_social }}
                    @else
                        <span class="text-gray-400 italic">—</span>
                    @endif
                </td>

                {{-- Categoria --}}
                <td class="px-5 py-4 text-center text-sm">
                    @if($conta->categoria)
                        {{ $conta->categoria->descricao }}
                    @else
                        <span class="text-gray-400 italic">Não definida</span>
                    @endif
                </td>

                {{-- Valor --}}
                <td class="px-5 py-4 text-right font-semibold text-gray-900">
                    R$ {{ number_format($conta->valor_documento, 2, ',', '.') }}
                </td>

                {{-- Vencimento --}}
                <td class="px-5 py-4 text-center text-gray-600">
                    {{ optional($conta->data_vencimento)->format('d/m/Y') ?? '—' }}
                </td>

                {{-- Status --}}
                <td class="px-5 py-4 text-center">
                    @php
                        $status = $conta->status_calculado;
                    @endphp

                    <span class="
                        px-3 py-1 rounded-full text-xs font-semibold
                        @if($status === 'VENCIDO') bg-red-100 text-red-700
                        @elseif($status === 'PAGO') bg-green-100 text-green-700
                        @elseif($status === 'CANCELADO') bg-gray-200 text-gray-700
                        @else bg-yellow-100 text-yellow-700
                        @endif
                    ">
                        {{ $status }}
                    </span>
                </td>

                {{-- Ações --}}
                <td class="px-5 py-4 text-center space-y-1">

                    {{-- Ver detalhes --}}
                    <a
                        href="{{ route('omie.pagar.show', [
                            'empresa' => $empresaSlug,
                            'pagar'   => $conta->id
                        ]) }}"
                        class="action-link block"
                    >
                        Ver detalhes
                    </a>

                    {{-- Registrar pagamento (MOVIMENTO FINANCEIRO) --}}
                    @if($conta->status_titulo !== 'PAGO' && $conta->status_titulo !== 'CANCELADO')
                        <a
                            href="{{ route('omie.movimentos.create', [
                                'empresa' => $empresaSlug,
                                'origem'  => 'pagar',
                                'id'      => $conta->id
                            ]) }}"
                            class="inline-block text-[0.65rem] font-semibold
                                   px-3 py-1 rounded-full
                                   bg-green-100 text-green-700
                                   hover:bg-green-200 transition"
                        >
                            Registrar pagamento
                        </a>
                    @endif

                    {{-- Toggle de status --}}
                    @if($conta->status_titulo !== 'CANCELADO')
                        <form
                            action="{{ route('omie.pagar.toggle-status', [
                                'empresa' => $empresaSlug,
                                'pagar'   => $conta->id
                            ]) }}"
                            method="POST"
                        >
                            @csrf
                            @method('PATCH')

                            <button
                                type="submit"
                                class="
                                    text-[0.65rem] font-semibold
                                    px-3 py-1 rounded-full transition
                                    @if($conta->status_titulo === 'PAGO')
                                        bg-gray-100 text-gray-600 hover:bg-yellow-100 hover:text-yellow-700
                                    @else
                                        bg-blue-100 text-blue-700 hover:bg-blue-200
                                    @endif
                                "
                            >
                                @if($conta->status_titulo === 'PAGO')
                                    Marcar como não pago
                                @else
                                    Marcar como pago
                                @endif
                            </button>
                        </form>
                    @endif
                </td>

                {{-- Tipo --}}
                <td class="px-5 py-4 text-center text-sm text-gray-600">
                    {{ optional($conta->tipoDocumento)->descricao ?? '—' }}
                </td>

            </tr>
        @empty
            <tr>
                <td colspan="7" class="px-5 py-8 text-center text-gray-400 italic">
                    Nenhuma conta encontrada para os filtros selecionados.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>



    </div>

    {{-- Paginação --}}
    <div class="mt-8">
        {{ $contas->withQueryString()->links() }}
    </div>

</div>
@endsection
