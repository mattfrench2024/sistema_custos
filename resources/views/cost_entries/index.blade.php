@extends('layouts.app')

@section('title', 'Área de Custos | RH')

@section('content')

<div class="mb-8 flex justify-between items-center">
    <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">
        Área de Custos
    </h1>

    <x-app-button href="{{ route('cost_entries.create') }}">
        Novo Lançamento
    </x-app-button>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

    @forelse($entries as $entry)
        <x-app-card>

            <div class="flex justify-between items-start">
                <div class="space-y-1">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                        {{ $entry->categoria }}
                    </h2>

                    <p class="text-sm text-gray-500">
                        {{ $entry->descricao ?? 'Sem descrição' }}
                    </p>
                </div>

                <span class="text-lg font-bold text-brand-dark">
                    R$ {{ number_format($entry->valor,2,',','.') }}
                </span>
            </div>

            <div class="mt-6 flex justify-end">
                <x-app-button 
                    href="{{ route('cost_entries.edit', $entry) }}"
                    class="!bg-gray-600 !bg-none !hover:bg-gray-700"
                >
                    Editar
                </x-app-button>
            </div>

        </x-app-card>

    @empty

        <div class="col-span-full">
            <x-app-card>
                <div class="text-center text-gray-500 py-6">
                    Nenhum custo lançado ainda.
                </div>
            </x-app-card>
        </div>

    @endforelse

</div>

@endsection
