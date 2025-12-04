@extends('layouts.app')

@section('content')
<h2 class="text-3xl font-bold mb-6">Painel de Auditoria</h2>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    <div class="p-6 bg-red-50 border-l-4 border-red-500 rounded shadow">
        <h3 class="text-lg font-semibold">Eventos de Auditoria</h3>
        <p class="text-4xl font-bold text-red-700">{{ \App\Models\AuditLog::count() }}</p>
    </div>

</div>

<div class="mt-10 bg-white p-6 rounded shadow">
    <h3 class="font-bold text-xl mb-4">Últimos eventos</h3>

    <ul class="divide-y">
        @foreach(\App\Models\AuditLog::latest()->take(10)->get() as $log)
            <li class="py-2">
                <strong>{{ $log->user_name }}</strong> — {{ $log->action }}
                <span class="text-gray-500 text-sm">({{ $log->created_at->format('d/m/Y H:i') }})</span>
            </li>
        @endforeach
    </ul>
</div>

@endsection
