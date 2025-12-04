@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">{{ isset($productPrice) ? 'Editar' : 'Adicionar' }} Preço</h1>

    <form action="{{ isset($productPrice) ? route('product_prices.update', $productPrice) : route('product_prices.store') }}" method="POST">
        @csrf
        @if(isset($productPrice))
            @method('PUT')
        @endif

        <div class="mb-4">
            <label class="block mb-1">Produto</label>
            <select name="product_id" class="border p-2 w-full">
                @foreach($products as $product)
                    <option value="{{ $product->id }}" {{ isset($productPrice) && $productPrice->product_id == $product->id ? 'selected' : '' }}>
                        {{ $product->nome }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label class="block mb-1">Mês</label>
            <input type="number" name="month" min="1" max="12" value="{{ $productPrice->month ?? old('month') }}" class="border p-2 w-full">
        </div>

        <div class="mb-4">
            <label class="block mb-1">Ano</label>
            <input type="number" name="year" value="{{ $productPrice->year ?? old('year') }}" class="border p-2 w-full">
        </div>

        <div class="mb-4">
            <label class="block mb-1">Valor</label>
            <input type="number" step="0.01" name="value" value="{{ $productPrice->value ?? old('value') }}" class="border p-2 w-full">
        </div>

        <button type="submit" class="btn btn-primary">{{ isset($productPrice) ? 'Atualizar' : 'Salvar' }}</button>
    </form>
</div>
@endsection
