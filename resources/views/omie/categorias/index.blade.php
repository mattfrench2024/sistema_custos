@extends('layouts.app')

@section('title', 'Categorias Financeiras')

@section('content')
<style>
:root {
    --brand-primary: #f97316;
    --brand-soft: #ffedd5;

    --danger: #dc2626;
    --danger-soft: #fee2e2;

    --success: #16a34a;
    --success-soft: #dcfce7;

    --surface: #ffffff;
    --surface-muted: #f9fafb;

    --text-primary: #111827;
    --text-secondary: #6b7280;

    --radius-lg: 14px;
    --radius-md: 10px;

    --shadow-soft:
        0 1px 2px rgba(0,0,0,.04),
        0 10px 28px rgba(0,0,0,.08);
}

    button {
        background: var(--brand-primary);
        color: #f97316;
        border-radius: var(--radius-md);
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        font-weight: 500;
        box-shadow: var(--shadow-soft);
        border: none;
        cursor: pointer;
        transition: background 0.2s ease-in-out;
    }

</style>

<div class="max-w-7xl mx-auto px-6 py-8 text-[var(--text-primary)]">

    {{-- HEADER --}}
    <div class="mb-8">
        <h1 class="text-2xl font-semibold">Categorias Financeiras</h1>
        <p class="text-sm text-[var(--text-secondary)]">
            {{ $empresaLabel }} • Estrutura contábil
        </p>
    </div>

    {{-- FILTROS --}}
    <form class="mb-8 grid grid-cols-1 md:grid-cols-5 gap-4">
    <input
        name="search"
        value="{{ request('search') }}"
        placeholder="Buscar código ou descrição"
        class="rounded-[var(--radius-md)] border px-4 py-2 text-sm"
    >

    <select name="tipo" class="rounded-[var(--radius-md)] border px-3 py-2 text-sm">
        <option value="">Todos os tipos</option>
        <option value="receita" @selected(request('tipo')==='receita')>Receita</option>
        <option value="despesa" @selected(request('tipo')==='despesa')>Despesa</option>
        <option value="transferencia" @selected(request('tipo')==='transferencia')>Transferência</option>
    </select>

    <select name="status" class="rounded-[var(--radius-md)] border px-3 py-2 text-sm">
        <option value="">Ativas e Inativas</option>
        <option value="ativa" @selected(request('status')==='ativa')>Ativas</option>
        <option value="inativa" @selected(request('status')==='inativa')>Inativas</option>
    </select>

    {{-- 🔥 NOVO FILTRO --}}
    <select name="movimentacao" class="rounded-[var(--radius-md)] border px-3 py-2 text-sm">
        <option value="">Com e sem movimentação</option>
        <option value="com" @selected(request('movimentacao')==='com')>Com movimentação</option>
        <option value="sem" @selected(request('movimentacao')==='sem')>Sem movimentação</option>
    </select>

    <button
        class="bg-[var(--brand-primary)] text-white rounded-[var(--radius-md)] text-sm font-medium hover:opacity-90">
        Aplicar filtros
    </button>
</form>


    {{-- TABELA --}}
    <div class="overflow-hidden bg-[var(--surface)] border border-gray-100"
         style="border-radius: var(--radius-lg); box-shadow: var(--shadow-soft);">

        <table class="min-w-full text-sm">
            <thead class="bg-[var(--surface-muted)] border-b">
                <tr class="text-xs uppercase tracking-wide text-[var(--text-secondary)]">
                    <th class="px-5 py-4 text-left">Código</th>
                    <th class="px-5 py-4 text-left">Categoria</th>
                    <th class="px-5 py-4 text-left">Tipo</th>
                    <th class="px-5 py-4 text-left">DRE</th>
                    <th class="px-5 py-4 text-right">Ação</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                @forelse ($categorias as $categoria)
                    <tr class="hover:bg-[var(--surface-muted)] transition">
                        <td class="px-5 py-4 font-mono text-xs text-gray-700">
                            {{ $categoria->codigo }}
                        </td>

                        <td class="px-5 py-4">
                            <div class="font-medium text-sm">
                                {{ $categoria->descricao }}
                            </div>
                            @if($categoria->natureza)
                                <div class="mt-1 text-xs text-[var(--text-secondary)] max-w-xl">
                                    {{ Str::limit($categoria->natureza, 120) }}
                                </div>
                            @endif
                        </td>

                        <td class="px-5 py-4">
                            @if($categoria->conta_receita)
                                <span class="px-3 py-1 rounded-full text-xs font-medium"
                                      style="background: var(--success-soft); color: var(--success);">
                                    Receita
                                </span>
                            @elseif($categoria->conta_despesa)
                                <span class="px-3 py-1 rounded-full text-xs font-medium"
                                      style="background: var(--danger-soft); color: var(--danger);">
                                    Despesa
                                </span>
                            @elseif($categoria->transferencia)
                                <span class="px-3 py-1 rounded-full text-xs font-medium"
                                      style="background: var(--brand-soft); color: var(--brand-primary);">
                                    Transferência
                                </span>
                            @else
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>

                        <td class="px-5 py-4 text-sm text-gray-600">
                            {{ $categoria->codigo_dre ?: '—' }}
                        </td>

                       <td class="px-5 py-4 text-right">
    <a
        href="{{ route('omie.categorias.show', [$empresa, $categoria->codigo]) }}"
        class="inline-flex items-center gap-1.5
               px-3 py-1.5
               text-xs font-medium
               rounded-[var(--radius-md)]
               border
               transition-all duration-200
               group"
        style="
            color: var(--brand-primary);
            border-color: var(--brand-soft);
            background: transparent;
        "
        onmouseover="this.style.background='var(--brand-soft)'"
        onmouseout="this.style.background='transparent'"
    >
        Ver detalhes
        <span class="transition-transform duration-200 group-hover:translate-x-0.5">
            →
        </span>
    </a>
</td>


                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">
                            Nenhuma categoria encontrada com os filtros aplicados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- PAGINAÇÃO --}}
        <div class="px-6 py-4 border-t bg-[var(--surface-muted)]">
            {{ $categorias->links() }}
        </div>
    </div>
</div>
@endsection
