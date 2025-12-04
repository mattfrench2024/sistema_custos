@props(['href' => null])

@if($href)
    <a href="{{ $href }}" 
       {{ $attributes->merge([
           'class' => '
                inline-flex items-center justify-center
                px-4 py-2 rounded-xl font-semibold
                bg-gradient-to-r from-brand-gradientFrom to-brand-gradientTo
                text-white shadow-lg hover:shadow-xl
                transition-all duration-200
                hover:-translate-y-0.5
                focus:ring-2 ring-brand-light
           '
       ]) }}>
       {{ $slot }}
    </a>
@else
    <button 
       {{ $attributes->merge([
           'class' => '
                inline-flex items-center justify-center
                px-4 py-2 rounded-xl font-semibold
                bg-gradient-to-r from-brand-gradientFrom to-brand-gradientTo
                text-white shadow-lg hover:shadow-xl
                transition-all duration-200
                hover:-translate-y-0.5
                focus:ring-2 ring-brand-light
           '
       ]) }}>
       {{ $slot }}
    </button>
@endif
