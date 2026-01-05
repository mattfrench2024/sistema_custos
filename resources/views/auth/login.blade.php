<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar - Verreschi Management</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-gradient-to-br from-brand to-purple-700 flex items-center justify-center font-sans">

    {{-- Container Principal --}}
    <div class="w-full max-w-md p-8 bg-white/20 backdrop-blur-xl rounded-2xl shadow-xl animate-fade-in">

        {{-- Logo / Título --}}
        <h2 class="text-3xl font-bold text-center text-white tracking-tight mb-6 animate-fade-in-up">
            Verreschi Management
        </h2>

        {{-- Mensagens de erro --}}
        @if ($errors->any())
            <div class="bg-red-500/80 text-white text-center py-2 px-3 rounded-md mb-4 shadow-md text-sm animate-scale-in">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Formulário --}}
        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            {{-- E-mail --}}
            <div class="animate-fade-in-up">
                <label class="text-white font-medium">E-mail</label>
                <input type="email" name="email" value="{{ old('email') }}"
                       class="w-full mt-1 px-4 py-3 rounded-lg bg-white/30 text-white placeholder-white/70
                              border border-white/20 focus:border-white/60 focus:ring-2 focus:ring-white/40
                              outline-none transition-all"
                       required autofocus>
            </div>

            {{-- Senha --}}
            <div class="animate-fade-in-up" style="animation-delay: .1s">
                <label class="text-white font-medium">Senha</label>
                <input type="password" name="password"
                       class="w-full mt-1 px-4 py-3 rounded-lg bg-white/30 text-white placeholder-white/70
                              border border-white/20 focus:border-white/60 focus:ring-2 focus:ring-white/40
                              outline-none transition-all"
                       required>
            </div>

            {{-- Botão --}}
            <button class="w-full py-3 rounded-lg text-white font-semibold text-lg
                           bg-gradient-to-r from-brand to-purple-600 shadow-lg hover:shadow-xl
                           hover:opacity-95 active:scale-95 transition-all animate-fade-in-up"
                    style="animation-delay: .2s">
                Entrar
            </button>

        </form>
    </div>

</body>
</html>
