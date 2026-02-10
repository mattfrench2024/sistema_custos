<style>
    :root {
        --brand-from: #f9821a;
        --brand-to:   #fc940d;
        --glass-bg: rgba(255,255,255,0.85);
        --card-radius: 14px;
        --shadow: 0 8px 28px rgba(22,22,22,0.08);
        --radius-lg: 1rem;
        --radius-md: 0.75rem;
        --shadow-soft: 0 1px 2px rgba(0,0,0,.04), 0 12px 32px rgba(0,0,0,.08);
        --slate-800: #1e293b;
        --slate-500: #64748b;
        --slate-50: #f8fafc;
    }
</style>

<nav class="fixed top-0 left-0 w-full z-50">
    <div class="backdrop-blur-xl bg-[var(--glass-bg)] dark:bg-[#0f172a]/80 border-b border-black/5 dark:border-white/10 shadow-[var(--shadow-soft)]">
        <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">

            <!-- Logo / Brand -->
            <a href="/financeiro/analitico" class="flex items-center gap-3 group">
                <img
                    src="{{ asset('img/verreschi_management.webp') }}"
                    alt="Verreschi Management"
                    class="h-10 w-auto object-contain select-none"
                />

                <div class="flex flex-col leading-tight">
                    <span class="text-[15px] font-semibold text-[var(--slate-800)] dark:text-slate-100 tracking-tight">
                        Sistema de Custos
                    </span>
                    <span class="text-xs text-[var(--slate-500)] dark:text-slate-400">
                        Financeiro & Gestão
                    </span>
                </div>
            </a>

            <!-- Right Actions -->
            <div class="flex items-center gap-3">

                <!-- Perfil -->
                <a href="/profile"
                   class="px-4 py-2 rounded-[var(--radius-md)] text-sm font-medium
                          text-[var(--slate-800)] dark:text-slate-200
                          hover:bg-black/5 dark:hover:bg-white/10 transition">
                    Perfil
                </a>

                <!-- Dark Mode -->
                <button
                    id="darkModeToggle"
                    aria-label="Alternar tema"
                    class="w-10 h-10 flex items-center justify-center rounded-lg
                           bg-black/5 dark:bg-white/10
                           text-[var(--slate-800)] dark:text-slate-200
                           hover:scale-105 transition"
                >
                    <svg id="iconMoon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
                    </svg>

                    <svg id="iconSun" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 3v1m0 16v1m8.66-11.66l-.7.7m-13.92 13.92l-.7.7M4 12H3m16 0h1"/>
                    </svg>
                </button>

                <!-- Divider -->
                <div class="h-8 w-px bg-black/10 dark:bg-white/10"></div>

                <!-- Logout -->
                <form method="POST" action="/logout">
                    @csrf
                    <button
                        class="px-4 py-2 rounded-[var(--radius-md)] text-sm font-semibold
                               text-white bg-gradient-to-r from-[var(--brand-from)] to-[var(--brand-to)]
                               shadow hover:shadow-md hover:brightness-105 transition">
                        Sair
                    </button>
                </form>

            </div>
        </div>
    </div>
</nav>

<script>
    const html = document.documentElement;
    const toggle = document.getElementById('darkModeToggle');
    const sun = document.getElementById('iconSun');
    const moon = document.getElementById('iconMoon');

    function syncIcons() {
        if (html.classList.contains('dark')) {
            moon.classList.remove('hidden');
            sun.classList.add('hidden');
        } else {
            sun.classList.remove('hidden');
            moon.classList.add('hidden');
        }
    }

    if (localStorage.getItem('theme') === 'dark') {
        html.classList.add('dark');
    }

    syncIcons();

    toggle.addEventListener('click', () => {
        html.classList.toggle('dark');
        localStorage.setItem('theme', html.classList.contains('dark') ? 'dark' : 'light');
        syncIcons();
    });
</script>
