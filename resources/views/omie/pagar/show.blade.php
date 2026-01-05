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

    .label {
        font-size: .75rem;
        color: var(--text-secondary);
        letter-spacing: .02em;
    }

    .value {
        font-weight: 600;
        color: var(--text-primary);
    }

    .badge {
        display: inline-flex;
        align-items: center;
        padding: 0.35rem 0.75rem;
        border-radius: 9999px;
        font-size: .7rem;
        font-weight: 600;
        letter-spacing: .04em;
    }

    .badge-success {
        background: #dcfce7;
        color: #166534;
    }

    .badge-warning {
        background: var(--brand-orange-soft);
        color: #9a3412;
    }

    .back-link {
        font-size: .8rem;
        color: var(--text-secondary);
        transition: color .15s ease;
    }

    .back-link:hover {
        color: var(--text-primary);
    }

    .json-box {
        background: var(--surface-muted);
        border-radius: var(--radius-md);
        font-size: .7rem;
        line-height: 1.5;
    }
</style>

<div class="max-w-5xl mx-auto px-6 py-8">

    {{-- Header --}}
    <div class="mb-8">
        <a href="{{ route('omie.pagar.index', ['empresa' => $empresaSlug]) }}" class="back-link">
    ← Voltar para Contas a Pagar
</a>


        <h1 class="mt-2 text-2xl font-semibold text-[var(--text-primary)] tracking-tight">
            Detalhe da Conta a Pagar
        </h1>
    </div>

    {{-- Card principal --}}
    <div class="card p-6 space-y-8">

        {{-- Grid de informações --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">

            <div>
                <span class="label">Fornecedor</span>
                <p class="value mt-1">
                    {{ $conta->codigo_cliente_fornecedor ?? '—' }}
                </p>
            </div>

            <div>
                <span class="label">Status</span>
                <div class="mt-1">
                    <span class="badge
                        {{ $conta->status_titulo === 'PAGO'
                            ? 'badge-success'
                            : 'badge-warning' }}">
                        {{ $conta->status_titulo ?? '—' }}
                    </span>
                </div>
            </div>

            <div>
                <span class="label">Data de Emissão</span>
                <p class="value mt-1">
                    {{ optional($conta->data_emissao)->format('d/m/Y') ?? '—' }}
                </p>
            </div>

            <div>
                <span class="label">Data de Vencimento</span>
                <p class="value mt-1">
                    {{ optional($conta->data_vencimento)->format('d/m/Y') ?? '—' }}
                </p>
            </div>

            <div>
                <span class="label">Valor</span>
                <p class="mt-1 text-2xl font-bold text-[var(--brand-orange)]">
                    R$ {{ number_format($conta->valor_documento, 2, ',', '.') }}
                </p>
            </div>

        </div>

        {{-- Informações Técnicas --}}
        <div class="border-t border-gray-100 pt-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">
                Informações Técnicas
            </h3>

            <pre class="json-box p-4 overflow-auto">
{{ json_encode($conta->info, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}
            </pre>
        </div>

    </div>

</div>
@endsection
