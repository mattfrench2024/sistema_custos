<nav class="navbar fixed top-0 left-0 w-full z-50 border-b border-black/5 dark:border-white/10 bg-white/80 dark:bg-gray-900/80 backdrop-blur-xl">
    <div class="max-w-7xl mx-auto px-6 flex items-center justify-between h-16">

        {{-- Logo / Brand --}}
        <a href="/dashboard" class="flex items-center gap-3">
            <img 
                src="{{ asset('img/verreschi_management.webp') }}" 
                alt="Verreschi Management Logo"
                class="h-10 w-auto object-contain select-none"
            >

            <span class="text-gray-800 dark:text-gray-200 font-semibold tracking-tight text-lg">
                Sistema de Custos
            </span>
        </a>

        {{-- Right Side --}}
        <div class="flex items-center gap-6">

            {{-- Profile --}}
            <a href="/profile"
               class="text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition">
                Perfil
            </a>

            {{-- Botão sair --}}
            <form method="POST" action="/logout">
                @csrf
                <button
                    class="px-4 py-2 rounded-md text-sm font-medium text-white bg-gradient-to-r
                           from-red-500 to-red-600 shadow-sm hover:shadow-md transition">
                    Sair
                </button>
            </form>

            {{-- Dark Mode Toggle --}}
            <button
                id="darkModeToggle"
                class="w-10 h-10 flex items-center justify-center rounded-lg
                       bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200
                       hover:bg-gray-300 dark:hover:bg-gray-600 transition"
            >
                <svg id="darkIcon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
                </svg>

                <svg id="lightIcon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 3v1m0 16v1m8.66-11.66l-.7.7m-13.92 13.92l-.7.7M4 12H3m16 0h1m-1.66-5.66l-.7-.7M5.34 18.66l-.7-.7"/>
                </svg>
            </button>

        </div>
    </div>
</nav>

<script>
    const toggle = document.getElementById('darkModeToggle');
    const html = document.documentElement;
    const darkIcon = document.getElementById('darkIcon');
    const lightIcon = document.getElementById('lightIcon');

    function updateIcons() {
        if (html.classList.contains('dark')) {
            darkIcon.classList.add('hidden');
            lightIcon.classList.remove('hidden');
        } else {
            lightIcon.classList.add('hidden');
            darkIcon.classList.remove('hidden');
        }
    }

    toggle.addEventListener('click', () => {
        html.classList.toggle('dark');
        localStorage.setItem('theme', html.classList.contains('dark') ? 'dark' : 'light');
        updateIcons();
    });

    if (localStorage.getItem('theme') === 'dark') {
        html.classList.add('dark');
    }

    updateIcons();
</script>
