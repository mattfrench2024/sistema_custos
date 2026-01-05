@props(['title', 'value', 'icon', 'color', 'desc'])

<div class="rounded-2xl border border-gray-200 dark:border-white/10 p-6
            bg-white dark:bg-gray-900/60 shadow-sm hover:shadow-lg
            backdrop-blur-xl transition-all duration-300 group">

    <div class="flex items-center justify-between">
        <h3 class="text-sm text-gray-500 dark:text-gray-400">{{ $title }}</h3>

        <i data-lucide="{{ $icon }}"
           class="w-5 h-5 text-{{ $color }}-500 group-hover:scale-110 transition"></i>
    </div>

    <p class="mt-3 text-3xl font-semibold text-gray-900 dark:text-white">
        {{ $value }}
    </p>

    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
        {{ $desc }}
    </p>
</div>
