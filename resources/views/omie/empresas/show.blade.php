@extends('layouts.app')

@section('title', "Empresa - {$empresaModel->razao_social}")

@section('content')
<style>
:root{
    --brand-from: #f97316;
    --brand-to:   #fbbf24;
    --brand-primary: #f97316;
    --soft-white: rgba(255,255,255,0.96);
    --soft-black: rgba(17,24,39,0.92);
    --glass-border: rgba(255,255,255,0.12);
    --text-primary: #111827;
    --muted: #6b7280;
    --radius-lg: 1rem;
    --radius-md: 0.75rem;
    --shadow-soft:
        0 1px 2px rgba(0,0,0,.04),
        0 12px 32px rgba(0,0,0,.08);
}

/* Body e Container */
body {
    background-color: #f9fafb;
    font-family: 'Inter', sans-serif;
    color: var(--text-primary);
}

.container {
    max-width: 1100px;
    margin: 0 auto;
    padding: 2rem;
}

/* Breadcrumbs */
.breadcrumb {
    display: flex;
    font-size: 0.875rem;
    margin-bottom: 1.5rem;
    color: var(--muted);
}

.breadcrumb a {
    color: var(--brand-primary);
    text-decoration: none;
}

.breadcrumb span {
    margin: 0 0.25rem;
}

/* Títulos */
h1 {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    color: var(--brand-primary);
}

h2 {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 1rem;
}

/* Cards gerais */
.card {
    background: var(--soft-white);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-soft);
    padding: 1.5rem;
    transition: all 0.3s ease;
    margin-bottom: 1.5rem;
}

.card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 20px rgba(0,0,0,.1);
}

/* Grid de detalhes */
.grid-details {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    margin-bottom: 2rem;
}

.grid-details div {
    background-color: var(--soft-white);
    padding: 0.75rem 1rem;
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-soft);
    font-size: 0.95rem;
    transition: all 0.3s;
}

.grid-details div:hover {
    background: linear-gradient(90deg, var(--brand-from), var(--brand-to));
    color: #fff;
}

/* Listas */
ul {
    list-style-type: disc;
    margin-left: 1.5rem;
    padding-left: 0.5rem;
}

ul li {
    padding: 0.4rem 0;
    transition: all 0.3s;
}

ul li:hover {
    color: var(--brand-primary);
    font-weight: 600;
}

/* Botão voltar */
.back-btn {
    display: inline-block;
    margin-top: 1rem;
    padding: 0.5rem 1rem;
    border-radius: var(--radius-md);
    background: var(--brand-primary);
    color: white;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
}

.back-btn:hover {
    background: linear-gradient(90deg, var(--brand-from), var(--brand-to));
    box-shadow: 0 4px 15px rgba(0,0,0,.15);
}

/* Badges */
.badge {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    border-radius: var(--radius-md);
    font-size: 0.75rem;
    font-weight: 600;
    background-color: var(--brand-primary);
    color: #fff;
    margin-right: 0.25rem;
}

/* Responsividade */
@media (max-width: 768px) {
    .grid-details {
        grid-template-columns: 1fr;
    }

    h1 {
        font-size: 1.5rem;
    }
}

/* Sections */
.section-title {
    font-size: 1.125rem;
    font-weight: 600;
    margin-bottom: 0.75rem;
    border-bottom: 2px solid var(--brand-primary);
    display: inline-block;
    padding-bottom: 0.25rem;
}

/* Cards de lista (serviços, clientes, categorias) */
.list-card {
    background-color: var(--soft-white);
    padding: 1rem;
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-soft);
    margin-bottom: 1rem;
    transition: all 0.3s ease;
}

.list-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,.1);
}

.list-card li {
    font-size: 0.95rem;
    padding: 0.35rem 0;
}

.list-card li:hover {
    color: var(--brand-primary);
}
</style>

<div class="container">

    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="{{ route('omie.empresas.index', $empresa) }}">Empresas</a>
        <span>/</span>
        <span>{{ $empresaModel->razao_social }}</span>
    </div>

    <!-- Título -->
    <h1>{{ $empresaModel->razao_social }} <span class="badge">{{ $empresaModel->nome_fantasia }}</span></h1>

    <!-- Grid de detalhes -->
    <div class="grid-details">
        <div><strong>Código Empresa:</strong> {{ $empresaModel->codigo_empresa }}</div>
        <div><strong>CNPJ:</strong> {{ $empresaModel->cnpj }}</div>
        <div><strong>Endereço:</strong> {{ $empresaModel->logradouro }} {{ $empresaModel->endereco_numero }} {{ $empresaModel->complemento }}</div>
        <div><strong>Bairro/Cidade:</strong> {{ $empresaModel->bairro }} / {{ $empresaModel->cidade }} - {{ $empresaModel->estado }}</div>
        <div><strong>CEP:</strong> {{ $empresaModel->cep }}</div>
        <div><strong>Email:</strong> {{ $empresaModel->email }}</div>
        <div><strong>Telefone 1:</strong> {{ $empresaModel->telefone1 }}</div>
        <div><strong>Telefone 2:</strong> {{ $empresaModel->telefone2 }}</div>
    </div>

    <!-- Serviços -->
    <h2 class="section-title">Serviços</h2>
    <ul class="list-card">
        @forelse($empresaModel->servicos as $servico)
            <li>{{ $servico->descricao }}</li>
        @empty
            <li>Não há serviços cadastrados.</li>
        @endforelse
    </ul>

   

    <a href="{{ route('omie.empresas.index', $empresa) }}" class="back-btn">Voltar para lista</a>

</div>
