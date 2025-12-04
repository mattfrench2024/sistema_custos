<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Custos</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 text-gray-800">

    <div class="min-h-screen flex">

        {{-- Sidebar --}}
        <aside class="w-64 bg-white shadow-lg border-r">
            <div class="p-6 border-b">
                <h1 class="text-xl font-bold text-blue-600">Sistema de Custos</h1>
            </div>

            <nav class="p-5">

                {{-- Dashboard principal --}}
                <a href="{{ route('dashboard') }}" 
                   class="block px-4 py-2 rounded hover:bg-blue-100 mb-2">
                    Dashboard
                </a>

                {{-- Menu por role --}}
                @php
                    $role = auth()->user()->role ?? 'default';
                @endphp

                @if ($role === 'admin')
                    <a href="{{ route('dashboard.admin') }}" class="block px-4 py-2 rounded hover:bg-blue-100 mb-2">
                        Painel Administrativo
                    </a>
                    <a href="{{ route('products.index') }}" class="block px-4 py-2 rounded hover:bg-blue-100 mb-2">
                        Produtos
                    </a>
                    <a href="{{ route('users.index') }}" class="block px-4 py-2 rounded hover:bg-blue-100 mb-2">
                        Usuários
                    </a>
                @endif

                @if ($role === 'financeiro')
                    <a href="{{ route('dashboard.financeiro') }}" class="block px-4 py-2 rounded hover:bg-blue-100 mb-2">
                        Financeiro
                    </a>
                    <a href="{{ route('invoices.index') }}" class="block px-4 py-2 rounded hover:bg-blue-100 mb-2">
                        Notas Fiscais
                    </a>
                @endif

                @if ($role === 'rh')
                    <a href="{{ route('dashboard.rh') }}" class="block px-4 py-2 rounded hover:bg-blue-100 mb-2">
                        Recursos Humanos
                    </a>
                    <a href="{{ route('payrolls.index') }}" class="block px-4 py-2 rounded hover:bg-blue-100 mb-2">
                        Folha de Pagamento
                    </a>
                @endif

                @if ($role === 'auditoria')
                    <a href="{{ route('dashboard.auditoria') }}" class="block px-4 py-2 rounded hover:bg-blue-100 mb-2">
                        Auditoria
                    </a>
                    <a href="{{ route('audit_logs.index') }}" class="block px-4 py-2 rounded hover:bg-blue-100 mb-2">
                        Logs do Sistema
                    </a>
                @endif

            </nav>
        </aside>

        {{-- Conteúdo --}}
        <main class="flex-1 p-8">
            @yield('content')
        </main>

    </div>

</body>
</html>
