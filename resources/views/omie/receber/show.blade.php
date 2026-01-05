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

/* Base */
h1,h2,h3 { color: var(--text-primary); }
p { color: var(--text-secondary); }

/* Card */
.card {
    background: var(--surface);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-soft);
    padding: 1.5rem;
}

/* Labels */
.label {
    font-size: .7rem;
    letter-spacing: .04em;
    text-transform: uppercase;
    color: var(--text-secondary);
}

/* Values */
.value {
    font-size: 1rem;
    font-weight: 600;
    color: var(--text-primary);
}

/* Status */
.badge {
    display: inline-flex;
    align-items: center;
    padding: .35rem .75rem;
    font-size: .7rem;
    font-weight: 600;
    border-radius: 9999px;
}
.status-recebido { background: #dcfce7; color: #166534; }
.status-pendente { background: #fef3c7; color: #78350f; }
.status-atrasado { background: #fee2e2; color: #991b1b; }

/* Section */
.section {
    margin-top: 2rem;
}
.section-title {
    font-size: .9rem;
    font-weight: 600;
    color: var(--text-secondary);
    margin-bottom: .75rem;
}

/* Payload */
.payload-box {
    background: var(--surface-muted);
    border-radius: var(--radius-md);
    font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
    font-size: .75rem;
    line-height: 1.6;
}

/* Back link */
.back-link {
    font-size: .8rem;
    color: var(--text-secondary);
    transition: color .15s ease;
}
.back-link:hover {
    color: var(--text-primary);
}
</style>

<div class="max-w-5xl mx-auto px-6 py-8 space-y-8">

    {{-- Header --}}
    <div>
        <a href="{{ route('omie.receber.index', $empresa) }}" class="back-link">
            ← Voltar para Contas a Receber
        </a>

        <h1 class="mt-2 text-2xl font-semibold tracking-tight">
            Detalhe da Conta a Receber
        </h1>
    </div>

    {{-- Resumo --}}
    <div class="card">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <span class="label">Código Omie</span>
                <p class="value mt-1">{{ $receber->codigo_lancamento_omie }}</p>
            </div>

            <div>
                <span class="label">Cliente</span>
                <p class="value mt-1">{{ $receber->codigo_cliente_fornecedor }}</p>
            </div>

            <div>
                <span class="label">Vencimento</span>
                <p class="value mt-1">{{ $receber->data_vencimento?->format('d/m/Y') }}</p>
            </div>

            <div>
                <span class="label">Valor</span>
                <p class="mt-1 text-xl font-bold text-[var(--brand-orange)]">
                    R$ {{ number_format($receber->valor_documento, 2, ',', '.') }}
                </p>
            </div>
        </div>
    </div>

    {{-- Detalhes --}}
    <div class="card">
        <h2 class="section-title">Detalhes Financeiros</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <span class="label">Data de Previsão</span>
                <p class="value mt-1">{{ $receber->data_previsao?->format('d/m/Y') ?? '—' }}</p>
            </div>

            <div>
                <span class="label">Categoria</span>
                <p class="value mt-1">{{ $receber->codigo_categoria ?? '—' }}</p>
            </div>

            <div>
                <span class="label">Conta Corrente</span>
                <p class="value mt-1">{{ $receber->id_conta_corrente ?? '—' }}</p>
            </div>

            <div>
                <span class="label">Status</span>
                @php
                    $statusClass = match(strtolower($receber->status)) {
                        'recebido' => 'status-recebido',
                        'pendente' => 'status-pendente',
                        'atrasado' => 'status-atrasado',
                        default => 'bg-gray-100 text-gray-700',
                    };
                @endphp
                <div class="mt-1">
                    <span class="badge {{ $statusClass }}">{{ ucfirst($receber->status) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Payload Técnico --}}
    @if(!empty($receber->payload))
    <div class="card">
        <h2 class="section-title">Informações Técnicas (Payload Omie)</h2>

        <details class="mt-2">
            <summary class="cursor-pointer text-sm text-[var(--brand-purple)] font-medium">
                Visualizar payload completo
            </summary>

            <pre class="payload-box mt-4 p-4 overflow-auto">
{{ json_encode($receber->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}
            </pre>
        </details>
    </div>
    @endif

</div>
@endsection
