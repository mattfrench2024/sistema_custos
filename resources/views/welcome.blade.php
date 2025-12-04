<!DOCTYPE html>
<html lang="pt-BR" class="antialiased scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Verreschi Management — Plataforma Corporativa</title>

    {{-- Vite (Tailwind + JS) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Tokens e microdesign premium --}}
    <style>
        :root {
            --brand-from: #f9821a;
            --brand-to:   #fc940d;
            --brand-glow: rgba(249,130,26,0.12);

            --surface: #ffffff;
            --surface-soft: #fafafa;
            --surface-glass: rgba(255,255,255,0.65);

            --text-strong: #0f172a;
            --text-soft: #475569;
            --text-muted: #64748b;

            --radius-sm: 12px;
            --radius-md: 18px;
            --radius-lg: 24px;

            --shadow-soft: 0 8px 24px rgba(0,0,0,0.06);
            --shadow-card: 0 12px 32px rgba(0,0,0,0.10);
            --shadow-deep: 0 18px 48px rgba(0,0,0,0.18);
        }

        .bg-brand {
            background: linear-gradient(90deg, var(--brand-from), var(--brand-to));
        }

        .glass {
            backdrop-filter: blur(12px);
            background: var(--surface-glass);
            border: 1px solid rgba(255,255,255,0.45);
        }

        @media (prefers-reduced-motion: no-preference) {
            .fade-up {
                opacity: 0;
                transform: translateY(14px);
                animation: fade-up .5s ease-out forwards;
            }
            @keyframes fade-up {
                to { opacity:1; transform: translateY(0); }
            }
        }
    </style>

