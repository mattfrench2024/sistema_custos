<div {{ $attributes->merge([
    'class' => '
        bg-white/80 dark:bg-white/5 
        backdrop-blur-md 
        shadow-xl 
        rounded-2xl 
        p-6 
        transition-all 
        hover:-translate-y-0.5 
        hover:shadow-2xl
    '
]) }}>
    {{ $slot }}
</div>
