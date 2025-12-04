<x-app-layout>
    <div class="p-6 max-w-xl mx-auto">

        <h1 class="text-2xl font-bold mb-4">Editar Item</h1>

        <form method="POST" action="{{ route('category-items.update', $category_item) }}">
            @csrf
            @method('PUT')

            <label class="block mb-2 font-semibold">Nome</label>
            <input type="text" name="nome" value="{{ $category_item->nome }}"
                   class="w-full p-2 border rounded mb-4">

            <label class="block mb-2 font-semibold">Tipo</label>
            <input type="text" name="tipo" value="{{ $category_item->tipo }}"
                   class="w-full p-2 border rounded mb-4">

            <button class="px-4 py-2 bg-blue-600 text-white rounded">
                Atualizar
            </button>
        </form>
    </div>
</x-app-layout>
