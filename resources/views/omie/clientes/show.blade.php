@extends('layouts.app')

@section('content')

<style>
    :root {
        --brand-orange: #f97316;
        --brand-orange-soft: #ffedd5;
        --brand-purple: #7c3aed;
        --brand-purple-soft: #ede9fe;

        --text-main: #111827;
        --text-muted: #6b7280;

        --radius-lg: 1rem;
        --radius-md: 0.75rem;

        --shadow-soft: 0 1px 2px rgba(0,0,0,.04),
                       0 10px 25px rgba(0,0,0,.06);
    }

    .card {
        background: #fff;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-soft);
        border: 1px solid #e5e7eb;
    }

    .section-title {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: var(--brand-purple);
    }

    .table-finance td {
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
        vertical-align: middle;
    }

    .table-finance tr:not(:last-child) {
        border-bottom: 1px solid #f1f5f9;
    }

    .label {
        color: var(--text-muted);
        font-weight: 500;
        width: 220px;
        white-space: nowrap;
    }

    .value {
        color: var(--text-main);
        font-weight: 600;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.6rem;
        font-size: 0.75rem;
        font-weight: 700;
        border-radius: 999px;
    }

    .badge-ok {
        background: var(--brand-orange-soft);
        color: var(--brand-orange);
    }

    .badge-purple {
        background: var(--brand-purple-soft);
        color: var(--brand-purple);
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--brand-orange);
    }

    .back-link:hover {
        text-decoration: underline;
    }
</style>

<div class="max-w-6xl mx-auto px-6 py-10 space-y-8">

    {{-- VOLTAR --}}
<a href="{{ route('omie.clientes.index', ['empresa' => $empresa]) }}" class="back-link">
        ← Voltar para clientes
    </a>

    {{-- HEADER --}}
    <div class="card p-6 space-y-2 border-l-4 border-orange-500">
        <h1 class="text-3xl font-semibold text-gray-900">
            {{ $cliente->razao_social }}
        </h1>

        <div class="flex flex-wrap gap-3 text-sm">
            <span class="badge badge-ok">
                {{ $cliente->cnpj_cpf }}
            </span>

            <span class="badge badge-purple">
                Código Omie: {{ data_get($cliente->payload, 'codigo_cliente_omie') }}
            </span>
        </div>
    </div>
<div class="flex justify-between items-start mb-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Detalhes do Cliente</h1>
        <p class="text-lg text-gray-600 mt-1">{{ $cliente->razao_social }}</p>
    </div>

    <div class="flex gap-3">
        <a href="{{ route('omie.clientes.index', $empresa) }}" class="btn-secondary">
            Voltar para lista
        </a>
        <a href="{{ route('omie.clientes.edit', ['empresa' => $empresa, 'cliente' => $cliente]) }}" class="btn-primary">
            Editar Cliente
        </a>
    </div>
</div>
    {{-- DADOS CADASTRAIS --}}
    <div class="card p-6 space-y-4">
        <div class="section-title">Dados Cadastrais</div>

        <table class="w-full table-finance">
            <tr>
                <td class="label">Razão Social</td>
                <td class="value">{{ data_get($cliente->payload, 'razao_social') ?: '—' }}</td>
            </tr>
            <tr>
                <td class="label">Nome Fantasia</td>
                <td class="value">{{ data_get($cliente->payload, 'nome_fantasia') ?: '—' }}</td>
            </tr>
            <tr>
                <td class="label">CNPJ / CPF</td>
                <td class="value font-mono">{{ data_get($cliente->payload, 'cnpj_cpf') }}</td>
            </tr>
            <tr>
                <td class="label">Pessoa Física</td>
                <td class="value">
                    {{ data_get($cliente->payload, 'pessoa_fisica') === 'S' ? 'Sim' : 'Não' }}
                </td>
            </tr>
            <tr>
                <td class="label">Cliente Inativo</td>
                <td class="value">
                    {{ data_get($cliente->payload, 'inativo') === 'S' ? 'Sim' : 'Não' }}
                </td>
            </tr>
        </table>
    </div>

    {{-- ENDEREÇO --}}
    <div class="card p-6 space-y-4">
        <div class="section-title">Endereço</div>

        <table class="w-full table-finance">
            <tr>
                <td class="label">Endereço</td>
                <td class="value">{{ data_get($cliente->payload, 'endereco') ?: '—' }}</td>
            </tr>
            <tr>
                <td class="label">Número / Complemento</td>
                <td class="value">
                    {{ data_get($cliente->payload, 'endereco_numero') ?: '—' }}
                    {{ data_get($cliente->payload, 'complemento') }}
                </td>
            </tr>
            <tr>
                <td class="label">Bairro</td>
                <td class="value">{{ data_get($cliente->payload, 'bairro') ?: '—' }}</td>
            </tr>
            <tr>
                <td class="label">Cidade / UF</td>
                <td class="value">
                    {{ data_get($cliente->payload, 'cidade') ?: '—' }} /
                    {{ data_get($cliente->payload, 'estado') ?: '—' }}
                </td>
            </tr>
            <tr>
                <td class="label">CEP</td>
                <td class="value">{{ data_get($cliente->payload, 'cep') ?: '—' }}</td>
            </tr>
        </table>
    </div>

    {{-- DADOS BANCÁRIOS --}}
    <div class="card p-6 space-y-4">
        <div class="section-title">Dados Bancários</div>

        <table class="w-full table-finance">
            <tr>
                <td class="label">Banco</td>
                <td class="value">{{ data_get($cliente->payload, 'dadosBancarios.codigo_banco') ?: '—' }}</td>
            </tr>
            <tr>
                <td class="label">Agência</td>
                <td class="value">{{ data_get($cliente->payload, 'dadosBancarios.agencia') ?: '—' }}</td>
            </tr>
            <tr>
                <td class="label">Conta Corrente</td>
                <td class="value">{{ data_get($cliente->payload, 'dadosBancarios.conta_corrente') ?: '—' }}</td>
            </tr>
            <tr>
                <td class="label">Titular</td>
                <td class="value">{{ data_get($cliente->payload, 'dadosBancarios.nome_titular') ?: '—' }}</td>
            </tr>
            <tr>
                <td class="label">PIX</td>
                <td class="value">{{ data_get($cliente->payload, 'dadosBancarios.cChavePix') ?: '—' }}</td>
            </tr>
        </table>
    </div>

    {{-- CONTROLE E AUDITORIA --}}
    <div class="card p-6 space-y-4">
        <div class="section-title">Controle & Auditoria</div>

        <table class="w-full table-finance">
            <tr>
                <td class="label">Data de Inclusão</td>
                <td class="value">{{ data_get($cliente->payload, 'info.dInc') }} às {{ data_get($cliente->payload, 'info.hInc') }}</td>
            </tr>
            <tr>
                <td class="label">Última Alteração</td>
                <td class="value">{{ data_get($cliente->payload, 'info.dAlt') }} às {{ data_get($cliente->payload, 'info.hAlt') }}</td>
            </tr>
            <tr>
                <td class="label">Usuário Alteração</td>
                <td class="value font-mono">{{ data_get($cliente->payload, 'info.uAlt') }}</td>
            </tr>
            <tr>
                <td class="label">Integração API</td>
                <td class="value">
                    {{ data_get($cliente->payload, 'info.cImpAPI') === 'S' ? 'Sim' : 'Não' }}
                </td>
            </tr>
        </table>
    </div>

</div>
@endsection
