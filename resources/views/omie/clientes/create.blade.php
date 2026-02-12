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
<div class="container mx-auto px-4 py-12 max-w-6xl" x-data="clienteForm()">
    <form action="{{ route('omie.clientes.store', $empresa) }}" method="POST">
        @csrf

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-8 mb-12 animate-fade-in">
            <div>
                <h1 class="header-title">
                    Criar Novo Cliente
                </h1>
                <p class="header-subtitle">
                    {{ $empresaLabel }}
                </p>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('omie.clientes.index', $empresa) }}"
                   class="filter-button bg-gray-600 hover:bg-gray-700">
                    Cancelar
                </a>
                <button type="submit" class="filter-button">
                    Salvar Cliente
                </button>
            </div>
        </div>

        @if (session('success'))
            <div class="glass-card p-6 mb-8 border-l-4 border-green-500 bg-green-50 animate-fade-in">
                <p class="text-green-800 font-medium">{{ session('success') }}</p>
            </div>
        @endif



        @if ($errors->any())
            <div class="glass-card p-6 mb-8 border-l-4 border-red-500 bg-red-50 animate-fade-in">
                <p class="text-red-800 font-medium">Por favor, corrija os erros abaixo:</p>
                <ul class="mt-3 list-disc list-inside text-red-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
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
                           value="{{ old('payload.razao_social') }}"
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
                           value="{{ old('payload.nome_fantasia') }}">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        CNPJ / CPF <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="payload[cnpj_cpf]"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 font-mono focus:border-accent focus:ring-4 focus:ring-orange-100 transition"
                           value="{{ old('payload.cnpj_cpf') }}"
                           required>
                    @error('payload.cnpj_cpf')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">E-mail</label>
                    <input type="email"
                           name="payload[email]"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-orange-100 transition"
                           value="{{ old('payload.email') }}">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Telefone</label>
                    <input type="text"
                           name="payload[telefone]"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-4 focus:ring-orange-100 transition"
                           value="{{ old('payload.telefone') }}">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Pessoa Física <span class="text-red-500">*</span>
                    </label>
                    <select name="payload[pessoa_fisica]"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-4 focus:ring-orange-100 transition"
                            required>
                        <option value="N" {{ old('payload.pessoa_fisica', 'N') === 'N' ? 'selected' : '' }}>
                            Não (Pessoa Jurídica)
                        </option>
                        <option value="S" {{ old('payload.pessoa_fisica') === 'S' ? 'selected' : '' }}>
                            Sim (Pessoa Física)
                        </option>
                    </select>
                </div>

                <!-- Tags com multi-select + chips visuais -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Tags
                    </label>
                    <div class="relative">
                        <select multiple
                                x-ref="tagSelect"
                                @change="updateSelectedTags()"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-4 focus:ring-orange-100 transition opacity-0 absolute">
                            @foreach ($tagsUnicas as $tag)
                                <option value="{{ $tag }}">{{ $tag }}</option>
                            @endforeach
                        </select>

                        <div class="min-h-[3rem] p-3 border border-gray-300 rounded-lg flex flex-wrap gap-2 items-center bg-white">
                            <template x-for="tag in selectedTags" :key="tag">
                                <span class="inline-flex items-center gap-1 px-3 py-1 bg-orange-100 text-orange-800 text-sm font-medium rounded-full">
                                    <span x-text="tag"></span>
                                    <button type="button" @click="removeTag(tag)" class="text-orange-600 hover:text-orange-800">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </span>
                            </template>
                            <input type="text" x-ref="tagInput" @keydown.enter.prevent="addTagFromInput()" @blur="addTagFromInput()"
                                   placeholder="{{ count($tagsUnicas) ? 'Digite para adicionar nova tag...' : 'Nenhuma tag cadastrada ainda' }}"
                                   class="flex-1 outline-none min-w-[200px]">
                        </div>
                    </div>
                    <!-- Hidden input para enviar array -->
                    <input type="hidden" name="selected_tags" :value="JSON.stringify(selectedTags)">
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
                           value="{{ old('payload.endereco') }}">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Número</label>
                    <input type="text"
                           name="payload[endereco_numero]"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-4 focus:ring-orange-100 transition"
                           value="{{ old('payload.endereco_numero') }}">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Complemento</label>
                    <input type="text"
                           name="payload[complemento]"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-4 focus:ring-orange-100 transition"
                           value="{{ old('payload.complemento') }}">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Bairro</label>
                    <input type="text"
                           name="payload[bairro]"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-4 focus:ring-orange-100 transition"
                           value="{{ old('payload.bairro') }}">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Cidade</label>
                    <input type="text"
                           name="payload[cidade]"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-4 focus:ring-orange-100 transition"
                           value="{{ old('payload.cidade') }}">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">UF</label>
                    <input type="text"
                           name="payload[estado]"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 uppercase focus:border-accent focus:ring-4 focus:ring-orange-100 transition"
                           maxlength="2"
                           value="{{ old('payload.estado') }}">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">CEP</label>
                    <input type="text"
                           name="payload[cep]"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-4 focus:ring-orange-100 transition"
                           value="{{ old('payload.cep') }}">
                </div>
            </div>
        </div>

        <!-- DADOS BANCÁRIOS -->
        <div class="glass-card p-8 mb-12 animate-fade-in" style="animation-delay: 0.3s;">
            <h2 class="section-title">Dados Bancários</h2>

            <!-- Select de Conta Corrente (pré-cadastrada) -->
            <div class="mb-8">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Vincular Conta Corrente Existente (opcional)
                </label>
                <select @change="populateBankData($event.target.value)"
                        class="w-full md:w-96 px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-4 focus:ring-orange-100 transition">
                    <option value="">-- Nenhuma / Preencher manualmente --</option>
                    @foreach ($contasCorrentes as $conta)
                        <option value="{{ $conta->id }}"
                                data-banco="{{ $conta->codigo_banco }}"
                                data-agencia="{{ $conta->codigo_agencia }}"
                                data-conta="{{ $conta->numero_conta_corrente }}"
                                data-titular="{{ $conta->descricao ?? '' }}"
                                data-pix="">
                            {{ $conta->codigo_banco }} - {{ $conta->descricao ?? 'Conta sem descrição' }} ({{ $conta->numero_conta_corrente }})
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-2">
                    Selecionar uma conta pré-cadastrada preenche automaticamente os campos abaixo.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Banco (código)</label>
                    <input type="text"
                           name="payload[dadosBancarios][codigo_banco]"
                           x-ref="banco"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-4 focus:ring-orange-100 transition"
                           value="{{ old('payload.dadosBancarios.codigo_banco') }}">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Agência</label>
                    <input type="text"
                           name="payload[dadosBancarios][agencia]"
                           x-ref="agencia"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-4 focus:ring-orange-100 transition"
                           value="{{ old('payload.dadosBancarios.agencia') }}">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Conta Corrente</label>
                    <input type="text"
                           name="payload[dadosBancarios][conta_corrente]"
                           x-ref="conta"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-4 focus:ring-orange-100 transition"
                           value="{{ old('payload.dadosBancarios.conta_corrente') }}">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Titular</label>
                    <input type="text"
                           name="payload[dadosBancarios][nome_titular]"
                           x-ref="titular"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-4 focus:ring-orange-100 transition"
                           value="{{ old('payload.dadosBancarios.nome_titular') }}">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Chave PIX</label>
                    <input type="text"
                           name="payload[dadosBancarios][cChavePix]"
                           x-ref="pix"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-4 focus:ring-orange-100 transition"
                           value="{{ old('payload.dadosBancarios.cChavePix') }}">
                </div>
            </div>
        </div>

        <!-- Botões finais -->
        <div class="flex justify-end gap-4 animate-fade-in" style="animation-delay: 0.4s;">
            <a href="{{ route('omie.clientes.index', $empresa) }}"
               class="filter-button bg-gray-600 hover:bg-gray-700">
                Cancelar
            </a>
            <button type="submit" class="filter-button">
                Salvar Cliente
            </button>
        </div>
    </form>
