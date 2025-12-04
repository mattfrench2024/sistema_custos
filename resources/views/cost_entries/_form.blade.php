@php
    $entry = $entry ?? null;
@endphp

<x-app-input 
    label="Categoria"
    name="categoria"
    type="text"
    value="{{ $entry->categoria ?? '' }}"
/>

<x-app-input 
    label="Valor"
    name="valor"
    type="number"
    step="0.01"
    value="{{ $entry->valor ?? '' }}"
/>

<x-app-input 
    label="Descrição"
    name="descricao"
    type="text"
    value="{{ $entry->descricao ?? '' }}"
/>
