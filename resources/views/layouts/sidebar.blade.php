<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4">
    <a class="navbar-brand" href="/dashboard">Sistema de Custos</a>

    <div class="collapse navbar-collapse">
        <ul class="navbar-nav me-auto">

            <li class="nav-item">
                <a class="nav-link" href="{{ route('categories.index') }}">Categorias</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('expenses.index') }}">Despesas</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('products.index') }}">Produtos</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('departments.index') }}">Departamentos</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('invoices.index') }}">NF / Faturas</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('payrolls.index') }}">Folha</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('settings.index') }}">Configurações</a>
            </li>
        </ul>

        <ul class="navbar-nav">
            <li class="nav-item">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-danger btn-sm">Sair</button>
                </form>
            </li>
        </ul>
    </div>
</nav>
