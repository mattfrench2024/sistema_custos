<style>
:root{
  --brand-from: #F9821A;
  --brand-to: #FC940D;
  --glass-bg: rgba(255,255,255,0.6);
  --glass-border: rgba(0,0,0,0.04);
  --muted: #6B7280;
  --card-radius: 1rem;
}

.glass-card{
  background: var(--glass-bg);
  backdrop-filter: blur(10px) saturate(1.05);
  border: 1px solid var(--glass-border);
  border-radius: var(--card-radius);
}

.table-head-gradient{
  background: linear-gradient(135deg, var(--brand-from), var(--brand-to));
}

.row-hover{
  transition: background .18s ease, transform .18s ease;
}

.row-hover:hover{
  background: rgba(0,0,0,0.025);
}

.nota-link{
  position: relative;
  transition: color .15s ease, transform .15s ease;
}

.nota-link:hover{
  color: var(--brand-from);
  transform: translateY(-1px);
}

.nota-missing{
  background: rgba(249,130,26,0.08);
  color: #9A3412;
}

.nota-ok{
  background: rgba(34,197,94,0.08);
  color: #166534;
}
</style>
@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-8 space-y-6">

    <!-- HEADER -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-semibold tracking-tight text-gray-900">
                Auditoria de Notas Fiscais
            </h1>
            <p class="text-sm text-[var(--muted)] mt-1">
                Conferência mensal de documentos fiscais por categoria
            </p>
        </div>
    </div>

    <!-- TABLE CARD -->
    <div class="glass-card overflow-hidden">

        <!-- SCROLL -->
        <div class="overflow-x-auto">

            <table class="min-w-full text-sm">

                <!-- HEAD -->
                <thead class="table-head-gradient text-white">
                    <tr>
                        <th class="px-5 py-4 text-left font-semibold">ID</th>
                        <th class="px-5 py-4 text-left font-semibold">Categoria</th>
                        <th class="px-5 py-4 text-left font-semibold">Ano</th>
                        <th class="px-5 py-4 text-left font-semibold">CNPJ</th>
                        <th class="px-5 py-4 text-left font-semibold">
                            Notas Fiscais (Mensal)
                        </th>
                    </tr>
                </thead>

                <!-- BODY -->
                <tbody class="divide-y divide-black/5">

                    @foreach($notas as $nota)
                        <tr class="row-hover">

                            <td class="px-5 py-4 font-medium text-gray-800">
                                {{ $nota->id }}
                            </td>

                            <td class="px-5 py-4 text-gray-800">
                                {{ $nota->Categoria }}
                            </td>

                            <td class="px-5 py-4 text-gray-700">
                                {{ $nota->Ano }}
                            </td>

                            <td class="px-5 py-4 text-gray-700">
                                {{ $nota->cnpj ?? '—' }}
                            </td>

                            <!-- MESES -->
                            <td class="px-5 py-4">
                                <div class="flex flex-wrap gap-2">

                                    @foreach(['jan','fev','mar','abr','mai','jun','jul','ago','set','out','nov','dez'] as $mes)
                                        @php $file = "file_$mes"; @endphp

                                        @if($nota->$file)
                                            <a
                                                href="{{ asset('storage/' . $nota->$file) }}"
                                                target="_blank"
                                                class="nota-link nota-ok px-2.5 py-1 rounded-lg text-xs font-semibold"
                                                title="Nota disponível"
                                            >
                                                {{ strtoupper($mes) }}
                                            </a>
                                        @else
                                            <span
                                                class="nota-missing px-2.5 py-1 rounded-lg text-xs font-medium"
                                                title="Nota ausente"
                                            >
                                                {{ strtoupper($mes) }}
                                            </span>
                                        @endif

                                    @endforeach

                                </div>
                            </td>

                        </tr>
                    @endforeach

                </tbody>
            </table>

        </div>
    </div>
</div>
@endsection
