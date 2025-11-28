@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between">
    <h2>Categorias</h2>
    <a href="{{ route('categories.create') }}" class="btn btn-primary">Nova Categoria</a>
</div>

<table class="table table-striped mt-3">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($categories as $cat)
        <tr>
            <td>{{ $cat->id }}</td>
            <td>{{ $cat->name }}</td>
            <td>
                <a class="btn btn-warning btn-sm" href="{{ route('categories.edit', $cat->id) }}">Editar</a>
                <form method="POST" action="{{ route('categories.destroy', $cat->id) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm">Excluir</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
