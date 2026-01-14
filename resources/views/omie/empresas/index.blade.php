@extends('layouts.app')

@section('title', "Empresas - {$empresaNome}")

@section('content')
<style>
:root {
    --brand-orange: #ff6200ff;
    --brand-orange-soft: #ffedd5;
    --brand-purple: #f97316;
    --brand-purple-soft: #ede9fe;

    --surface: #ffffff;
    --surface-muted: #f9fafb;

    --text-primary: #111827;
    --text-secondary: #6b7280;

    --radius-lg: 1rem;
    --radius-md: 0.75rem;

    --shadow-soft:
        0 1px 2px rgba(0,0,0,.04),
        0 12px 32px rgba(0,0,0,.08);
}

/* Reset básico */
body {
    background-color: var(--surface-muted);
    color: var(--text-primary);
    font-family: 'Inter', sans-serif;
    line-height: 1.6;
}

/* Container principal */
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

/* Títulos */
h1 {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    color: var(--brand-purple);
}

h2 {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: var(--text-primary);
}

/* Tabela premium */
.table-auto {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    background-color: var(--surface);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-soft);
}

.table-auto thead {
    background-color: var(--brand-orange-soft);
}

.table-auto th,
.table-auto td {
    padding: 0.75rem 1rem;
    text-align: left;
    border-bottom: 1px solid #e5e7eb;
    font-size: 0.95rem;
}

.table-auto th {
    font-weight: 600;
    color: var(--text-primary);
}

.table-auto tbody tr:hover {
    background-color: var(--brand-purple-soft);
    transition: background-color 0.3s ease;
}

.table-auto a {
    color: var(--brand-orange);
    font-weight: 500;
    text-decoration: none;
}

.table-auto a:hover {
    text-decoration: underline;
}

/* Paginação */
.mt-4 {
    margin-top: 1rem;
}

.pagination {
    display: flex;
    justify-content: flex-end;
    padding: 0.5rem 0;
}

.pagination li {
    margin: 0 0.25rem;
}

.pagination li a {
    padding: 0.5rem 0.75rem;
    background-color: var(--surface);
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-soft);
    font-weight: 500;
    color: var(--text-primary);
    transition: all 0.3s;
}

.pagination li a:hover {
    background-color: var(--brand-orange-soft);
    color: var(--brand-orange);
}

/* Cards de resumo */
.card {
    background-color: var(--surface);
    padding: 1.5rem;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-soft);
    margin-bottom: 1rem;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 20px rgba(0,0,0,.1);
}

.card h3 {
    font-size: 1rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.card p {
    font-size: 0.875rem;
    color: var(--text-secondary);
}

/* Breadcrumbs */
.breadcrumb {
    display: flex;
    font-size: 0.875rem;
    margin-bottom: 1.5rem;
}

.breadcrumb a {
    color: var(--brand-orange);
    text-decoration: none;
}

.breadcrumb span {
    margin: 0 0.25rem;
    color: var(--text-secondary);
}

/* Botões */
.btn {
    padding: 0.5rem 1rem;
    border-radius: var(--radius-md);
    font-weight: 600;
    background-color: var(--brand-orange);
    color: var(--surface);
    transition: all 0.3s ease;
}

.btn:hover {
    background-color: var(--brand-purple);
    color: var(--surface);
}

/* Form inputs */
input, select, textarea {
    padding: 0.5rem 0.75rem;
    border-radius: var(--radius-md);
    border: 1px solid #e5e7eb;
    font-size: 0.875rem;
    width: 100%;
    margin-bottom: 0.75rem;
    transition: all 0.3s ease;
}

input:focus, select:focus, textarea:focus {
    outline: none;
    border-color: var(--brand-orange);
    box-shadow: 0 0 0 3px var(--brand-orange-soft);
}

/* Responsividade */
@media (max-width: 768px) {
    .table-auto th, .table-auto td {
        padding: 0.5rem;
    }

    h1 {
        font-size: 1.5rem;
    }
}
</style>

<div class="container mx-auto p-4">

    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="{{ route('omie.empresas.index', $empresa) }}">Home</a>
        <span>/</span>
        <span>Empresas</span>
    </div>

    <h1>Empresas — {{ $empresaNome }}</h1>

    <!-- Cards de resumo -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="card">
            <h3>Total Empresas</h3>
            <p>{{ $empresas->total() }}</p>
        </div>
        <div class="card">
            <h3>Empresa Ativa</h3>
            <p>{{ $empresas->count() }} visíveis</p>
        </div>
        <div class="card">
            <h3>Última Atualização</h3>
            <p>{{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <!-- Tabela de empresas -->
    <table class="table-auto w-full">
        <thead>
            <tr>
                <th>Código</th>
                <th>Razão Social</th>
                <th>Nome Fantasia</th>
                <th>CNPJ</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($empresas as $empresaItem)
            <tr>
                <td>{{ $empresaItem->codigo_empresa }}</td>
                <td>{{ $empresaItem->razao_social }}</td>
                <td>{{ $empresaItem->nome_fantasia }}</td>
                <td>{{ $empresaItem->cnpj }}</td>
                <td>
                    <a href="{{ route('omie.empresas.show', [$empresa, $empresaItem]) }}" class="btn">Ver</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Paginação -->
    <div class="mt-4">
        {{ $empresas->links() }}
    </div>

</div>
