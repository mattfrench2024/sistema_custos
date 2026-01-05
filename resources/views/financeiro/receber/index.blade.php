@extends('layouts.app')

@section('content')
<style>
    :root{
        --brand-from: #ff7a18;
        --brand-to:   #ffb347;
        --brand-purple: #7e22cc;
        --soft-white: rgba(255,255,255,0.9);
        --soft-black: rgba(8,10,15,0.88);
        --glass-border: rgba(255,255,255,0.06);
        --muted: #6b7280;
    }

    /* small refinements */
    .badge-anim {
        transition: transform .16s cubic-bezier(.2,.9,.2,1), box-shadow .16s;
    }
    .badge-anim:active { transform: translateY(1px) scale(.995); }

    .glass-bg {
        background-image: linear-gradient(180deg, rgba(255,255,255,0.56), rgba(255,255,255,0.40));
        backdrop-filter: blur(8px) saturate(105%);
    }

    .focus-ring:focus-visible {
        outline: none;
        box-shadow: 0 0 0 6px rgba(255, 122, 24, 0.10);
        border-color: rgba(255,122,24,0.9);
    }

    /* sticky header shadow subtle */
    thead.sticky th {
        position: sticky;
        top: 0;
        backdrop-filter: blur(6px);
        z-index: 20;
    }

    /* responsive small screens: stack labels */
    @media (max-width: 640px){
        .hide-sm { display:none; }
        .text-sm-center { text-align:center; }
    }

    /* tiny focus-visible improvements for buttons */
    button:focus-visible { box-shadow: 0 0 0 6px rgba(34, 197, 94, 0.06); }

    /* accessible color adjustments */
    .dark .glass-bg { background-image: linear-gradient(180deg, rgba(12,12,12,0.5), rgba(18,18,18,0.45)); }
