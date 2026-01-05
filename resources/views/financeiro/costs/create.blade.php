@extends('layouts.app')

@section('content')

<style>
:root{
    --brand-from: #f9821a;
    --brand-to:   #fc940d;
    --glass-bg: rgba(255,255,255,0.85);
    --card-radius: 14px;
    --shadow: 0 8px 28px rgba(22,22,22,0.08);
    --danger: #ff4d4f;
    --success: #16a34a;
    --warning: #facc15;
}

.card-modern{
    background: var(--glass-bg);
    border-radius: var(--card-radius);
    box-shadow: var(--shadow);
    backdrop-filter: blur(14px) saturate(1.3);
    animation: fadeIn .4s ease;
}

@keyframes fadeIn{
    from{opacity:0; transform: translateY(6px);}
    to{opacity:1; transform: translateY(0);}
}

.label-modern{
    font-size: .88rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 6px;
    display: block;
    letter-spacing: -0.2px;
}

.input-modern{
    width: 100%;
    padding: 0.75rem 0.85rem;
    border: 1px solid #d2d5da;
    border-radius: 12px;
    background: #fff;
    appearance: none;
    transition: all .20s ease;
    font-size: .95rem;
}

.input-modern:hover{
    border-color: #c4c7cc;
}

.input-modern:focus{
    outline: none;
    border-color: var(--brand-from);
    box-shadow: 0 0 0 3px rgba(249,130,26,0.24);
}

/* SELECT WRAPPER */
.select-wrapper{
    position: relative;
}

.select-wrapper::after{
    content: "▾";
    font-size: 0.9rem;
    color: #555;
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    pointer-events: none;
    opacity: 0.65;
}

select.input-modern option[disabled][selected]{
    color: #999;
}

.btn-brand{
    background: linear-gradient(90deg, var(--brand-from), var(--brand-to));
    color: white;
    padding: 0.95rem 1.5rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1.05rem;
    letter-spacing: -0.3px;
    box-shadow: 0 6px 18px rgba(249,130,26,0.32);
    transition: all .20s ease;
}

.btn-brand:hover{
    opacity: .93;
    transform: translateY(-2px);
    box-shadow: 0 10px 24px rgba(249,130,26,0.38);
}

/* VISIBILITY */
.hidden{
    display: none;
}
</style>


<div class="max-w-2xl mx-auto mt-10 card-modern p-8">

    <h2 class="text-3xl font-bold mb-8 text-neutral-800 tracking-tight">
        Criar novo lançamento
    </h2>

    {{-- =============================
         FORM START
    ============================== --}}
    <form action="{{ route('costs.store') }}" method="POST" id="cost-form">
        @csrf


        {{-- =============================
             CATEGORIA (PRIMEIRA ETAPA)
        ============================== --}}
        <div class="mb-8">
            <label class="label-modern">Categoria</label>

            <div class="select-wrapper">
                <select name="cost_base_id" id="categoria" class="input-modern" required>
                    <option value="" disabled selected>Selecione...</option>

                    @foreach($categories as $c)
                        <option value="{{ $c->id }}">
                            {{ $c->Categoria }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>


        {{-- =============================
             DADOS DO LANÇAMENTO
             (MOSTRA APENAS SE CATEGORIA ESCOLHIDA)
        ============================== --}}
        <div id="form-fields" class="hidden">


            {{-- Vencimento --}}
            <div class="mb-6">
                <label class="label-modern">Vencimento</label>
                <input 
                    type="date" 
                    name="vencimento" 
                    id="vencimento"
                    class="input-modern" 
                    required
                >
            </div>


            {{-- Valor --}}
            <div class="mb-6">
                <label class="label-modern">Valor (R$)</label>
                <input type="number" step="0.01" name="value" class="input-modern" required>
            </div>


            {{-- Mês --}}
            <div class="mb-6">
                <label class="label-modern">Mês de referência</label>
                <input 
                    type="month" 
                    name="month" 
                    id="month"
                    class="input-modern"
                    required
                >
            </div>


            {{-- Status --}}
            <div class="mb-8">
                <label class="label-modern">Status</label>

                <div class="select-wrapper">
                    <select name="status" class="input-modern" required>
                        <option value="" disabled selected>Selecione...</option>
                        <option value="pendente">Pendente</option>
                        <option value="pago">Pago</option>
                        <option value="atrasado">Atrasado</option>
                    </select>
                </div>
            </div>


            {{-- SUBMIT --}}
            <button class="btn-brand w-full text-center">
                Salvar lançamento
            </button>

        </div>
    </form>

</div>



{{-- ==================================
     SCRIPTS
================================== --}}
<script>
// Exibir o restante do form quando escolher categoria
document.getElementById('categoria').addEventListener('change', function(){
    document.getElementById('form-fields').classList.remove('hidden');
});


// Auto-preencher o mês baseado no vencimento
document.getElementById('vencimento').addEventListener('change', function () {
    if (!this.value) return;

    const data = new Date(this.value);
    const ano  = data.getFullYear();
    const mes  = String(data.getMonth() + 1).padStart(2, '0');

    document.getElementById('month').value = `${ano}-${mes}`;
});
</script>

@endsection
