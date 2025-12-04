@props([
    'label' => null,
    'name',
    'type' => 'text',
    'value' => '',
])

<div class="space-y-2">
    @if($label)
        <label class="text-sm font-medium text-gray-600 dark:text-gray-300">
            {{ $label }}
        </label>
    @endif

    <input 
        type="{{ $type }}"
        name="{{ $name }}"
        value="{{ old($name, $value) }}"
        {{ $attributes->merge([
            'class' => '
                w-full px-4 py-3 text-sm
                rounded-xl border
                border-gray-300 dark:border-white/10
                bg-white/60 dark:bg-white/5
                backdrop-blur-lg
                focus:outline-none
                focus:ring-2 focus:ring-brand-light
                transition-all
            '
        ]) }}
    >
</div>

@error($name)
    <div class="text-sm text-red-500">{{ $message }}</div>
@enderror
