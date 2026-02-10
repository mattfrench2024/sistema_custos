@extends('layouts.app')

@section('content')
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

    /* Custom Scrollbar for Table */
    .custom-scrollbar::-webkit-scrollbar {
        height: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: #cbd5e1;
        border-radius: 20px;
    }

    /* Utility Helpers */
    .erp-card {
        background: white;
        border-radius: var(--card-radius);
        box-shadow: var(--shadow);
        border: 1px solid rgba(0,0,0,0.02);
    }

    .erp-gradient-text {
        background: linear-gradient(135deg, var(--brand-from), var(--brand-to));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .erp-btn-primary {
        background: linear-gradient(135deg, var(--brand-from), var(--brand-to));
        color: white;
        transition: all 0.2s ease;
        box-shadow: 0 4px 12px rgba(249, 130, 26, 0.25);
    }
    .erp-btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(249, 130, 26, 0.35);
    }

    .erp-btn-ghost {
        background: #f1f5f9;
        color: var(--slate-800);
        transition: all 0.2s ease;
    }
    .erp-btn-ghost:hover {
        background: #e2e8f0;
        color: #0f172a;
    }
</style>

<div class="min-h-screen bg-slate-50 py-10 px-4 sm:px-6 lg:px-8 font-sans">
    <div class="max-w-7xl mx-auto space-y-6">
        
        {{-- Header Section --}}
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-slate-900">
                    Notas Fiscais
                </h1>
                <p class="mt-1 text-sm text-slate-500 flex items-center gap-2">
                    <span class="inline-block w-2 h-2 rounded-full bg-[var(--brand-from)]"></span>
                    Empresa Ativa: <span class="font-semibold text-slate-700">{{ $empresaNome }}</span>
                </p>
            </div>
            
            {{-- Optional: Contextual Actions or Filter placeholder could go here --}}
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 bg-white border border-slate-200 rounded-full text-xs font-medium text-slate-500 shadow-sm">
                    Atualizado: {{ now()->format('H:i') }}
                </span>
            </div>
        </header>

        {{-- Main Data Card --}}
        <div class="erp-card overflow-hidden bg-white">
            
            {{-- Table Container --}}
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-900 text-white">
                            <th scope="col" class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-slate-300 w-24">
                                Número
                            </th>
                            <th scope="col" class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-slate-300 w-20">
                                Tipo
                            </th>
                            <th scope="col" class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-slate-300 w-32">
                                Emissão
                            </th>
                            <th scope="col" class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-slate-300">
                                Cliente / Destinatário
                            </th>
                            <th scope="col" class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-slate-300 text-right w-40">
                                Valor Total
                            </th>
                            <th scope="col" class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-slate-300 text-center w-40">
                                Ações
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($notas as $nota)
                            <tr class="group hover:bg-orange-50/30 transition-colors duration-150">
                                
                                {{-- Numero --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-bold text-slate-700 group-hover:text-[var(--brand-from)] transition-colors">
                                        #{{ $nota->numero }}
                                    </span>
                                </td>

                                {{-- Tipo --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-800 border border-slate-200">
                                        {{ $nota->tipo }}
                                    </span>
                                </td>

                                {{-- Data --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center text-sm text-slate-600">
                                        <svg class="mr-1.5 h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        {{ optional($nota->data_emissao)->format('d/m/Y') }}
                                    </div>
                                </td>

                                {{-- Cliente --}}
                                <td class="px-6 py-4">
                                    <div class="text-sm text-slate-700 font-medium truncate max-w-[200px] md:max-w-xs" title="{{ $nota->payload['Cabecalho']['cRazaoDestinatario'] ?? '-' }}">
                                        {{ $nota->payload['Cabecalho']['cRazaoDestinatario'] ?? '-' }}
                                    </div>
                                </td>

                                {{-- Valor --}}
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <span class="text-sm font-mono font-bold text-slate-800">
                                        R$ {{ number_format($nota->valor_total, 2, ',', '.') }}
                                    </span>
                                </td>

                                {{-- Ações (PDF) --}}
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if ($nota->possui_pdf && $nota->pdf_path)
                                        <a href="{{ route('omie.notas.pdf.ver', ['empresa' => $empresa, 'nota' => $nota->id]) }}" 
                                           target="_blank"
                                           class="erp-btn-ghost inline-flex items-center justify-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-[var(--radius-md)] w-full max-w-[120px]">
                                            <svg class="mr-1.5 h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 5 8.268 7.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Ver PDF
                                        </a>
                                    @else
                                        <form method="POST" action="{{ route('omie.notas.pdf', ['empresa' => $empresa, 'nota' => $nota->id]) }}">
                                            @csrf
                                            <button type="submit" 
                                                class="erp-btn-primary inline-flex items-center justify-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-[var(--radius-md)] w-full max-w-[120px]">
                                                <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                </svg>
                                                Gerar PDF
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-slate-400">
                                        <svg class="h-12 w-12 mb-3 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <span class="text-sm font-medium">Nenhuma nota fiscal encontrada.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer / Pagination --}}
            @if($notas->hasPages())
                <div class="bg-slate-50 px-6 py-4 border-t border-slate-200">
                    {{ $notas->links() }}
                </div>
            @endif
        </div>
        
        <div class="text-center mt-6 text-xs text-slate-400">
            &copy; {{ date('Y') }} Sistema ERP Financeiro &bull; Módulo Fiscal
        </div>
    </div>
</div>
@endsection