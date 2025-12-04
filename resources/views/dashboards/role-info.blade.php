@extends('layouts.app')

@section('content')

<div class="bg-white p-6 rounded shadow-md">
    <h2 class="text-2xl font-bold mb-4">Informações da Sessão</h2>

    <div class="p-4 bg-blue-50 border-l-4 border-blue-500 rounded">
        <p class="text-gray-700 text-lg">
            <strong>Usuário logado:</strong> {{ auth()->user()->name }}
        </p>

        <p class="text-gray-700 text-lg mt-2">
            <strong>Papel (Role):</strong> 
            <span class="text-blue-700 font-bold uppercase">
                {{ auth()->user()->role }}
            </span>
        </p>
    </div>
</div>

@endsection
