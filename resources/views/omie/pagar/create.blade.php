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

/* ===== FORM SYSTEM ===== */
.label{
    font-size: .75rem;
    font-weight: 600;
    letter-spacing: .02em;
    color: var(--muted);
    margin-bottom: .35rem;
}
.input{
    width: 100%;
    padding: .55rem .75rem;
    font-size: .875rem;
    color: var(--text-primary);
    background: white;
    border-radius: var(--radius-md);
    border: 1px solid #e5e7eb;
}
.input:focus{
    outline: none;
    border-color: var(--brand-primary);
    box-shadow: 0 0 0 2px rgba(249,115,22,.15);
}
.error{
    font-size: .7rem;
    color: #dc2626;
    margin-top: .25rem;
}

/* ===== ALERTS ===== */
.alert{
    border-radius: var(--radius-md);
    padding: .9rem 1rem;
    font-size: .85rem;
    margin-bottom: 1rem;
}
.alert-success{
    background: #ecfdf5;
    border: 1px solid #86efac;
    color: #065f46;
}
.alert-error{
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #7f1d1d;
}

/* ===== AUTOCOMPLETE ===== */
.autocomplete{
    position: absolute;
    z-index: 50;
    margin-top: .25rem;
    width: 100%;
    background: white;
    border-radius: var(--radius-md);
    border: 1px solid #e5e7eb;
    box-shadow: var(--shadow-soft);
    max-height: 14rem;
    overflow-y: auto;
    display: none;
}
.autocomplete div{
    padding: .6rem .75rem;
    font-size: .8rem;
    cursor: pointer;
}
.autocomplete div:hover{
    background: #fff7ed;
}
</style>

