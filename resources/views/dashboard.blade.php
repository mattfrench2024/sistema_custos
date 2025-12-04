@extends('layouts.app')

@section('content')
<h2 class="mb-3">Dashboard Geral</h2>

<div class="alert alert-primary">
    Bem-vindo, {{ auth()->user()->name }}!
</div>
@endsection
