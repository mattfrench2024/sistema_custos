<nav class="navbar">
    <div>
        <strong>{{ $authUser->name }}</strong> 
        <small>({{ strtoupper($userRole) }})</small>
    </div>

    <div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Sair</button>
        </form>
    </div>
</nav>