</style>
  <br>

    <form method="GET"
          class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-5 rounded-2xl border border-white/10 shadow-sm glass-bg dark:bg-gray-900/40 dark:border-gray-800/60">

        <!-- Mês -->
        <div class="flex flex-col">
            <label for="mes" class="text-xs font-semibold text-gray-600 dark:text-gray-300 mb-2">
                Mês
            </label>

            <div class="relative">
                <select id="mes" name="mes"
                        class="w-full p-3 rounded-xl bg-white/90 dark:bg-gray-800/60
                               border border-gray-200/60 dark:border-gray-700 text-sm
                               focus-ring transition outline-none appearance-none">
                    @for ($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ $i == $mes ? 'selected' : '' }}>
                            {{ ucfirst(\Carbon\Carbon::create()->month($i)->translatedFormat('F')) }}
                        </option>
                    @endfor
                </select>

                <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
                    <svg class="w-4 h-4 text-gray-400 dark:text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.064a.75.75 0 111.12.998l-4.25 4.656a.75.75 0 01-1.12 0L5.21 8.28a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Ano -->
        <div class="flex flex-col">
            <label for="ano" class="text-xs font-semibold text-gray-600 dark:text-gray-300 mb-2">
                Ano
            </label>
            <input id="ano" type="number" name="ano" value="{{ $ano }}"
                   class="w-full p-3 rounded-xl bg-white/90 dark:bg-gray-800/60
                          border border-gray-200/60 dark:border-gray-700 text-sm
                          focus-ring transition outline-none"
                   inputmode="numeric" aria-label="Ano">
        </div>

        <!-- ACTION -->
        <div class="flex items-end">
            <button
                class="w-full flex items-center justify-center gap-3 px-6 py-3 rounded-xl font-semibold shadow-md
                       bg-gradient-to-r from-[var(--brand-from)] to-[var(--brand-to)]
                       text-white hover:brightness-105 active:scale-[0.996] focus-ring transition"
                aria-label="Filtrar">
                <svg class="w-5 h-5 -ml-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M22 4H2l7 8v6l6-3v-11z"></path>
                </svg>
                Filtrar
            </button>
        </div>
    </form>

    <!-- CARD + TABLE -->
    <div class="mt-6 rounded-2xl border border-white/8 shadow-xl overflow-hidden bg-white/65 dark:bg-gray-900/40 glass-bg">
        <div class="px-6 py-4 border-b border-white/6 dark:border-gray-800/60 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Lançamentos</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Resultados filtrados por mês/ano</p>
            </div>

            <div class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-3">
                <span class="hidden sm:inline text-sm">Total:</span>
                <strong class="text-gray-700 dark:text-gray-200">{{ $dados->count() }}</strong>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left">
                <thead class="sticky top-0 z-10 bg-gradient-to-r from-white/50 to-white/30 dark:from-gray-900/40 dark:to-gray-900/30">
                    <tr class="sticky">
                        <th class="px-6 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 tracking-wide text-left">Categoria</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 tracking-wide">Vencimento</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 tracking-wide text-right">Valor</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 tracking-wide text-center">Status</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200/40 dark:divide-gray-800">
                    @forelse ($dados as $d)
                        <tr class="group transition-colors duration-200 hover:bg-[linear-gradient(90deg,rgba(255,122,24,0.04),transparent)] dark:hover:bg-[linear-gradient(90deg,rgba(255,255,255,0.02),transparent)]">
                            <!-- Categoria -->
                            <td class="px-6 py-4 align-middle min-w-0">
                                <div class="flex items-center gap-3">
                                    <span class="w-9 h-9 rounded-lg flex items-center justify-center
                                                 bg-gradient-to-br from-[var(--brand-purple)]/8 to-[var(--brand-from)]/10
                                                 border border-white/6 dark:border-gray-800/60 shrink-0">
                                        <svg class="w-5 h-5 text-[var(--brand-purple)]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                    </span>

                                    <div class="min-w-0">
                                        <div class="text-sm font-medium text-gray-800 dark:text-gray-100 truncate">
                                            {{ $d->Categoria }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                            {{ \Illuminate\Support\Str::limit($d->Categoria, 36) }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Vencimento -->
                            <td class="px-6 py-4 align-middle">
                                <div class="text-sm text-gray-700 dark:text-gray-300">
                                    {{ \Carbon\Carbon::parse($d->vencimento)->format('d/m/Y') }}
                                </div>
                            </td>

                            <!-- Valor -->
                            <td class="px-6 py-4 align-middle text-right">
                                <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    R$ {{ number_format($d->valor, 2, ',', '.') }}
                                </div>
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-4 align-middle text-center">
                                @if ($d->status === 'Pago')
                                    <button class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm font-semibold
                                                   bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-300
                                                   border border-green-100/40 dark:border-green-900/40
                                                   badge-anim shadow-sm hover:shadow-md active:scale-[0.995]"
                                            type="button" aria-pressed="true" title="Pago">
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M20 6L9 17l-5-5"></path>
                                        </svg>
                                        Pago
                                    </button>

                                @elseif ($d->status === 'Pendente')
                                    <button class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm font-semibold
                                                   bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-300
                                                   border border-red-100/40 dark:border-red-900/40
                                                   badge-anim shadow-sm hover:shadow-md active:scale-[0.995]"
                                            type="button" aria-pressed="false" title="Pendente">
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="9" />
                                            <path d="M12 7v6l4 2"></path>
                                        </svg>
                                        Pendente
                                    </button>

                                @else
                                    <button class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm font-semibold
                                                   bg-gray-50 text-gray-700 dark:bg-gray-800/30 dark:text-gray-300
                                                   border border-gray-100/30 dark:border-gray-700/40
                                                   badge-anim shadow-sm hover:shadow-md active:scale-[0.995]"
                                            type="button" aria-pressed="false" title="Sem Lançamento">
                                        Sem Lançamento
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-600 dark:text-gray-400">
                                Nenhuma conta encontrada para este período.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
