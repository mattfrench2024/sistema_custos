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
</style>

<div class="max-w-4xl mx-auto px-6 py-10">

    {{-- HEADER --}}
    <div class="mb-8">
        <div class="text-xs text-[var(--muted)] mb-1">
            Financeiro · Movimentos Financeiros
        </div>

        <h1 class="text-2xl font-semibold text-[var(--text-primary)]">
            {{ $origem === 'pagar' ? 'Registrar Pagamento' : 'Registrar Recebimento' }}
        </h1>

        <p class="text-sm text-[var(--muted)] mt-1">
            Este lançamento irá liquidar o título selecionado e movimentar a conta corrente.
        </p>
    </div>

    @if ($errors->any())
        <div class="alert-error mb-6">
            ❌ Não foi possível salvar o movimento. Verifique os campos obrigatórios.
        </div>
    @endif

    {{-- CARD --}}
    <div class="bg-[var(--soft-white)] border border-[var(--glass-border)]
                rounded-[var(--radius-lg)] shadow-[var(--shadow-soft)] p-8">

        <form action="{{ route('omie.movimentos.store', $empresa) }}" method="POST">
            @csrf

            {{-- HIDDEN --}}
            <input type="hidden" name="origem" value="{{ $origem }}">

{{-- Sempre envia um identificador numérico válido --}}
<input type="hidden" name="codigo_titulo"
       value="{{ $origem === 'pagar'
            ? ($titulo->codigo_lancamento_omie ?? $titulo->id)
            : ($titulo->codigo_lancamento_integracao ?? $titulo->id) }}">

            {{-- TÍTULO --}}
            <h3 class="text-sm font-semibold mb-4">Título Vinculado</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label class="label">Tipo</label>
                    <input class="input bg-gray-50" readonly
                        value="{{ strtoupper($origem) }}">
                </div>

                <div>
                    <label class="label">Envolvido</label>
                    <input class="input bg-gray-50" readonly
                        value="{{ $origem === 'pagar'
                            ? $titulo->nome_fornecedor
                            : $titulo->nome_cliente }}">
                </div>

                <div>
                    <label class="label">Vencimento</label>
                    <input class="input bg-gray-50" readonly
                        value="{{ optional($titulo->data_vencimento)->format('d/m/Y') }}">
                </div>

                <div>
                    <label class="label">Valor do Título</label>
                    <input class="input bg-gray-50" readonly
                        value="R$ {{ number_format($titulo->valor_documento,2,',','.') }}">
                </div>
            </div>

            {{-- MOVIMENTO --}}
            <h3 class="text-sm font-semibold mb-4">Movimento Financeiro</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

                <div>
                    <label class="label">Conta Corrente *</label>
                    <select name="codigo_conta_corrente" class="input" required>
                        <option value="">Selecione…</option>
                        @foreach ($contas as $conta)
                            <option value="{{ $conta->omie_cc_id }}">
                                {{ $conta->descricao ?? 'Conta #' . $conta->omie_cc_id }}
                            </option>
                        @endforeach
                    </select>
                    @error('codigo_conta_corrente') <p class="error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="label">Data do Movimento *</label>
                    <input type="date" name="data_movimento" class="input"
                           value="{{ now()->toDateString() }}" required>
                </div>

                <div>
                    <label class="label">Valor *</label>
                    <input type="number" step="0.01" name="valor" class="input"
                        value="{{ $origem === 'pagar'
                            ? -abs($titulo->valor_documento)
                            : abs($titulo->valor_documento) }}"
                        required>
                    @error('valor') <p class="error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="label">Observação</label>
                    <input type="text" name="observacao" class="input"
                           placeholder="Opcional">
                </div>
            </div>

            {{-- ACTIONS --}}
            <div class="flex justify-end gap-3 pt-6 border-t">
                <a href="{{ url()->previous() }}"
                   class="px-6 py-2 text-sm rounded border hover:bg-gray-50">
                    Cancelar
                </a>

                <button type="submit"
                    class="px-7 py-2.5 rounded text-white text-sm font-semibold
                           bg-gradient-to-r from-[var(--brand-from)] to-[var(--brand-to)]
                           hover:opacity-95 transition">
                    Registrar Movimento
                </button>
            </div>

        </form>
    </div>
</div>
@endsection
