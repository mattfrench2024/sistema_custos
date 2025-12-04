<x-app-layout>
    <div class="p-6">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">Itens de Categoria</h1>

            <a href="{{ route('category-items.create') }}"
               class="px-4 py-2 bg-blue-600 text-white rounded-md shadow">
                Novo Item
            </a>
        </div>

        <table class="w-full bg-white shadow rounded">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 text-left">Nome</th>
                    <th class="p-3 text-left">Tipo</th>
                    <th class="p-3 text-right">Ações</th>
                </tr>
            </thead>

            <tbody>
                @foreach($items as $item)
                    <tr class="border-t">
                        <td class="p-3">{{ $item->nome }}</td>
                        <td class="p-3">{{ $item->tipo }}</td>

                        <td class="p-3 text-right">
                            <a href="{{ route('category-items.edit', $item) }}"
                               class="text-blue-600 font-semibold">Editar</a>

                            <form action="{{ route('category-items.destroy', $item) }}"
                                  method="POST"
                                  class="inline-block ml-3"
                                  onsubmit="return confirm('Excluir item?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 font-semibold">
                                    Excluir
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</x-app-layout>
