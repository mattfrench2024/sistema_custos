@extends('layouts.app')

@section('content')

<style>
    :root {
        --brand-from: #F9821A;
        --brand-to: #FC940D;
        --glass-bg: rgba(255,255,255,0.65);
    }
</style>

<div class="max-w-7xl mx-auto px-4 md:px-8 py-10">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-10">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">
                Dashboard RH
            </h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm">
                Painel de indicadores e ferramentas administrativas
            </p>
        </div>
    </div>

    {{-- GRID DE CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- CARD 1: Colaboradores --}}
        <a href="#"
           class="group p-6 rounded-2xl bg-[var(--glass-bg)] backdrop-blur shadow hover:shadow-xl
                  transition-all hover:scale-[1.02] border border-white/60 dark:border-white/10">

            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                    Colaboradores
                </h3>

                <div class="w-10 h-10 rounded-xl bg-orange-100 flex items-center justify-center
                            group-hover:scale-110 transition-transform">
                    <span class="text-xl">👥</span>
                </div>
            </div>

            <p class="mt-3 text-gray-500 dark:text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-300">
                Gerencie usuários, cargos e permissões internas.
            </p>

            <div class="mt-6">
                <span class="inline-block px-4 py-2 rounded-xl text-sm font-semibold 
                             bg-gradient-to-r from-[var(--brand-from)] to-[var(--brand-to)]
                             text-white shadow-sm group-hover:shadow-lg group-hover:scale-[1.04] 
                             transition-all">
                    Acessar
                </span>
            </div>
        </a>


        {{-- CARD 2: Benefícios --}}
        <a href="#"
           class="group p-6 rounded-2xl bg-[var(--glass-bg)] backdrop-blur shadow hover:shadow-xl
                  transition-all hover:scale-[1.02] border border-white/60 dark:border-white/10">

            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                    Benefícios
                </h3>

                <div class="w-10 h-10 rounded-xl bg-orange-100 flex items-center justify-center
                            group-hover:scale-110 transition-transform">
                    <span class="text-xl">🎁</span>
                </div>
            </div>

            <p class="mt-3 text-gray-500 dark:text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-300">
                Controle de benefícios, planos e reembolsos.
            </p>

            <div class="mt-6">
                <span class="inline-block px-4 py-2 rounded-xl text-sm font-semibold 
                             bg-gradient-to-r from-[var(--brand-from)] to-[var(--brand-to)]
                             text-white shadow-sm group-hover:shadow-lg group-hover:scale-[1.04] 
                             transition-all">
                    Acessar
                </span>
            </div>
        </a>


        {{-- CARD 3: Férias e Ponto --}}
        <a href="#"
           class="group p-6 rounded-2xl bg-[var(--glass-bg)] backdrop-blur shadow hover:shadow-xl
                  transition-all hover:scale-[1.02] border border-white/60 dark:border-white/10">

            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                    Férias & Ponto
                </h3>

                <div class="w-10 h-10 rounded-xl bg-orange-100 flex items-center justify-center
                            group-hover:scale-110 transition-transform">
                    <span class="text-xl">📅</span>
                </div>
            </div>

            <p class="mt-3 text-gray-500 dark:text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-300">
                Registros, dias acumulados e ausências.
            </p>

            <div class="mt-6">
                <span class="inline-block px-4 py-2 rounded-xl text-sm font-semibold 
                             bg-gradient-to-r from-[var(--brand-from)] to-[var(--brand-to)]
                             text-white shadow-sm group-hover:shadow-lg group-hover:scale-[1.04] 
                             transition-all">
                    Acessar
                </span>
            </div>
        </a>


        {{-- CARD 4: Área de Custos (NOVO) --}}
        <a href="{{ route('cost_entries.index') }}"
           class="group p-6 rounded-2xl bg-[var(--glass-bg)] backdrop-blur shadow hover:shadow-xl
                  transition-all hover:scale-[1.02] border border-white/60 dark:border-white/10">

            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                    Área de Custos
                </h3>

                <div class="w-10 h-10 rounded-xl bg-orange-100 flex items-center justify-center
                            group-hover:scale-110 transition-transform">
                    <span class="text-xl">💸</span>
                </div>
            </div>

            <p class="mt-3 text-gray-500 dark:text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-300">
                Gerencie os lançamentos por categoria e acompanhe os valores pagos mensalmente.
            </p>

            <div class="mt-6">
                <span class="inline-block px-4 py-2 rounded-xl text-sm font-semibold 
                             bg-gradient-to-r from-[var(--brand-from)] to-[var(--brand-to)]
                             text-white shadow-sm group-hover:shadow-lg group-hover:scale-[1.04] 
                             transition-all">
                    Acessar
                </span>
            </div>
        </a>

    </div>
</div>

@endsection
