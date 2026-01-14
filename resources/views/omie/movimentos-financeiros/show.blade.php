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

    .badge {
        padding: .3rem .9rem;
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

    .badge-neutral {
        background: rgba(107,114,128,.15);
        color: #374151;
    }

    .label {
        font-size: .75rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: var(--muted);
    }

    .value {
        font-weight: 600;
        color: var(--text-primary);
    }

    .link {
        color: var(--brand-primary);
        font-weight: 600;
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
                    Movimento Financeiro
                </h1>
                <p class="opacity-90 text-sm">
                    {{ $empresa }}
                </p>
            </div>

            <div
                class="glass px-4 py-2 text-sm rounded-lg"
                style="background: rgba(255,255,255,.18);"
            >
                Código:
                <strong>{{ $movimento->codigo_movimento }}</strong>
            </div>

        </div>
    </div>

    {{-- AÇÕES + STATUS --}}
    <div class="flex justify-between items-center">

        <a
            href="{{ route('omie.movimentos.index', $empresa) }}"
            class="link text-sm"
        >
            ← Voltar para listagem
        </a>

        <span
            class="badge
                @if($movimento->isEntradaGerencial())
                    badge-credit
                @elseif($movimento->isSaidaGerencial())
                    badge-debit
                @else
                    badge-neutral
                @endif
            "
        >
            {{ $movimento->tipo_label }}
        </span>

    </div>

    {{-- DADOS PRINCIPAIS --}}
    <div class="card p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <div>
                <div class="label">Data do movimento</div>
                <div class="value">
                    {{ optional($movimento->data_movimento)->format('d/m/Y') }}
                </div>
            </div>

            <div>
                <div class="label">Data de competência</div>
                <div class="value">
                    {{ optional($movimento->data_competencia)->format('d/m/Y') ?? '—' }}
                </div>
            </div>

            <div>
                <div class="label">Origem</div>
                <div class="value">
                    {{ $movimento->grupo_label }}
                </div>
            </div>

        </div>
    </div>

    {{-- VALOR --}}
    <div class="card p-6">
        <div class="label mb-1">Valor do movimento</div>

        <div
            class="text-3xl font-bold
                @if($movimento->isEntradaGerencial())
                    text-green-600
                @elseif($movimento->isSaidaGerencial())
                    text-red-600
                @else
                    text-gray-500
                @endif
            "
        >
            R$ {{ number_format($movimento->valor, 2, ',', '.') }}
        </div>
    </div>

    {{-- DADOS TÉCNICOS --}}
    <div class="card p-6">
        <h2 class="text-lg font-semibold mb-4">
            Informações técnicas
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">

            <div>
                <div class="label">Código do lançamento (Omie)</div>
                <div class="value">
                    {{ $movimento->codigo_lancamento_omie ?? '—' }}
                </div>
            </div>

            <div>
                <div class="label">Código do título</div>
                <div class="value">
                    {{ $movimento->codigo_titulo ?? '—' }}
                </div>
            </div>

            <div>
                <div class="label">Conta corrente</div>
                <div class="value">
                    {{ $movimento->codigo_conta_corrente ?? '—' }}
                </div>
            </div>

            <div>
                <div class="label">Empresa</div>
                <div class="value">
                    {{ $movimento->empresa }}
                </div>
            </div>

        </div>
    </div>

    {{-- PAYLOAD COMPLETO --}}
    <div class="card p-6">
        <h2 class="text-lg font-semibold mb-4">
            Payload completo (Omie)
        </h2>

        <pre class="text-xs bg-gray-900 text-green-200 rounded-lg p-4 overflow-x-auto">
{{ json_encode($movimento->info, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}
        </pre>
    </div>

</div>

@endsection
