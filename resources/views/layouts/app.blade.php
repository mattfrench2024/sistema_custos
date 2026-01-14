<!DOCTYPE html>
<html lang="pt-br" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ config('app.name') }} — Dashboard</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"
        rel="stylesheet"
    >

    {{-- Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200 font-inter antialiased">

    {{-- Sidebar / Navbar --}}
    @include('partials.nav')

    {{-- Conteúdo principal --}}
    <div class="md:pl-64 transition-all duration-300 min-h-screen flex flex-col">
        <main class="flex-1 p-6 md:p-10 max-w-7xl mx-auto w-full">
            @yield('content')
        </main>

        {{-- Footer --}}
        <footer class="py-6 text-center text-sm text-gray-500 dark:text-gray-400">
            Sistema de Custos • {{ date('Y') }}
        </footer>
    </div>

    {{-- 🔥 SCRIPTS GLOBAIS (OBRIGATÓRIO PARA @push) --}}
    @stack('scripts')

</body>
</html>