</div>

<script>
function clienteForm() {
    return {
        selectedTags: [],

        updateSelectedTags() {
            this.selectedTags = Array.from(this.$refs.tagSelect.selectedOptions).map(opt => opt.value);
        },

        addTagFromInput() {
            const input = this.$refs.tagInput;
            const value = input.value.trim();
            if (value && !this.selectedTags.includes(value)) {
                this.selectedTags.push(value);
            }
            input.value = '';
        },

        removeTag(tag) {
            this.selectedTags = this.selectedTags.filter(t => t !== tag);
        },

        populateBankData(contaId) {
            if (!contaId) {
                this.$refs.banco.value = '';
                this.$refs.agencia.value = '';
                this.$refs.conta.value = '';
                this.$refs.titular.value = '';
                this.$refs.pix.value = '';
                return;
            }

            const option = this.$refs.tagSelect.querySelector(`option[value="${contaId}"]`); // erro aqui, corrigir para o select de contas
            // Corrija: use um x-ref no select de contas
            // Para simplificar, use document.querySelector
            const selectedOption = document.querySelector(`select option[value="${contaId}"]`);
            if (selectedOption) {
                this.$refs.banco.value = selectedOption.dataset.banco || '';
                this.$refs.agencia.value = selectedOption.dataset.agencia || '';
                this.$refs.conta.value = selectedOption.dataset.conta || '';
                this.$refs.titular.value = selectedOption.dataset.titular || '';
                this.$refs.pix.value = selectedOption.dataset.pix || '';
            }
        }
    }
}
</script>
@endsection
@endpush