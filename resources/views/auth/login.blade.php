<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar - Verreschi Management</title>

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root{
            --brand-from: #f9821a;
            --brand-to:   #fc940d;
            --glass-bg: rgba(255,255,255,0.85);
            --card-radius: 14px;
            --shadow: 0 8px 28px rgba(22,22,22,0.08);
        }

        body {
            background: linear-gradient(135deg, var(--brand-from), var(--brand-to));
            height: 100vh;
            font-family: 'Inter', sans-serif;
        }

        .login-card {
            background: var(--glass-bg);
            border-radius: var(--card-radius);
            box-shadow: var(--shadow);
            padding: 2.2rem;
            min-width: 380px;
            backdrop-filter: blur(12px);
        }

        .brand-title {
            font-weight: 700;
            font-size: 1.6rem;
            color: #333;
        }

        .btn-login {
            background: linear-gradient(135deg, var(--brand-from), var(--brand-to));
            border: none;
            padding: .7rem;
            font-weight: 600;
        }
        .btn-login:hover {
            opacity: .92;
        }
    </style>

</head>

<body class="d-flex justify-content-center align-items-center">

    <div class="login-card">
        <h3 class="text-center brand-title mb-4">Verreschi Management</h3>

        {{-- Mensagens de erro --}}
        @if ($errors->any())
            <div class="alert alert-danger py-2">
                <small>{{ $errors->first() }}</small>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">E-mail</label>
                <input type="email" name="email" value="{{ old('email') }}" class="form-control" required autofocus>
            </div>

            <div class="mb-3">
                <label class="form-label">Senha</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <button class="btn btn-login text-white w-100">Entrar</button>
        </form>
    </div>

</body>
</html>
