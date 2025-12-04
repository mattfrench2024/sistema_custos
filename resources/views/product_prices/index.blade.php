@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Preços dos Produtos</h1>

    <a href="{{ route('product_prices.create') }}" class="btn btn-primary mb-4">Adicionar Preço</a>

    <table class="table-auto w-full border">
        <thead>
            <tr class="bg-gray-100">
                <th class="px-4 py-2">Produto</th>
                <th class="px-4 py-2">Mês</th>
                <th class="px-4 py-2">Ano</th>
                <th class="px-4 py-2">Valor</th>
                <th class="px-4 py-2">Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($prices as $price)
            <tr>
                <td class="border px-4 py-2">{{ $price->product->nome }}</td>
                <td class="border px-4 py-2">{{ $price->month }}</td>
                <td class="border px-4 py-2">{{ $price->year }}</td>
                <td class="border px-4 py-2">R$ {{ number_format($price->value, 2, ',', '.') }}</td>
                <td class="border px-4 py-2">
                    <a href="{{ route('product_prices.edit', $price) }}" class="text-blue-500">Editar</a> |
                    <form action="{{ route('product_prices.destroy', $price) }}" method="POST" class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500" onclick="return confirm('Deseja deletar?')">Deletar</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
