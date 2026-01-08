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
Empresa <span class="font-medium text-gray-700">{{ strtoupper($empresaSlug) }}</span>
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

    {{-- Tabela --}}
    <div class="card overflow-x-auto">
        <table class="min-w-full text-sm">
    <thead class="table-header text-[0.65rem] uppercase tracking-widest text-gray-500">
        <tr>
            <th class="px-5 py-4 text-left font-semibold">Fornecedor</th>
            <th class="px-5 py-4 text-center font-semibold">Emissão</th>
            <th class="px-5 py-4 text-center font-semibold">Vencimento</th>
            <th class="px-5 py-4 text-right font-semibold">Valor</th>
            <th class="px-5 py-4 text-center font-semibold">Status</th>
            <th class="px-5 py-4 text-center font-semibold">Ações</th>
            <th class="px-5 py-4 text-center font-semibold">Tipo</th>
        </tr>
    </thead>

    <tbody class="divide-y divide-gray-100">
        @forelse($contas as $conta)
            <tr class="row-hover">
                <td class="px-5 py-4 font-medium text-gray-800">
    {{ $conta->nome_fornecedor }}
</td>


                <td class="px-5 py-4 text-center text-gray-600">
                    {{ optional($conta->data_emissao)->format('d/m/Y') ?? '—' }}
                </td>

                <td class="px-5 py-4 text-center text-gray-600">
                    {{ optional($conta->data_vencimento)->format('d/m/Y') ?? '—' }}
                </td>

                <td class="px-5 py-4 text-right font-semibold text-gray-900">
                    R$ {{ number_format($conta->valor_documento, 2, ',', '.') }}
                </td>

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

                <td class="px-5 py-4 text-center">
                    <a
                        href="{{ route('omie.pagar.show', [
                            'empresa' => $empresaSlug,
                            'pagar'   => $conta->codigo_lancamento_omie
                        ]) }}"
                        class="action-link"
                    >
                        Ver detalhes
                    </a>
                </td>

                <td class="px-4 py-2 text-sm text-center">
    @if($conta->codigo_tipo_documento === '99999')
        <span class="text-gray-400 italic">
            Outros
        </span>
    @else
        <span class="font-medium text-gray-700">
            {{ $conta->tipoDocumento->descricao }}
        </span>
    @endif
</td>


            </tr>
        @empty
            <tr>
                <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                    Nenhuma conta encontrada.
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
