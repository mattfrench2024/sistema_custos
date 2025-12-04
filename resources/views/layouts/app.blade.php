<!DOCTYPE html>
<html lang="pt-br" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ config('app.name') }} — Dashboard</title>

    {{-- Fonts modernas --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"
        rel="stylesheet"
    >

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200 font-inter antialiased">

    {{-- Background global suave --}}
    <div class="min-h-screen flex flex-col">

        {{-- Top Navigation --}}
        @include('partials.nav')

        {{-- Main Content --}}
        <main class="flex-1 p-6 md:p-10 max-w-7xl mx-auto w-full">
            @yield('content')
        </main>

        {{-- Footer minimalista --}}
        <footer class="py-6 text-center text-sm text-gray-500 dark:text-gray-400">
            Sistema de Custos • {{ date('Y') }}
        </footer>

    </div>

</body>
</html>
