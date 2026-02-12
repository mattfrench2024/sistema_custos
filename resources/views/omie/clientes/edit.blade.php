@extends('layouts.app')

@section('content')
@push('styles')
<style>
    :root {
        --accent: #f97316;
        --accent-hover: #ea580c;
    }

    .header-title {
        font-size: 2.25rem;
        font-weight: 800;
        color: #1e293b;
        background: linear-gradient(135deg, #1e293b, var(--accent));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .header-subtitle {
        font-size: 1.125rem;
        color: #64748b;
        margin-top: 0.5rem;
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.90);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(226, 232, 240, 0.8);
        border-radius: 1rem;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.15);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
    }

    .glass-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.2);
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1e293b;
        position: relative;
        padding-bottom: 0.75rem;
    }

    .section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 80px;
        height: 4px;
        background: var(--accent);
        border-radius: 2px;
    }

    .filter-button {
        background: var(--accent);
        color: white;
        font-weight: 600;
        padding: 0.75rem 2rem;
        border-radius: 0.5rem;
        transition: all 0.3s ease;
    }

    .filter-button:hover {
        background: var(--accent-hover);
        transform: translateY(-2px);
    }

    .filter-button.bg-gray-600 {
        background: #475569;
    }

    .filter-button.bg-gray-600:hover {
        background: #374151;
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .animate-fade-in {
        animation: fadeInUp 0.8s ease-out forwards;
    }
</style>
<div class="container mx-auto px-4 py-12 max-w-5xl">
    <form action="{{ route('omie.clientes.update', ['empresa' => $empresa, 'cliente' => $cliente]) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-8 mb-12 animate-fade-in">
            <div>
                <h1 class="header-title">
                    Editar Cliente
                </h1>
                <p class="header-subtitle">
                    {{ $empresaLabel }} • {{ $cliente->razao_social ?? 'Novo Cliente' }}
                </p>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('omie.clientes.show', ['empresa' => $empresa, 'cliente' => $cliente]) }}"
                   class="filter-button bg-gray-600 hover:bg-gray-700">
                    Cancelar
                </a>
                <button type="submit" class="filter-button">
                    Salvar Alterações
                </button>
            </div>
        </div>

        @if (session('success'))
            <div class="glass-card p-6 mb-8 border-l-4 border-green-500 bg-green-50 animate-fade-in">
                <p class="text-green-800 font-medium">{{ session('success') }}</p>
            </div>
        @endif

        <!-- DADOS CADASTRAIS -->
        <div class="glass-card p-8 mb-10 animate-fade-in" style="animation-delay: 0.1s;">
            <h2 class="section-title">Dados Cadastrais</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-8">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Razão Social <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="payload[razao_social]"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-4 focus:ring-orange-100 transition"
                           value="{{ old('payload.razao_social', data_get($cliente->payload, 'razao_social')) }}"
                           required>
                    @error('payload.razao_social')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nome Fantasia</label>
                    <input type="text"
                           name="payload[nome_fantasia]"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-4 focus:ring-orange-100 transition"
                           value="{{ old('payload.nome_fantasia', data_get($cliente->payload, 'nome_fantasia')) }}">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        CNPJ / CPF <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="payload[cnpj_cpf]"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 font-mono focus:border-accent focus:ring-4 focus:ring-orange-100 transition"
                           value="{{ old('payload.cnpj_cpf', data_get($cliente->payload, 'cnpj_cpf')) }}"
                           required>
                    @error('payload.cnpj_cpf')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">E-mail</label>
                    <input type="email"
                           name="payload[email]"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-4 focus:ring-orange-100 transition"
                           value="{{ old('payload.email', data_get($cliente->payload, 'email')) }}">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Telefone</label>
                    <input type="text"
                           name="payload[telefone]"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-4 focus:ring-orange-100 transition"
                           value="{{ old('payload.telefone', data_get($cliente->payload, 'telefone')) }}">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Pessoa Física <span class="text-red-500">*</span>
                    </label>
                    <select name="payload[pessoa_fisica]"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-4 focus:ring-orange-100 transition"
                            required>
                        <option value="N" {{ old('payload.pessoa_fisica', data_get($cliente->payload, 'pessoa_fisica', 'N')) === 'N' ? 'selected' : '' }}>
                            Não (Pessoa Jurídica)
                        </option>
                        <option value="S" {{ old('payload.pessoa_fisica', data_get($cliente->payload, 'pessoa_fisica')) === 'S' ? 'selected' : '' }}>
                            Sim (Pessoa Física)
                        </option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Tags (separadas por vírgula)
                    </label>
                    <input type="text"
                           name="tags"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-4 focus:ring-orange-100 transition"
                           value="{{ old('tags', collect(data_get($cliente->payload, 'tags', []))->pluck('tag')->implode(', ')) }}"
                           placeholder="Ex: VIP, Revenda, Fornecedor">
                </div>
            </div>
        </div>

        <!-- ENDEREÇO -->
        <div class="glass-card p-8 mb-10 animate-fade-in" style="animation-delay: 0.2s;">
            <h2 class="section-title">Endereço</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-8">
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Logradouro</label>
                    <input type="text"
                           name="payload[endereco]"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-4 focus:ring-orange-100 transition"
                           value="{{ old('payload.endereco', data_get($cliente->payload, 'endereco')) }}">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Número</label>
                    <input type="text"
                           name="payload[endereco_numero]"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-4 focus:ring-orange-100 transition"
                           value="{{ old('payload.endereco_numero', data_get($cliente->payload, 'endereco_numero')) }}">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Complemento</label>
                    <input type="text"
                           name="payload[complemento]"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-4 focus:ring-orange-100 transition"
                           value="{{ old('payload.complemento', data_get($cliente->payload, 'complemento')) }}">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Bairro</label>
                    <input type="text"
                           name="payload[bairro]"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-4 focus:ring-orange-100 transition"
                           value="{{ old('payload.bairro', data_get($cliente->payload, 'bairro')) }}">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Cidade</label>
                    <input type="text"
                           name="payload[cidade]"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-4 focus:ring-orange-100 transition"
                           value="{{ old('payload.cidade', data_get($cliente->payload, 'cidade')) }}">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">UF</label>
                    <input type="text"
                           name="payload[estado]"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 uppercase focus:border-accent focus:ring-4 focus:ring-orange-100 transition"
                           maxlength="2"
                           value="{{ old('payload.estado', data_get($cliente->payload, 'estado')) }}">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">CEP</label>
                    <input type="text"
                           name="payload[cep]"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-4 focus:ring-orange-100 transition"
                           value="{{ old('payload.cep', data_get($cliente->payload, 'cep')) }}">
                </div>
            </div>
        </div>

        <!-- DADOS BANCÁRIOS -->
        <div class="glass-card p-8 mb-12 animate-fade-in" style="animation-delay: 0.3s;">
            <h2 class="section-title">Dados Bancários</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-8">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Banco (código)</label>
                    <input type="text"
                           name="payload[dadosBancarios][codigo_banco]"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-4 focus:ring-orange-100 transition"
                           value="{{ old('payload.dadosBancarios.codigo_banco', data_get($cliente->payload, 'dadosBancarios.codigo_banco')) }}">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Agência</label>
                    <input type="text"
                           name="payload[dadosBancarios][agencia]"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-4 focus:ring-orange-100 transition"
                           value="{{ old('payload.dadosBancarios.agencia', data_get($cliente->payload, 'dadosBancarios.agencia')) }}">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Conta Corrente</label>
                    <input type="text"
                           name="payload[dadosBancarios][conta_corrente]"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-4 focus:ring-orange-100 transition"
                           value="{{ old('payload.dadosBancarios.conta_corrente', data_get($cliente->payload, 'dadosBancarios.conta_corrente')) }}">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Titular</label>
                    <input type="text"
                           name="payload[dadosBancarios][nome_titular]"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-4 focus:ring-orange-100 transition"
                           value="{{ old('payload.dadosBancarios.nome_titular', data_get($cliente->payload, 'dadosBancarios.nome_titular')) }}">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Chave PIX</label>
                    <input type="text"
                           name="payload[dadosBancarios][cChavePix]"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-4 focus:ring-orange-100 transition"
                           value="{{ old('payload.dadosBancarios.cChavePix', data_get($cliente->payload, 'dadosBancarios.cChavePix')) }}">
                </div>
            </div>
        </div>

        <!-- Botões finais -->
        <div class="flex justify-end gap-4 animate-fade-in" style="animation-delay: 0.4s;">
            <a href="{{ route('omie.clientes.show', ['empresa' => $empresa, 'cliente' => $cliente]) }}"
               class="filter-button bg-gray-600 hover:bg-gray-700">
                Cancelar
            </a>
            <button type="submit" class="filter-button">
                Salvar Alterações
            </button>
        </div>
    </form>
</div>
@endsection
@endpush