@extends('layouts.app')

@section('title', 'Novo Lançamento | RH')

@section('content')

<div class="mb-8 flex justify-between items-center">
    <h1 class="text-3xl font-bold">Novo Lançamento</h1>
</div>

<x-app-card class="max-w-2xl mx-auto">

    <form method="POST" action="{{ route('cost_entries.store') }}" class="space-y-6">
        @csrf

        @include('cost_entries._form')

        <div class="pt-6 flex justify-end space-x-3">
            <x-app-button href="{{ route('cost_entries.index') }}" 
                class="!bg-gray-600 !bg-none !hover:bg-gray-700">
                Cancelar
            </x-app-button>

            <x-app-button type="submit">
                Salvar
            </x-app-button>
        </div>
    </form>

</x-app-card>

@endsection
