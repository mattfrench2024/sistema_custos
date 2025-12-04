<x-app-layout>
    <div class="p-6 max-w-xl mx-auto">

        <h1 class="text-2xl font-bold mb-4">Novo Item da Categoria</h1>

        <form method="POST" action="{{ route('category-items.store') }}">
            @csrf

            <label class="block mb-2 font-semibold">Nome do Item</label>
            <select name="nome" class="w-full p-2 border rounded mb-4" required>
                <option value="">Selecione...</option>
                @foreach($categorias as $cat)
                    <option value="{{ $cat }}">{{ $cat }}</option>
                @endforeach
            </select>

            <label class="block mb-2 font-semibold">Tipo</label>
            <input type="text" name="tipo"
                   placeholder="software, serviço, etc"
                   class="w-full p-2 border rounded mb-4">

            <button class="px-4 py-2 bg-blue-600 text-white rounded">
                Salvar
            </button>
        </form>
    </div>
</x-app-layout>