</head>
<body class="bg-gradient-to-br from-white via-slate-50 to-white text-slate-900">

    {{-- HEADER --}}
    <header class="max-w-7xl mx-auto px-6 py-6 flex items-center justify-between">
        <a href="{{ route('login') }}" class="flex items-center gap-3">
            <img src="{{ asset('img/verreschi_management.png') }}" 
                 class="w-11 h-11 object-contain rounded-xl shadow-sm"
                 alt="Verreschi Management">

            <div class="leading-tight">
                <span class="block font-semibold text-[var(--text-strong)]">Verreschi Management</span>
                <span class="text-xs text-[var(--text-muted)]">Plataforma Corporativa</span>
            </div>
        </a>

        <nav class="hidden sm:flex items-center gap-8 text-sm">
            <a href="#sobre" class="text-slate-600 hover:text-slate-900 transition">Sobre</a>
            <a href="#recursos" class="text-slate-600 hover:text-slate-900 transition">Recursos</a>
            <a href="#contato" class="text-slate-600 hover:text-slate-900 transition">Contato</a>

            <a href="{{ route('login') }}"
               class="px-4 py-2 rounded-xl bg-brand text-white shadow-md hover:opacity-95 transition">
                Entrar
            </a>
        </nav>
    </header>

    {{-- HERO --}}
    <main class="max-w-7xl mx-auto px-6 lg:px-8 pt-12 lg:pt-20">
        <section class="grid lg:grid-cols-2 gap-14 items-center">

            {{-- TEXTO --}}
            <div class="space-y-7 fade-up">
                <h1 class="text-5xl lg:text-6xl font-extrabold leading-tight tracking-tight text-[var(--text-strong)]">
                    Gestão Corporativa Precisa e Integrada
                </h1>

                <p class="text-lg text-[var(--text-soft)] max-w-xl">
                    Controle financeiro, RH, auditoria e governança em um único ambiente — 
                    construído para empresas que exigem precisão, compliance e visão executiva.
                </p>

                {{-- CTAs --}}
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center gap-3 px-6 py-3 rounded-2xl bg-brand text-white font-semibold shadow-lg active:scale-[0.98] transition">
                        Iniciar sessão
                        <svg class="w-5 h-5" fill="none" stroke="white" stroke-width="1.7" viewBox="0 0 24 24">
                            <path d="M5 12h14m-7-7 7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>

                    <a href="#sobre"
                       class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 transition">
                        Conhecer recursos
                    </a>
                </div>

                {{-- MICRO FEATURES --}}
                <div class="flex flex-wrap gap-6 pt-2">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 flex items-center justify-center bg-brand/10 text-brand rounded-xl">📊</div>
                        <div>
                            <strong class="block text-slate-900 text-sm">Financeiro</strong>
                            <span class="text-xs text-[var(--text-muted)]">Notas • Conciliação</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 flex items-center justify-center bg-brand/10 text-brand rounded-xl">👥</div>
                        <div>
                            <strong class="block text-slate-900 text-sm">RH / DP</strong>
                            <span class="text-xs text-[var(--text-muted)]">Folha • Custos</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CARD PRINCIPAL --}}
            <aside class="glass p-10 rounded-[26px] shadow-card relative fade-up" style="animation-delay: .15s">
                <h3 class="text-xl font-semibold text-[var(--text-strong)]">Ambiente Corporativo Integrado</h3>

                <ul class="mt-6 space-y-5 text-[var(--text-soft)]">
                    <li class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-brand/10 rounded-xl flex items-center justify-center text-brand">🧾</div>
                        <div>
                            <strong class="text-slate-900">Gestão de Notas</strong>
                            <p class="text-sm">Upload seguro, validações e controle por vencimentos.</p>
                        </div>
                    </li>

                    <li class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-brand/10 rounded-xl flex items-center justify-center text-brand">📈</div>
                        <div>
                            <strong class="text-slate-900">Dashboards Estratégicos</strong>
                            <p class="text-sm">Indicadores claros e acionáveis para diretoria.</p>
                        </div>
                    </li>

                    <li class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-brand/10 rounded-xl flex items-center justify-center text-brand">🔒</div>
                        <div>
                            <strong class="text-slate-900">Compliance & Auditoria</strong>
                            <p class="text-sm">Trilhas completas e logs imutáveis.</p>
                        </div>
                    </li>
                </ul>

                <a href="{{ route('login') }}"
                   class="mt-8 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand text-white font-medium shadow-lg hover:opacity-95 transition">
                    Entrar na Demonstração
                </a>
            </aside>
        </section>

        {{-- GRID DE RECURSOS --}}
        <section id="recursos" class="mt-28">
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ([
                    ['title' => 'Contas a pagar', 'desc' => 'Controle total de notas, prazos e fluxo de caixa.', 'detail' => 'CSV / PDF'],
                    ['title' => 'Folha & Benefícios', 'desc' => 'Encargos e consolidação por departamento.', 'detail' => 'Importação / Relatórios'],
                    ['title' => 'Auditoria', 'desc' => 'Trilha de alterações e acessos.', 'detail' => 'RBAC completo']
                ] as $card)
                <article class="bg-white p-6 rounded-[20px] shadow-card hover:shadow-deep transition">
                    <h4 class="font-semibold text-brand text-lg">{{ $card['title'] }}</h4>
                    <p class="mt-3 text-[var(--text-soft)] text-sm">{{ $card['desc'] }}</p>
                    <div class="mt-4 text-xs text-[var(--text-muted)]">{{ $card['detail'] }}</div>
                </article>
                @endforeach
            </div>
        </section>

        {{-- SOBRE --}}
        <section id="sobre" class="mt-28">
            <div class="glass p-10 rounded-[26px] shadow-card max-w-4xl mx-auto">
                <div class="grid md:grid-cols-2 gap-10">
                    <div>
                        <h3 class="text-2xl font-semibold text-[var(--text-strong)]">Por que escolher?</h3>

                        <p class="mt-4 text-[var(--text-soft)]">
                            A plataforma foi projetada para governança, segurança e escalabilidade —
                            com auditoria completa e visão executiva.
                        </p>

                        <ul class="mt-6 space-y-2 text-sm text-[var(--text-muted)]">
                            <li>• RBAC avançado</li>
                            <li>• Relatórios PDF (mPDF)</li>
                            <li>• Uploads seguros (S3 Ready)</li>
                        </ul>
                    </div>

                    <div class="flex flex-col gap-5">
                        <div class="p-5 bg-white rounded-xl shadow-sm">
                            <div class="flex justify-between">
                                <div>
                                    <span class="text-xs text-[var(--text-muted)]">Custo Mensal</span>
                                    <div class="text-lg font-semibold mt-1">R$ 1.234.567</div>
                                </div>
                                <span class="text-green-600 text-sm font-medium">↗ 8%</span>
                            </div>
                            <div class="mt-4 h-14 bg-brand/10 rounded-md"></div>
                        </div>

                        <div class="p-5 bg-white rounded-xl shadow-sm">
                            <span class="text-xs text-[var(--text-muted)]">Top fornecedor</span>
                            <div class="font-medium mt-2">Fornecedor Exemplo S/A</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- FOOTER --}}
        <footer id="contato" class="mt-28 py-12 border-t border-slate-200">
            <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between gap-6">
                <div class="text-sm text-[var(--text-muted)]">
                    © {{ date('Y') }} Verreschi Management — Todos os direitos reservados
                </div>

                <div class="flex items-center gap-6 text-sm text-[var(--text-soft)]">
                    <a href="#" class="hover:text-slate-900 transition">Privacidade</a>
                    <a href="#" class="hover:text-slate-900 transition">Termos</a>
                    <a href="mailto:contato@grupoverreschi.com.br"
                       class="hover:text-slate-900 transition">contato@grupoverreschi.com.br</a>
                </div>
            </div>
        </footer>

    </main>

</body>
</html>
