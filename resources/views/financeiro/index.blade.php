@extends('layouts.app')

@section('content')

<h1 class="text-3xl font-bold mb-8">Custos Operacionais</h1>

<div class="rounded-2xl shadow bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-6">

<table class="min-w-full text-sm">
    <thead>
        <tr class="border-b dark:border-gray-700 text-gray-500 uppercase text-xs">
            <th class="py-3 text-left">Categoria</th>
            <th class="text-right">Ago</th>
            <th class="text-right">Set</th>
            <th class="text-right">Out</th>
            <th class="text-right">Nov</th>
            <th class="text-right">Dez</th>
            <th></th>
        </tr>
    </thead>

    <tbody class="divide-y dark:divide-gray-800">
        @foreach($costs as $cost)
        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
            <td class="py-3">{{ $cost->Categoria }}</td>
            <td class="text-right">{{ number_format($cost->{'Pago ago'},2,',','.') }}</td>
            <td class="text-right">{{ number_format($cost->{'Pago set'},2,',','.') }}</td>
            <td class="text-right">{{ number_format($cost->{'Pago out'},2,',','.') }}</td>
            <td class="text-right">{{ number_format($cost->{'Pago nov'},2,',','.') }}</td>
            <td class="text-right">{{ number_format($cost->{'Pago dez'},2,',','.') }}</td>
            <td class="text-right">
                <a href="{{ route('financeiro.costs.edit', $cost->id) }}"
                   class="text-blue-600 hover:text-blue-800 font-medium">
                    Editar →
                </a>
            </td>
        </tr>
        @endforeach
    </tbody>

</table>

</div>

@endsection