<div class="max-w-5xl mx-auto px-6 py-10">

    {{-- HEADER --}}
    <div class="mb-10">
        <div class="text-xs text-[var(--muted)] mb-1">Financeiro · Contas a Pagar</div>
        <h1 class="text-2xl font-semibold text-[var(--text-primary)]">
            Novo Pagamento / Custo
        </h1>
        <p class="text-sm text-[var(--muted)] mt-1">
            Registre um novo pagamento informando fornecedor, documento, categoria, valores e datas.
        </p>
    </div>

    {{-- ALERTS --}}
    @if(session('success'))
        <div class="alert alert-success">
            ✅ {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-error">
            ❌ Não foi possível salvar. Verifique os campos obrigatórios abaixo.
        </div>
    @endif

    {{-- CARD --}}
    <div class="bg-[var(--soft-white)] border border-[var(--glass-border)]
                rounded-[var(--radius-lg)] shadow-[var(--shadow-soft)] p-8">

        <form action="{{ route('omie.pagar.store', ['empresa' => $empresaSlug]) }}" method="POST">
            @csrf
            <input type="hidden" name="empresa_codigo" value="{{ $empresaCodigo }}">
            <input type="hidden" name="empresa_slug" value="{{ $empresaSlug }}">

            {{-- CLASSIFICAÇÃO --}}
            <h3 class="text-sm font-semibold mb-4">Classificação</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

                {{-- STATUS --}}
                <div>
                    <label class="label">Status</label>
                    <select name="status_titulo" id="status_titulo" class="input">
                        <option value="A VENCER">A VENCER</option>
                        <option value="VENCE HOJE">VENCE HOJE</option>
                        <option value="PAGO">PAGO</option>
                        <option value="CANCELADO">CANCELADO</option>
                    </select>
                </div>

                {{-- TIPO DOCUMENTO (OBRIGATÓRIO) --}}
                <div>
                    <label class="label">Tipo de Documento *</label>
                    <select name="codigo_tipo_documento" class="input" required>
                        <option value="">Selecione...</option>
                        @foreach($tiposDocumento as $tipo)
                            <option value="{{ $tipo->codigo }}"
                                @selected(old('codigo_tipo_documento') == $tipo->codigo)>
                                {{ $tipo->descricao }}
                            </option>
                        @endforeach
                    </select>
                    @error('codigo_tipo_documento')<p class="error">{{ $message }}</p>@enderror
                </div>

                {{-- FORNECEDOR --}}
                <div class="relative">
                    <label class="label">Fornecedor *</label>
                    <input type="text" name="codigo_cliente_fornecedor" id="fornecedor"
                           class="input" autocomplete="off">
                    <div id="fornecedor-list" class="autocomplete"></div>
                    @error('codigo_cliente_fornecedor')<p class="error">{{ $message }}</p>@enderror
                </div>

                {{-- CATEGORIA --}}
                <div class="relative">
                    <label class="label">Categoria *</label>
                    <input type="text" name="codigo_categoria" id="categoria"
                           class="input" autocomplete="off">
                    <div id="categoria-list" class="autocomplete"></div>
                    @error('codigo_categoria')<p class="error">{{ $message }}</p>@enderror
                </div>

                {{-- CONTA --}}
                <div>
                    <label class="label">Conta Corrente</label>
                    <select name="id_conta_corrente" class="input">
                        <option value="">Selecione...</option>
                        @foreach($contasCorrentes as $conta)
                            <option value="{{ $conta->id }}">
                                {{ $conta->descricao ?? $conta->numero_conta_corrente }}
                            </option>
                        @endforeach
                    </select>
                </div>

            </div>

            {{-- DATAS --}}
            <h3 class="text-sm font-semibold mb-4">Datas e Valor</h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div>
                    <label class="label">Data de Emissão *</label>
                    <input type="date" name="data_emissao" class="input" required>
                </div>
                <div>
                    <label class="label">Data de Vencimento *</label>
                    <input type="date" name="data_vencimento" id="data_vencimento" class="input" required>
                </div>
                <div>
                    <label class="label">Valor *</label>
                    <input type="number" step="0.01" name="valor_documento" class="input" required>
                </div>
            </div>

            {{-- OBS --}}
            <div class="mb-8">
                <label class="label">Observações</label>
                <textarea name="info" rows="3" class="input"></textarea>
            </div>

            {{-- ACTION --}}
            <div class="flex justify-end">
                <button type="submit"
                        class="px-7 py-2.5 rounded-[var(--radius-md)] text-white text-sm font-semibold
                               bg-gradient-to-r from-[var(--brand-from)] to-[var(--brand-to)]
                               hover:opacity-95 transition">
                    Salvar Pagamento
                </button>
            </div>
        </form>
    </div>
</div>

{{-- JS --}}
<script>
const fornecedores = @json($fornecedores);
const categorias = @json($categorias);

function autocomplete(input, list, items, key, label) {
    input.addEventListener('input', () => {
        const val = input.value.toLowerCase();
        list.innerHTML = '';
        if (val.length < 2) return list.style.display = 'none';

        items.filter(i => (i[label] || '').toLowerCase().includes(val))
            .slice(0, 8)
            .forEach(i => {
                const div = document.createElement('div');
                div.textContent = i[label];
                div.onclick = () => {
                    input.value = i[key];
                    list.style.display = 'none';
                };
                list.appendChild(div);
            });

        list.style.display = 'block';
    });
}

autocomplete(fornecedor, document.getElementById('fornecedor-list'), fornecedores, 'codigo_cliente_omie', 'razao_social');
autocomplete(categoria, document.getElementById('categoria-list'), categorias, 'codigo', 'descricao');

/* STATUS DINÂMICO */
document.getElementById('data_vencimento').addEventListener('change', e => {
    const today = new Date().toISOString().split('T')[0];
    const status = document.getElementById('status_titulo');
    status.innerHTML = '';

    if (e.target.value === today) {
        ['VENCE HOJE','PAGO'].forEach(v => status.add(new Option(v,v)));
    } else {
        ['A VENCER','PAGO','CANCELADO'].forEach(v => status.add(new Option(v,v)));
    }
});
</script>
@endsection
