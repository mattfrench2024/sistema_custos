<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Sistema de Custos — Bem-vindo</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --brand-from: #f9821a;
            --brand-to:   #fc940d;
            --glass-bg: rgba(255,255,255,0.65);
            --card-radius: 18px;
            --shadow: 0 12px 32px rgba(0,0,0,0.12);
        }

        .gradient-text {
            background: linear-gradient(90deg, var(--brand-from), var(--brand-to));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .glass-card {
            background: var(--glass-bg);
            border-radius: var(--card-radius);
            box-shadow: var(--shadow);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
        }

        body {
            background: radial-gradient(circle at top, #ffffff, #efefef);
        }
    </style>
</head>

<body class="antialiased">

    <div class="min-h-screen flex flex-col items-center justify-center p-6">

        <div class="text-center mb-14 animate-fade-in">
            <h1 class="text-5xl font-extrabold gradient-text tracking-tight drop-shadow-sm">
                Sistema de Custos
            </h1>

            <p class="mt-3 text-gray-600 text-lg max-w-md mx-auto leading-relaxed">
                Controle financeiro integrado, pessoal e estratégico — tudo em um só lugar.
            </p>
        </div>

        <div class="glass-card w-full max-w-xl p-10 backdrop-blur-xl transform hover:scale-[1.01] transition-all duration-300">

            <h2 class="text-2xl font-semibold text-gray-800 text-center mb-6">
                Bem-vindo ao ambiente corporativo
            </h2>

            <p class="text-gray-700 leading-relaxed text-center mb-10">
                Uma plataforma moderna para monitorar custos, visualizar dashboards,
                organizar despesas e administrar operações com total segurança e precisão.
            </p>

            <div class="flex flex-col space-y-4">

                <a  href="{{ route('login') }}"
                    class="w-full py-3 text-center font-semibold text-white rounded-lg transition
                           bg-gradient-to-r from-[var(--brand-from)] to-[var(--brand-to)]
                           hover:opacity-90 active:scale-[0.97]">
                    Acessar o Sistema
                </a>

                <a  href="#sobre"
                    class="w-full py-3 text-center font-medium text-gray-700 rounded-lg border
                           border-gray-300 hover:bg-gray-100 transition active:scale-[0.97]">
                    Saiba mais
                </a>

            </div>

        </div>

        <div id="sobre" class="mt-20 max-w-3xl text-center text-gray-700 leading-relaxed animate-fade-in-up">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Por que usar o Sistema?</h3>

            <p class="mb-6">
                O sistema unifica todos os processos essenciais para que cada departamento
                opere com precisão, clareza e eficiência.
            </p>

            <ul class="text-left mx-auto inline-block space-y-2 text-gray-700">
                <li>• Financeiro — contas, notas, vencimentos e dashboards inteligentes.</li>
                <li>• RH/DP — folha, benefícios, custos por colaborador e relatórios.</li>
                <li>• Auditoria/Diretoria — visão estratégica, indicadores e compliance.</li>
                <li>• TI/Admin — controle total, permissões, logs e governança.</li>
            </ul>
        </div>

    </div>

</body>
</html>
