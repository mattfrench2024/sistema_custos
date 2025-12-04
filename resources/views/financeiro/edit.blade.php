@extends('layouts.app')

@section('content')

<h1 class="text-3xl font-bold mb-8">Editar Registro – {{ $cost->Categoria }}</h1>

<form action="{{ route('financeiro.costs.update', $cost->id) }}" method="POST"
      class="space-y-6 p-6 bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow">
    @csrf

    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        @foreach($cost->getMonthlyValues() as $month => $value)
        <div>
            <label class="block text-sm mb-1 font-medium capitalize">{{ $month }}</label>
            <input type="number" step="0.01" name="Pago {{ $month }}"
                   value="{{ $value }}"
                   class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
        </div>
        @endforeach
    </div>

    <button class="px-6 py-3 rounded-xl bg-blue-600 text-white font-medium shadow hover:bg-blue-700">
        Salvar alterações
    </button>

</form>

@endsection
