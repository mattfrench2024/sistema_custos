@extends('layouts.app')

@section('content')
<h2 class="mb-3">Despesas</h2>

<a href="{{ route('expenses.create') }}" class="btn btn-primary mb-3">Nova Despesa</a>

<table class="table table-bordered table-hover">
    <thead>
        <tr>
            <th>ID</th>
            <th>Descrição</th>
            <th>Valor</th>
            <th>Data</th>
            <th>Usuário</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        @foreach($expenses as $e)
        <tr>
            <td>{{ $e->id }}</td>
            <td>{{ $e->descricao }}</td>
            <td>R$ {{ number_format($e->valor, 2, ',', '.') }}</td>
            <td>{{ $e->data->format('d/m/Y') }}</td>
            <td>{{ $e->user->name }}</td>
            <td>
                <a href="{{ route('expenses.show', $e) }}" class="btn btn-sm btn-info">Ver</a>
                <a href="{{ route('expenses.edit', $e) }}" class="btn btn-sm btn-warning">Editar</a>
                <form action="{{ route('expenses.destroy', $e) }}" method="POST" style="display:inline;">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger" onclick="return confirm('Confirmar remoção?')">Excluir</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{ $expenses->links() }}

@endsection
