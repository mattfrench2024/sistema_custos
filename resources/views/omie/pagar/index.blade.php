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
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                @forelse($contas as $conta)
                    <tr class="row-hover">
                        <td class="px-5 py-4 font-medium text-gray-800">
                            {{ $conta->codigo_cliente_fornecedor ?? '—' }}
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
                            <span class="badge
                                {{ $conta->status_titulo === 'PAGO'
                                    ? 'badge-success'
                                    : 'badge-warning' }}">
                                {{ $conta->status_titulo ?? '—' }}
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
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-500">
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
