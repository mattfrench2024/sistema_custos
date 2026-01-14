@extends('layouts.app')

@section('content')

<style>
    :root {
        --brand-orange: #ff6200;
        --brand-orange-soft: #ffedd5;

        --surface: #ffffff;
        --surface-muted: #f9fafb;

        --text-primary: #111827;
        --text-secondary: #6b7280;

        --radius-lg: 1rem;
        --radius-md: .75rem;

        --shadow-soft:
            0 1px 2px rgba(0,0,0,.04),
            0 12px 32px rgba(0,0,0,.08);
    }

    .json-block {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: .75rem;
        line-height: 1.5;
    }
</style>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Top Bar --}}
    <div class="flex items-center justify-between mb-8">
        <div class="flex flex-col gap-1">
            <h1 class="text-2xl font-semibold tracking-tight text-[var(--text-primary)]">
                {{ $servico->descricao }}
            </h1>

            <p class="text-sm text-[var(--text-secondary)]">
                Detalhes completos do serviço cadastrado na Omie
            </p>
        </div>

        <a
            href="{{ route('omie.servicos.index', $empresa) }}"
            class="
                inline-flex items-center gap-2
                px-4 py-2
                text-sm font-medium
                rounded-md
                border border-gray-300
                text-gray-700
                hover:border-[var(--brand-orange)]
                hover:text-[var(--brand-orange)]
                hover:bg-[var(--brand-orange-soft)]
                transition
            "
        >
            ← Voltar
        </a>
    </div>

    {{-- GRID --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- COLUNA ESQUERDA --}}
        <div class="space-y-6 lg:col-span-1">

            {{-- Card: Dados Gerais --}}
            <div class="bg-white rounded-[var(--radius-lg)] shadow-[var(--shadow-soft)] border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-800 uppercase tracking-wide">
                        Dados do Serviço
                    </h2>
                </div>

                <div class="px-6 py-5 space-y-4 text-sm">

                    <div>
                        <p class="text-xs text-gray-500">Código</p>
                        <p class="font-mono text-gray-800">{{ $servico->codigo }}</p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">Categoria</p>
                        @if($servico->categoria)
                            <span class="inline-flex mt-1 px-2.5 py-1 rounded-md text-xs font-medium bg-indigo-50 text-indigo-600">
                                {{ $servico->categoria->descricao }}
                            </span>
                        @else
                            <span class="text-xs text-gray-400">—</span>
                        @endif
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">Preço Unitário</p>
                        <p class="font-semibold text-gray-900">
                            R$ {{ number_format($servico->preco_unitario ?? 0, 2, ',', '.') }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">Status</p>
                        @if($servico->inativo)
                            <span class="inline-flex items-center gap-1 text-xs font-medium text-red-600">
                                ● Inativo
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-xs font-medium text-green-600">
                                ● Ativo
                            </span>
                        @endif
                    </div>

                </div>
            </div>

            {{-- Card: Metadados --}}
            <div class="bg-white rounded-[var(--radius-lg)] shadow-[var(--shadow-soft)] border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-800 uppercase tracking-wide">
                        Metadados
                    </h2>
                </div>

                <div class="px-6 py-5 space-y-3 text-xs text-gray-600">
                    <div>
                        <span class="font-medium">Importado via API:</span>
                        {{ $servico->importado_api ? 'Sim' : 'Não' }}
                    </div>

                    <div>
                        <span class="font-medium">Empresa:</span>
                        {{ $servico->empresa }}
                    </div>
                </div>
            </div>

        </div>

        {{-- COLUNA DIREITA --}}
        <div class="space-y-6 lg:col-span-2">

            {{-- Card: Tributação --}}
            <div class="bg-white rounded-[var(--radius-lg)] shadow-[var(--shadow-soft)] border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-800 uppercase tracking-wide">
                        Tributação
                    </h2>
                </div>

                <div class="px-6 py-5 bg-[var(--surface-muted)]">
                    @if(!empty($servico->impostos))
                        <pre class="json-block text-gray-700 overflow-auto">
{{ json_encode($servico->impostos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}
                        </pre>
                    @else
                        <p class="text-sm text-gray-500">
                            Nenhuma informação de tributação disponível.
                        </p>
                    @endif
                </div>
            </div>

            {{-- Card: Payload Omie --}}
            <div class="bg-white rounded-[var(--radius-lg)] shadow-[var(--shadow-soft)] border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-800 uppercase tracking-wide">
                        Payload Completo (Omie)
                    </h2>
                </div>

                <div class="px-6 py-5 bg-[var(--surface-muted)]">
                    <pre class="json-block text-gray-700 overflow-auto max-h-[420px]">
{{ json_encode($servico->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}
                    </pre>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection
