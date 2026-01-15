@extends('layouts.app')

@section('content')
<style>
:root{
    --brand-from:#f97316;
    --brand-to:#fbbf24;
    --brand-primary:#f97316;
    --soft-white:rgba(255,255,255,.97);
    --glass-border:rgba(0,0,0,.06);
    --text-primary:#111827;
    --muted:#6b7280;
    --radius-lg:1rem;
    --radius-md:.75rem;
    --shadow-soft:
        0 1px 2px rgba(0,0,0,.04),
        0 12px 32px rgba(0,0,0,.08);
}

/* LABEL / INPUT */
.label{
    font-size:.7rem;
    font-weight:600;
    letter-spacing:.05em;
    text-transform:uppercase;
    color:var(--muted);
    margin-bottom:.35rem;
    display:block;
}
.input{
    width:100%;
    padding:.6rem .75rem;
    font-size:.875rem;
    border-radius:var(--radius-md);
    border:1px solid #e5e7eb;
    background:white;
}
.input:focus{
    outline:none;
    border-color:var(--brand-primary);
    box-shadow:0 0 0 2px rgba(249,115,22,.15);
}
.error{
    font-size:.7rem;
    color:#dc2626;
    margin-top:.25rem;
}
.alert-error{
    background:#fef2f2;
    color:#991b1b;
    padding:.75rem 1rem;
    border-radius:.75rem;
    font-size:.8rem;
}

/* AUTOCOMPLETE */
.autocomplete{
    position:absolute;
    z-index:50;
    margin-top:.25rem;
    width:100%;
    background:white;
    border-radius:.75rem;
    border:1px solid #e5e7eb;
    box-shadow:var(--shadow-soft);
    max-height:14rem;
    overflow-y:auto;
    display:none;
}
.autocomplete div{
    padding:.55rem .75rem;
    font-size:.8rem;
    cursor:pointer;
}
.autocomplete div:hover{
    background:#fff7ed;
}

/* STATUS BADGE */
.status{
    font-size:.7rem;
    font-weight:600;
    padding:.25rem .5rem;
    border-radius:.5rem;
    display:inline-block;
}
.status-pendente{background:#fffbeb;color:#92400e;}
.status-vencido{background:#fef2f2;color:#991b1b;}
.status-hoje{background:#ecfeff;color:#155e75;}
</style>

<div class="max-w-5xl mx-auto px-6 py-10">

    {{-- HEADER --}}
    <div class="mb-10">
        <div class="text-xs text-[var(--muted)] mb-1">
            Financeiro · Contas a Receber
        </div>
        <h1 class="text-2xl font-semibold text-[var(--text-primary)]">
            Novo Recebimento
        </h1>
        <p class="text-sm text-[var(--muted)] mt-1">
            Registre um valor a receber vinculando cliente, categoria, conta, valor e vencimento.
        </p>
    </div>

    @if ($errors->any())
        <div class="alert-error mb-6">
            ❌ Não foi possível salvar. Verifique os campos obrigatórios.
        </div>
    @endif

    {{-- CARD --}}
    <div class="bg-[var(--soft-white)] border border-[var(--glass-border)]
                rounded-[var(--radius-lg)] shadow-[var(--shadow-soft)] p-8">

        <form action="{{ route('omie.receber.store', $empresa) }}" method="POST">
            @csrf

            {{-- CLASSIFICAÇÃO --}}
            <h3 class="text-sm font-semibold mb-4">Classificação</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

                {{-- CLIENTE --}}
                <div class="relative">
                    <label class="label">Cliente *</label>
                    <input type="text" id="cliente" class="input" placeholder="Digite ao menos 2 caracteres…" autocomplete="off" required>
                    <input type="hidden" name="codigo_cliente_fornecedor" id="cliente_codigo">
                    <div id="cliente-list" class="autocomplete"></div>
                    @error('codigo_cliente_fornecedor') <p class="error">{{ $message }}</p> @enderror
                </div>

                {{-- CATEGORIA --}}
                <div class="relative">
                    <label class="label">Categoria *</label>
                    <input type="text" id="categoria" class="input" placeholder="Digite ao menos 2 caracteres…" autocomplete="off" required>
                    <input type="hidden" name="codigo_categoria" id="categoria_codigo">
                    <div id="categoria-list" class="autocomplete"></div>
                    @error('codigo_categoria') <p class="error">{{ $message }}</p> @enderror
                </div>

            </div>

            {{-- CONTA + VALOR --}}
            <h3 class="text-sm font-semibold mb-4">Conta e Valor</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

                <div>
                    <label class="label">Conta Corrente *</label>
                    <select name="id_conta_corrente" class="input" required>
                        <option value="">Selecione…</option>
                        @foreach ($contas ?? [] as $conta)
                            <option value="{{ $conta->id }}">{{ $conta->descricao ?? 'Conta #' . $conta->id }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="label">Valor do Documento *</label>
                    <input type="number" step="0.01" name="valor_documento" class="input" required>
                </div>

            </div>

            {{-- VENCIMENTO --}}
            <h3 class="text-sm font-semibold mb-4">Vencimento</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                <div>
                    <label class="label">Data de Vencimento *</label>
                    <input type="date" name="data_vencimento" id="vencimento" class="input" required>
                </div>

                <div class="flex items-end">
                    <div id="statusPreview" class="status status-pendente">
                        Status: Pendente
                    </div>
                </div>
            </div>

            {{-- ACTIONS --}}
            <div class="flex justify-end gap-3 pt-6 border-t">
                <a href="{{ route('omie.receber.index', $empresa) }}"
                   class="px-6 py-2 text-sm rounded border hover:bg-gray-50">
                    Cancelar
                </a>

                <button type="submit"
                    class="px-7 py-2.5 rounded text-white text-sm font-semibold
                           bg-gradient-to-r from-[var(--brand-from)] to-[var(--brand-to)]
                           hover:opacity-95 transition">
                    Salvar Recebimento
                </button>
            </div>

        </form>
    </div>
</div>

{{-- DATA --}}
<script>
const clientes = @json($clientes->map(fn($c)=>[
    'codigo'=>$c->codigo_cliente_omie,
    'nome'=>$c->nome_fantasia ?? $c->razao_social
]));

const categorias = @json($categorias->map(fn($c)=>[
    'codigo'=>$c->codigo,
    'nome'=>$c->descricao
]));

function autocomplete(input, hidden, list, items){
    input.addEventListener('input', ()=>{
        const v=input.value.toLowerCase();
        list.innerHTML='';
        hidden.value='';
        if(v.length<2){list.style.display='none';return;}
        items.filter(i=>i.nome.toLowerCase().includes(v)).slice(0,8).forEach(i=>{
            const d=document.createElement('div');
            d.textContent=i.nome;
            d.onclick=()=>{input.value=i.nome;hidden.value=i.codigo;list.style.display='none';};
            list.appendChild(d);
        });
        list.style.display='block';
    });
}

autocomplete(cliente, cliente_codigo, document.getElementById('cliente-list'), clientes);
autocomplete(categoria, categoria_codigo, document.getElementById('categoria-list'), categorias);

// STATUS PREVIEW
document.getElementById('vencimento').addEventListener('change', e=>{
    const hoje = new Date().toISOString().split('T')[0];
    const el = document.getElementById('statusPreview');
    if(e.target.value < hoje){
        el.textContent='Status: Vencido';
        el.className='status status-vencido';
    }else if(e.target.value === hoje){
        el.textContent='Status: Vence Hoje';
        el.className='status status-hoje';
    }else{
        el.textContent='Status: Pendente';
        el.className='status status-pendente';
    }
});
</script>
@endsection
