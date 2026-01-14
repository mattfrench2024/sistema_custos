@extends('layouts.app')

@section('content')
<style>
    :root {
        --brand-orange: #ff6200ff;
        --brand-orange-soft: #ffedd5;
        --brand-purple: #f97316;
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
</style>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header --}}
    <div class="flex flex-col gap-1 mb-8">
        <h1 class="text-2xl font-semibold text-[var(--text-primary)] tracking-tight">
            Serviços
        </h1>
        <p class="text-sm text-[var(--text-secondary)]">
            Catálogo de serviços cadastrados — <span class="font-medium">{{ $empresaNome }}</span>
        </p>
    </div>

    {{-- Card --}}
    <div class="bg-white rounded-[var(--radius-lg)] shadow-[var(--shadow-soft)] border border-gray-100 overflow-hidden">

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
    <thead class="bg-[var(--surface-muted)] border-b border-gray-200">
        <tr class="text-left text-xs uppercase tracking-wide text-gray-500">
            <th class="px-6 py-4 font-medium">Código</th>
            <th class="px-6 py-4 font-medium">Descrição</th>
            <th class="px-6 py-4 font-medium">Categoria</th>
            <th class="px-6 py-4 font-medium text-right">Valor do Serviço</th>
            <th class="px-6 py-4"></th>
        </tr>
    </thead>

    <tbody class="divide-y divide-gray-100">
        @forelse ($servicos as $servico)
            <tr class="group hover:bg-gray-50 transition-colors">
                
                {{-- Código --}}
                <td class="px-6 py-4 font-mono text-xs text-gray-600">
                    {{ $servico->codigo }}
                </td>

                {{-- Descrição --}}
                <td class="px-6 py-4">
                    <div class="font-medium text-[var(--text-primary)]">
                        {{ $servico->descricao }}
                    </div>

                    @if($servico->inativo)
                        <span class="inline-flex items-center mt-1 text-xs font-medium text-red-600">
                            ● Inativo
                        </span>
                    @endif
                </td>

                {{-- Categoria --}}
                <td class="px-6 py-4 text-gray-700">
                    @if($servico->categoria)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-[var(--brand-purple-soft)] text-[var(--brand-purple)]">
                            {{ $servico->categoria->descricao }}
                        </span>
                    @else
                        <span class="text-xs text-gray-400">—</span>
                    @endif
                </td>

                {{-- Valor do Serviço --}}
                <td class="px-6 py-4 text-right">
                    <span class="font-semibold text-emerald-600">
                        R$ {{ number_format($servico->valor_total, 2, ',', '.') }}
                    </span>

                    @if($servico->preco_unitario == 0)
                        <div class="text-[10px] text-gray-400 mt-0.5">
                            valor informado na descrição
                        </div>
                    @endif
                </td>

                {{-- Ações --}}
                <td class="px-6 py-4 text-right">
                    <a
                        href="{{ route('omie.servicos.show', [$empresa, $servico]) }}"
                        class="
                            inline-flex items-center gap-1
                            px-3 py-1.5
                            text-xs font-medium
                            rounded-md
                            border border-gray-300
                            text-gray-700
                            hover:border-[var(--brand-orange)]
                            hover:text-[var(--brand-orange)]
                            hover:bg-[var(--brand-orange-soft)]
                            transition
                            focus:outline-none
                            focus:ring-2
                            focus:ring-offset-2
                            focus:ring-[var(--brand-orange)]
                        "
                    >
                        Ver
                    </a>
                </td>
            </tr>
        @empty
            {{-- Empty State --}}
            <tr>
                <td colspan="5" class="px-6 py-16 text-center">
                    <div class="flex flex-col items-center gap-2">
                        <div class="text-sm font-medium text-gray-700">
                            Nenhum serviço encontrado
                        </div>
                        <div class="text-xs text-gray-500 max-w-sm">
                            Não há serviços cadastrados para esta empresa no momento.
                        </div>
                    </div>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

        </div>

        {{-- Footer / Pagination --}}
        <div class="border-t border-gray-100 px-6 py-4 bg-white">
            {{ $servicos->links() }}
        </div>

    </div>
</div>
@endsection
