@props([
    'label' => '',
    'route' => '',
    'icon'  => '',
])

@php
    $isActive = request()->routeIs($route);
@endphp

<a href="{{ route($route) }}"
   class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all font-medium
          {{ $isActive
                ? 'bg-gradient-to-r from-[var(--brand-from)]/20 to-[var(--brand-to)]/20 text-[var(--brand-from)] dark:text-[var(--brand-to)] border border-[var(--brand-from)]/40 shadow-sm'
                : 'text-gray-700 dark:text-gray-300 hover:bg-white/30 dark:hover:bg-gray-800/40' }}"
>
    {{-- Ícone Lucide --}}
    <i data-lucide="{{ $icon }}"
       class="w-5 h-5 {{ $isActive ? 'text-[var(--brand-from)] dark:text-[var(--brand-to)]' : '' }}"></i>

    <span>{{ $label }}</span>
</a>
