<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Sistema de Custos</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- FONT AWESOME --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />

    <style>
        :root {
            --brand-from: #ff7a00;
            --brand-to: #ff9a00;
            --brand-soft: rgba(255, 122, 0, 0.1);
            --sidebar-width: 270px;

            /* Gray scale */
            --gray-1: #fcfcfc;
            --gray-2: #f9f9f9;
            --gray-3: #f0f0f0;
            --gray-4: #e8e8e8;

            /* Dark mode */
            --dark-1: #111113;
            --dark-2: #19191b;
            --dark-3: #1f1f22;
            --dark-4: #26262a;

            --transition-fast: 0.2s ease-in-out;
        }

        /* Sidebar */
        aside {
            backdrop-filter: blur(15px);
        }

        /* Menu sections */
        .menu-section {
            @apply text-[10px] uppercase font-semibold tracking-wider text-gray-500 dark:text-gray-400 select-none;
        }

        /* Nav item hover */
        nav a {
            @apply flex items-center gap-3 p-3 rounded-lg font-medium text-gray-700 dark:text-gray-200 hover:bg-[var(--brand-soft)] hover:text-[var(--brand-to)] transition-colors duration-200;
        }

        /* Active link */
        nav a.active {
            @apply bg-gradient-to-r from-[var(--brand-from)] to-[var(--brand-to)] text-white shadow-lg;
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-thumb {
            background-color: rgba(0,0,0,0.2);
            border-radius: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
    </style>
</head>

<body class="bg-gray-100 dark:bg-dark-1 text-gray-800 dark:text-gray-200 transition-colors duration-300">

<div class="min-h-screen flex relative">

    {{-- ===================== SIDEBAR ===================== --}}
    <aside 
        x-data="{ open: true }"
        class="w-[var(--sidebar-width)] h-screen fixed left-0 top-0 z-40
               bg-white/70 dark:bg-dark-2 border-r border-gray-200/40 dark:border-white/10
               shadow-lg shadow-black/5 overflow-y-auto animate-in fade-in slide-in-from-left-4
               transition-all duration-300"
    >
        {{-- LOGO --}}
        <div class="p-6 border-b border-gray-200/40 dark:border-white/10">
            <h1 class="text-2xl font-extrabold tracking-tight bg-gradient-to-r
                       from-[var(--brand-from)] to-[var(--brand-to)]
                       text-transparent bg-clip-text drop-shadow-sm">
                Financeiro
            </h1>
        </div>

        {{-- NAVIGATION --}}
        <nav class="p-4 space-y-8 h-[calc(100vh-100px)] overflow-y-auto text-sm
                    selection:bg-[var(--brand-to)] selection:text-white
                    scrollbar-thin scrollbar-track-transparent scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-700">
            
            {{-- FINANCEIRO --}}
            <div class="space-y-2">
                <p class="menu-section">Financeiro</p>
                <x-nav.item label="Contas a Pagar" route="financeiro.pagar.index" icon="fa-solid fa-arrow-up" />
                <x-nav.item label="Contas a Receber" route="financeiro.receber.index" icon="fa-solid fa-arrow-down" />
                <x-nav.item label="Novo Lançamento" route="financeiro.cost_entries.create" icon="fa-solid fa-circle-plus" />
                <x-nav.item label="Notas Fiscais" route="invoices.index" icon="fa-solid fa-file-invoice" />
                <x-nav.item label="Financeiro Analítico" route="financeiro.analitico.dashboard" icon="fa-solid fa-chart-line" />
            </div>

            {{-- USUÁRIO --}}
            <div class="space-y-2">
                <p class="menu-section">Usuário</p>
                <x-nav.item label="Notificações" route="notifications.index" icon="fa-solid fa-bell" />
                <x-nav.item label="Perfil" route="profile.edit" icon="fa-solid fa-user" />
            </div>
        </nav>
    </aside>

    {{-- ===================== MAIN CONTENT ===================== --}}
    <main class="flex-1 ml-[var(--sidebar-width)] p-10 animate-in fade-in slide-in-from-bottom-2 transition-all duration-300">
        @yield('content')
    </main>

</div>

{{-- ICONES CUSTOM --}}
@vite('resources/js/lucide-icons.js')

</body>
</html>
